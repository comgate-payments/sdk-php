<?php declare(strict_types = 1);

/**
 * Fake Comgate API for the fixture-backed integration tests.
 *
 * WHY THIS EXISTS
 * ---------------
 * The live Comgate test account can only produce a subset of responses: a real
 * merchant has no CloudPOS terminal, cannot complete a MOTO card payment, and
 * never returns the Apple-domain / crypto-key / error payloads on demand. That
 * leaves large parts of the SDK's response-parsing code (Terminal*Response,
 * MotoPaymentCreateResponse, the transport error/logging branches, ...) unreachable
 * from live tests.
 *
 * This script is a tiny HTTP server, booted by IntegrationApi via `php -S`, that
 * returns canned Comgate-shaped responses so those code paths can be exercised and
 * covered. It is a test fixture only - nothing in src/ depends on it.
 *
 * HOW REQUESTS ARE ADDRESSED
 * --------------------------
 * IntegrationApi points the SDK at  http://127.0.0.1:<port>/<mode>  so every request
 * arrives as  /<mode>/<resource>  , e.g.  /success/payment.json  or  /missing/payment.json .
 *   - <mode>     first path segment - selects a behaviour (see MODES below).
 *                Defaults to "success" for normal happy-path requests.
 *   - <resource> the rest of the path - the Comgate endpoint being called,
 *                e.g. "payment.json" or "payment/transId/PAY-123.json".
 *
 * MODES
 *   success        normal response for the requested resource (the default)
 *   helper-redirect / query-parse   run an SDK unit in-process to capture its coverage
 *   http-400 / http-500             respond with that HTTP status
 *   body-only                       strip all response headers
 *   malformed                       return invalid JSON
 *   api-error / missing / preauth-error   return a Comgate error envelope
 *   no-key                          pubCryptoKey.json returns a non-JWK key
 */

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Comgate\SDK\Http\Query;
use Comgate\SDK\Utils\Helpers;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;

/** Send a JSON body and stop. */
function send(array $data): void
{
	echo json_encode($data);
}

/** Send a Comgate error envelope and stop. */
function send_error(int $code, string $message): void
{
	send(['code' => $code, 'message' => $message]);
}

/**
 * Run a single SDK source file under code coverage while $callback executes,
 * then serialise the result to the file IntegrationApi will merge back in.
 * Used by the helper-redirect / query-parse modes below.
 */
function capture_coverage(string $sourceFile, callable $callback): void
{
	$filter = new Filter();
	$filter->includeFile($sourceFile);
	$coverage = new CodeCoverage((new Selector())->forLineCoverage($filter), $filter);
	$coverage->start('fixture');
	register_shutdown_function(static function () use ($coverage): void {
		$coverage->stop();
		file_put_contents((string) getenv('COMGATE_FIXTURE_COVERAGE_FILE'), serialize($coverage));
	});
	$callback();
}

// --- Parse the incoming request -------------------------------------------------

$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$mode = array_shift($segments) ?: 'success';
$resource = implode('/', $segments);
$method = $_SERVER['REQUEST_METHOD'];
$body = file_get_contents('php://input');
$headers = function_exists('getallheaders') ? getallheaders() : [];
$src = dirname(__DIR__, 3) . '/src';

// --- Coverage-only modes --------------------------------------------------------
// These invoke an SDK unit directly so its coverage can be captured; they are not
// real Comgate endpoints, so they return early before the request is logged.

if ($mode === 'helper-redirect') {
	capture_coverage($src . '/Utils/Helpers.php', static function (): void {
		Helpers::redirect($_GET['target'] ?? '/');
	});
	echo 'redirected';
	return;
}

if ($mode === 'query-parse') {
	capture_coverage($src . '/Http/Query.php', static function (): void {
		header('Content-Type: application/json');
		echo json_encode(Query::parse($_SERVER['QUERY_STRING'] ?? ''));
	});
	return;
}

// --- Record the request so tests can assert what the SDK sent -------------------

