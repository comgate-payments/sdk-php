<?php declare(strict_types = 1);

require dirname(__DIR__, 3) . '/vendor/autoload.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim((string) $path, '/'));
$mode = array_shift($segments) ?: 'success';
$resource = implode('/', $segments);
$body = file_get_contents('php://input');
$headers = function_exists('getallheaders') ? getallheaders() : [];

if ($mode === 'helper-redirect') {
	$filter = new \SebastianBergmann\CodeCoverage\Filter();
	$filter->includeFile(dirname(__DIR__, 3) . '/src/Utils/Helpers.php');
	$driver = (new \SebastianBergmann\CodeCoverage\Driver\Selector())->forLineCoverage($filter);
	$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage($driver, $filter);
	$coverage->start('Helpers::redirect');
	register_shutdown_function(static function () use ($coverage): void {
		$coverage->stop();
		file_put_contents((string) getenv('COMGATE_FIXTURE_COVERAGE_FILE'), serialize($coverage));
	});
	\Comgate\SDK\Utils\Helpers::redirect($_GET['target'] ?? '/');
	echo 'redirected';
	return;
}

if ($mode === 'query-parse') {
	$filter = new \SebastianBergmann\CodeCoverage\Filter();
	$filter->includeFile(dirname(__DIR__, 3) . '/src/Http/Query.php');
	$driver = (new \SebastianBergmann\CodeCoverage\Driver\Selector())->forLineCoverage($filter);
	$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage($driver, $filter);
	$coverage->start('Query::parse');
	register_shutdown_function(static function () use ($coverage): void {
		$coverage->stop();
		file_put_contents((string) getenv('COMGATE_FIXTURE_COVERAGE_FILE'), serialize($coverage));
	});
	header('Content-Type: application/json');
	echo json_encode(\Comgate\SDK\Http\Query::parse($_SERVER['QUERY_STRING'] ?? ''));
	return;
}

file_put_contents((string) getenv('COMGATE_FIXTURE_REQUEST_LOG'), json_encode([
	'mode' => $mode,
	'method' => $_SERVER['REQUEST_METHOD'],
	'path' => '/' . $resource,
	'query' => $_GET,
	'headers' => $headers,
	'body' => $body,
], JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);

header('X-Fixture: Comgate SDK');
header('X-Repeated: first', false);
header('X-Repeated: second', false);

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

$error = static function (int $code, string $message): void {
	echo json_encode(['code' => $code, 'message' => $message]);
};
if ($mode === 'api-error') {
	$error(1500, 'Fixture API error');
	return;
}
if ($mode === 'missing') {
	$error(1400, 'Fixture missing parameter');
	return;
}
if ($mode === 'preauth-error') {
	$error(1401, 'Fixture preauth error');
	return;
}

if ($resource === 'payment.json') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'PAY-123', 'redirect' => 'https://pay.test/PAY-123']);
	return;
}
if (strpos($resource, 'payment/transId/') === 0 && $_SERVER['REQUEST_METHOD'] === 'GET') {
	echo json_encode([
		'code' => 0, 'message' => 'OK', 'merchant' => 'fixture-merchant', 'secret' => 'fixture-secret',
		'transId' => 'PAY-123', 'test' => 'true', 'price' => 12345, 'curr' => 'CZK', 'label' => 'Fixture order',
		'refId' => 'ORDER-42', 'payerId' => 'PAYER-7', 'method' => 'CARD_CZ_CSOB_2', 'account' => '123/0100',
		'email' => 'buyer@example.test', 'name' => 'Ada Buyer', 'phone' => '+420123456789', 'status' => 'PAID',
		'payerName' => 'Ada Buyer', 'payerAcc' => '987/0300', 'fee' => '12', 'vs' => '42', 'cardValid' => '12/30',
		'cardNumber' => '************1111', 'appliedFee' => '3', 'appliedFeeTyp' => 'fixed', 'paymentErrorReason' => '',
	]);
	return;
}
if (strpos($resource, 'payment/transId/') === 0 || strpos($resource, 'preauth/transId/') === 0 || $resource === 'refund.json' || $resource === 'simulation.json') {
	echo json_encode(['code' => 0, 'message' => 'OK']);
	return;
}
if ($resource === 'recurring.json') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'REC-123']);
	return;
}
if (strpos($resource, 'transferList/date/') === 0) {
	echo json_encode([
		['transferId' => 1, 'transferDate' => '2026-07-20', 'accountCounterparty' => '0/0000', 'accountOutgoing' => '1/0000', 'variableSymbol' => '42'],
		['transferId' => 2, 'transferDate' => '2026-07-21', 'accountCounterparty' => '2/0100', 'accountOutgoing' => '3/0300', 'variableSymbol' => '43'],
	]);
	return;
}
if (strpos($resource, 'singleTransfer/transferId/') === 0) {
	echo json_encode([['transId' => 'PAY-1', 'price' => 100], ['transId' => 'PAY-2', 'price' => 200]]);
	return;
}
if (strpos($resource, 'csvSingleTransfer/transferId/') === 0) {
	echo json_encode(['nazev' => 'transfer.csv', 'csv' => base64_encode("id,amount\n1,100\n")]);
	return;
}
if (strpos($resource, 'aboSingleTransfer/transferId/') === 0) {
	echo json_encode(['nazev' => 'transfer.abo', 'abo' => base64_encode('UHL1 0100 100')]);
	return;
}
if (strpos($resource, 'csvDownload/date/') === 0) {
	echo json_encode(['nazev' => 'daily.csv', 'csv' => base64_encode("date,amount\n2026-07-21,100\n")]);
	return;
}
if (strpos($resource, 'aboDownload/date/') === 0) {
	echo json_encode(['nazev' => 'daily.abo', 'abo' => base64_encode('UHL1 DAILY')]);
	return;
}
if (strpos($resource, 'appleDomainAssociation.json') === 0) {
	echo json_encode(['fileContent' => 'apple-domain-association']);
	return;
}