file_put_contents((string) getenv('COMGATE_FIXTURE_REQUEST_LOG'), json_encode([
	'mode' => $mode,
	'method' => $method,
	'path' => '/' . $resource,
	'query' => $_GET,
	'headers' => $headers,
	'body' => $body,
], JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);

// Default headers every fixture response carries (X-Repeated appears twice on
// purpose, to exercise repeated-header handling in the transport).
header('X-Fixture: Comgate SDK');
header('X-Repeated: first', false);
header('X-Repeated: second', false);

// --- Transport-level behaviour modes --------------------------------------------

if ($mode === 'http-500') {
	http_response_code(500);
}
if ($mode === 'http-400') {
	http_response_code(400);
}
if ($mode === 'body-only') {
	header_remove();
}
if ($mode === 'malformed') {
	echo '{broken-json';
	return;
}
if ($mode === 'api-error') {
	send_error(1500, 'Fixture API error');
	return;
}
if ($mode === 'missing') {
	send_error(1400, 'Fixture missing parameter');
	return;
}
if ($mode === 'preauth-error') {
	send_error(1401, 'Fixture preauth error');
	return;
}

// --- Payments -------------------------------------------------------------------

if ($resource === 'payment.json') {
	send(['code' => 0, 'message' => 'OK', 'transId' => 'PAY-123', 'redirect' => 'https://pay.test/PAY-123']);
	return;
}
if (strpos($resource, 'payment/transId/') === 0 && $method === 'GET') {
	send([
		'code' => 0, 'message' => 'OK', 'merchant' => 'fixture-merchant', 'secret' => 'fixture-secret',
		'transId' => 'PAY-123', 'test' => 'true', 'price' => 12345, 'curr' => 'CZK', 'label' => 'Fixture order',
		'refId' => 'ORDER-42', 'payerId' => 'PAYER-7', 'method' => 'CARD_CZ_CSOB_2', 'account' => '123/0100',
		'email' => 'buyer@example.test', 'name' => 'Ada Buyer', 'phone' => '+420123456789', 'status' => 'PAID',
		'payerName' => 'Ada Buyer', 'payerAcc' => '987/0300', 'fee' => '12', 'vs' => '42', 'cardValid' => '12/30',
		'cardNumber' => '************1111', 'appliedFee' => '3', 'appliedFeeTyp' => 'fixed', 'paymentErrorReason' => '',
	]);
	return;
}
if (
	strpos($resource, 'payment/transId/') === 0    // cancel payment (DELETE)
	|| strpos($resource, 'preauth/transId/') === 0  // capture / cancel preauth
	|| $resource === 'refund.json'
	|| $resource === 'simulation.json'
) {
	send(['code' => 0, 'message' => 'OK']);
	return;
}
if ($resource === 'recurring.json') {
	send(['code' => 0, 'message' => 'OK', 'transId' => 'REC-123']);
	return;
}

// --- Transfers & downloads ------------------------------------------------------

if (strpos($resource, 'transferList/date/') === 0) {
	send([
		['transferId' => 1, 'transferDate' => '2026-07-20', 'accountCounterparty' => '0/0000', 'accountOutgoing' => '1/0000', 'variableSymbol' => '42'],
		['transferId' => 2, 'transferDate' => '2026-07-21', 'accountCounterparty' => '2/0100', 'accountOutgoing' => '3/0300', 'variableSymbol' => '43'],
	]);
	return;
}
if (strpos($resource, 'singleTransfer/transferId/') === 0) {
	send([['transId' => 'PAY-1', 'price' => 100], ['transId' => 'PAY-2', 'price' => 200]]);
	return;
}
if (strpos($resource, 'csvSingleTransfer/transferId/') === 0) {
	send(['nazev' => 'transfer.csv', 'csv' => base64_encode("id,amount\n1,100\n")]);
	return;
}
if (strpos($resource, 'aboSingleTransfer/transferId/') === 0) {
	send(['nazev' => 'transfer.abo', 'abo' => base64_encode('UHL1 0100 100')]);
	return;
}
if (strpos($resource, 'csvDownload/date/') === 0) {
	send(['nazev' => 'daily.csv', 'csv' => base64_encode("date,amount\n2026-07-21,100\n")]);
	return;
}
if (strpos($resource, 'aboDownload/date/') === 0) {
	send(['nazev' => 'daily.abo', 'abo' => base64_encode('UHL1 DAILY')]);
	return;
}
if (strpos($resource, 'appleDomainAssociation.json') === 0) {
	send(['fileContent' => 'apple-domain-association']);
	return;
}

// --- Terminal / CloudPOS --------------------------------------------------------

if ($resource === 'terminalPayment.json') {
	send(['code' => 0, 'message' => 'OK', 'transId' => 'TERM-PAY-1']);
	return;
}
if (strpos($resource, 'terminalPayment/transId/') === 0 && $method === 'GET') {
	send(['code' => 0, 'message' => 'OK', 'price' => 1250, 'curr' => 'CZK', 'refId' => 'TP-1', 'transId' => 'TERM-PAY-1', 'status' => 'PAID', 'fee' => '10', 'cardValid' => '12/30', 'cardNumber' => '****1111', 'paymentErrorReason' => '', 'reversed' => false, 'amountRefunded' => '0']);
	return;
}
if (strpos($resource, 'terminalPayment/transId/') === 0) {
	send(['code' => 0, 'message' => 'OK']);
	return;
}
if ($resource === 'terminalRefund.json') {
	send(['code' => 0, 'message' => 'OK', 'transId' => 'TERM-REF-1']);
	return;
}
if (strpos($resource, 'terminalRefund/transId/') === 0 && $method === 'GET') {
	send(['code' => 0, 'message' => 'OK', 'price' => 500, 'curr' => 'CZK', 'refId' => 'TR-1', 'transId' => 'TERM-REF-1', 'status' => 'REFUNDED', 'cardNumber' => '****1111', 'reversed' => true]);
	return;
}
if (strpos($resource, 'terminalRefund/transId/') === 0) {
	send(['code' => 0, 'message' => 'OK']);
	return;
}
if ($resource === 'terminalClosing.json') {
	send(['code' => 0, 'message' => 'OK', 'batchNumber' => 12, 'batchData' => [['count' => 2, 'amount' => 1750]]]);
	return;
}
if ($resource === 'terminal.json') {
	send(['status' => 'ONLINE']);
	return;
}

// --- Crypto key & MOTO ----------------------------------------------------------

if ($resource === 'pubCryptoKey.json') {
	if ($mode === 'no-key') {
		send(['code' => 0, 'message' => 'OK', 'key' => base64_encode(json_encode(['notJwk' => []]))]);
		return;
	}
	$publicKey = base64_decode((string) getenv('COMGATE_FIXTURE_PUBLIC_JWK'), true);
	send(['code' => 0, 'message' => 'OK', 'key' => base64_encode(json_encode(['jwk' => json_decode($publicKey, true)]))]);
	return;
}
if ($resource === 'moto.json') {
	send(['code' => 0, 'message' => 'OK', 'transId' => 'MOTO-1', 'status' => 'PAID']);
	return;
}

// --- Unknown resource -----------------------------------------------------------

send_error(1500, 'Unknown fixture route: ' . $resource);