if ($resource === 'terminalPayment.json') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'TERM-PAY-1']);
	return;
}
if (strpos($resource, 'terminalPayment/transId/') === 0 && $_SERVER['REQUEST_METHOD'] === 'GET') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'price' => 1250, 'curr' => 'CZK', 'refId' => 'TP-1', 'transId' => 'TERM-PAY-1', 'status' => 'PAID', 'fee' => '10', 'cardValid' => '12/30', 'cardNumber' => '****1111', 'paymentErrorReason' => '', 'reversed' => false, 'amountRefunded' => '0']);
	return;
}
if (strpos($resource, 'terminalPayment/transId/') === 0) {
	echo json_encode(['code' => 0, 'message' => 'OK']);
	return;
}
if ($resource === 'terminalRefund.json') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'TERM-REF-1']);
	return;
}
if (strpos($resource, 'terminalRefund/transId/') === 0 && $_SERVER['REQUEST_METHOD'] === 'GET') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'price' => 500, 'curr' => 'CZK', 'refId' => 'TR-1', 'transId' => 'TERM-REF-1', 'status' => 'REFUNDED', 'cardNumber' => '****1111', 'reversed' => true]);
	return;
}
if (strpos($resource, 'terminalRefund/transId/') === 0) {
	echo json_encode(['code' => 0, 'message' => 'OK']);
	return;
}
if ($resource === 'terminalClosing.json') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'batchNumber' => 12, 'batchData' => [['count' => 2, 'amount' => 1750]]]);
	return;
}
if ($resource === 'terminal.json') {
	echo json_encode(['status' => 'ONLINE']);
	return;
}

if ($resource === 'pubCryptoKey.json') {
	if ($mode === 'no-key') {
		echo json_encode(['code' => 0, 'message' => 'OK', 'key' => base64_encode(json_encode(['notJwk' => []]))]);
		return;
	}
	$publicKey = base64_decode((string) getenv('COMGATE_FIXTURE_PUBLIC_JWK'), true);
	echo json_encode(['code' => 0, 'message' => 'OK', 'key' => base64_encode(json_encode(['jwk' => json_decode($publicKey, true)]))]);
	return;
}
if ($resource === 'moto.json') {
	echo json_encode(['code' => 0, 'message' => 'OK', 'transId' => 'MOTO-1', 'status' => 'PAID']);
	return;
}

echo json_encode(['code' => 1500, 'message' => 'Unknown fixture route: ' . $resource]);
