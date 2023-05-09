<?php


namespace Tests\Integration;

use DateTime;
use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;
use Comgate\SDK\Client;
use Comgate\SDK\Comgate;
use Comgate\SDK\Entity\Codes\CountryCode;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\LangCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Codes\PaymentStatusCode;
use Comgate\SDK\Entity\Codes\RequestCode;
use Comgate\SDK\Entity\Codes\TypeCode;
use Comgate\SDK\Entity\Method;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Entity\Request\AboSingleTransferRequest;
use Comgate\SDK\Entity\Request\MethodsRequest;
use Comgate\SDK\Entity\Response\AboSingleTransferResponse;
use Comgate\SDK\Entity\Response\CsvSingleTransferResponse;
use Comgate\SDK\Entity\Response\MethodsResponse;
use Comgate\SDK\Entity\Response\PaymentCancelResponse;
use Comgate\SDK\Entity\Response\PaymentCreateResponse;
use Comgate\SDK\Entity\Response\PaymentStatusResponse;
use Comgate\SDK\Entity\Response\RecurringPaymentResponse;
use Comgate\SDK\Entity\Response\RefundResponse;
use Comgate\SDK\Entity\Response\SingleTransferResponse;
use Comgate\SDK\Entity\Response\TransferListResponse;
use Comgate\SDK\Entity\Transfer;
use Comgate\SDK\Exception\Api\PreauthException;
use Comgate\SDK\Exception\ApiException;
use Tests\Support\IntegrationTester;

class ClientCest
{
	#[Group('methods')]
	public function getMethodsTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		$methodsResponse = $client->getMethods();

		$I->assertInstanceOf(MethodsResponse::class, $methodsResponse);
		$I->assertNotEmpty($methodsResponse->getMethodsList(), 'Methods list should not be empty');
	}

	#[Group('methods')]
	public function getMethodsWithParamsTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		$methodsRequest = new MethodsRequest();
		$methodsRequest->setType(TypeCode::TYPE_JSON)
			->setLang(LangCode::CS)
			->setCurrency(CurrencyCode::CZK)
			->setCountry(CountryCode::CZ);
		$methodsResponse = $client->getMethods($methodsRequest);

		$I->assertInstanceOf(MethodsResponse::class, $methodsResponse);
		$I->assertNotEmpty($methodsResponse->getMethodsList(), 'Methods list should not be empty');

		$foundMethod = false;
		foreach ($methodsResponse->getMethodsList() as $method) {
			if ($method instanceof Method && $method->getId() === PaymentMethodCode::CARD_CARD_CZ_CSOB_2) {
				$I->assertEquals('Platební karta', $method->getName());
				$foundMethod = true;
			}
		}

		$I->assertTrue($foundMethod, 'Method ' . PaymentMethodCode::CARD_CARD_CZ_CSOB_2 . ' not found');
	}

	#[Group('methods')]
	public function getMethodsFailTest(IntegrationTester $I)
	{
		$client = $this->getClient();
		$config = $client->getTransport()->getConfig()->setMerchant(112233);

		$I->expectThrowable(ApiException::class, function () use ($client) {
			$methodsResponse = $client->getMethods();
		});
	}

	#[Group('status')]
	#[Group('payment')]
	#[DataProvider('getStatusScenarios')]
	public function getStatusTest(IntegrationTester $I, Example $statusParams)
	{
		$client = $this->getClient();

		//create a payment
		$payment = $I->createPayment();
		foreach ($statusParams['params'] as $paramKey => $paramValue) {
			$payment->setParam($paramKey, $paramValue);
		}
		$paymentResponse = $client->createPayment($payment);

		// get status
		$statusResponse = $client->getStatus($paymentResponse->getTransId());
		$I->assertEquals(RequestCode::OK, $statusResponse->getCode());
		$I->assertInstanceOf(PaymentStatusResponse::class, $statusResponse);
		$I->assertEquals(PaymentStatusCode::PENDING, $statusResponse->getStatus());
		$I->assertEquals($statusParams['response']['curr'], $statusResponse->getCurrency());
		$I->assertEquals($statusParams['response']['email'], $statusResponse->getEmail());
	}

	protected function getStatusScenarios()
	{
		return [
			[
				'params' => [
					'method' => PaymentMethodCode::CARD_CARD_CZ_CSOB_2,
					'curr' => CurrencyCode::CZK,
					'email' => 'sdk@comgate.cz',
				],
				'response' => [
					'method' => PaymentMethodCode::CARD_CARD_CZ_CSOB_2,
					'curr' => CurrencyCode::CZK,
					'email' => 'sdk@comgate.cz',
				],
			],
			[
				'params' => [
					'method' => PaymentMethodCode::CARD_CARD_CZ_CSOB_2,
					'curr' => CurrencyCode::EUR,
					'email' => 'sdk@comgate.cz',
				],
				'response' => [
					'method' => PaymentMethodCode::CARD_CARD_CZ_CSOB_2,
					'curr' => CurrencyCode::EUR,
					'email' => 'sdk@comgate.cz',
				],
			],
		];
	}


	#[Group('payment')]
	public function createPaymentTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$paymentsCreateResponse = $client->createPayment($payment);

		// assertions
		$I->assertEquals(RequestCode::OK, $paymentsCreateResponse->getCode());
		$I->assertInstanceOf(PaymentCreateResponse::class, $paymentsCreateResponse);
		$I->assertNotEmpty($paymentsCreateResponse->getTransId());
	}

	#[Group('payment')]
	public function cancelPaymentTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$paymentsCreateResponse = $client->createPayment($payment);
		$transId = $paymentsCreateResponse->getTransId();

		// cancel the payment
		$paymentsCancelResponse = $client->cancelPayment($transId);
		$I->assertEquals(RequestCode::OK, $paymentsCancelResponse->getCode());
		$I->assertInstanceOf(PaymentCancelResponse::class, $paymentsCancelResponse);
	}

	#[Group('payment')]
	public function createTestPaymentTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create the test payment
		$payment = $I->createPayment();
		$payment->setMethods([PaymentMethodCode::CARD_CARD_CZ_CSOB_2]);
		$payment->setTest(true);
		$paymentsCreateResponse = $client->createPayment($payment);

		$I->assertEquals(RequestCode::OK, $paymentsCreateResponse->getCode());
		$I->assertInstanceOf(PaymentCreateResponse::class, $paymentsCreateResponse);
		$I->assertNotEmpty($paymentsCreateResponse->getTransId());

		// set PAID
		$simulationResponse = $client->simulation(
			[
				'subject' => 'payment',
				'transId' => $paymentsCreateResponse->getTransId(),
				'task' => PaymentStatusCode::PAID,
			]
		);

		$I->assertEquals(RequestCode::OK, $simulationResponse->getCode());

		// check status
		$statusResponse = $client->getStatus($paymentsCreateResponse->getTransId());
		$I->assertEquals(PaymentStatusCode::PAID, $statusResponse->getStatus());
	}

	#[Group('preauth')]
	public function capturePreauthTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$payment->setPreauth(true)
			->setMethods([PaymentMethodCode::CARD_CARD_CZ_CSOB_2])
			->setTest(true);
		$paymentsCreateResponse = $client->createPayment($payment);
		$transId = $paymentsCreateResponse->getTransId();

		// set AUTHORIZED
		$simulationResponse = $client->simulation(
			[
				'subject' => 'payment',
				'transId' => $paymentsCreateResponse->getTransId(),
				'task' => PaymentStatusCode::AUTHORIZED,
			]
		);
		$I->assertEquals(RequestCode::OK, $simulationResponse->getCode());

		// capture preauth
		$amount = Money::ofCents(100);
		$capturePreauthResponse = $client->capturePreauth($transId, $amount);
		$I->assertEquals(RequestCode::OK, $capturePreauthResponse->getCode());

		// check status PAID
		$statusResponse = $client->getStatus($paymentsCreateResponse->getTransId());
		$I->assertEquals(PaymentStatusCode::PAID, $statusResponse->getStatus());
	}

	#[Group('preauth')]
	public function cancelPreauthTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$payment->setPreauth(true)
			->setMethods([PaymentMethodCode::CARD_CARD_CZ_CSOB_2])
			->setTest(true);
		$paymentsCreateResponse = $client->createPayment($payment);
		$transId = $paymentsCreateResponse->getTransId();

		// set AUTHORIZED
		$simulationResponse = $client->simulation(
			[
				'subject' => 'payment',
				'transId' => $paymentsCreateResponse->getTransId(),
				'task' => PaymentStatusCode::AUTHORIZED,
			]
		);
		$I->assertEquals(RequestCode::OK, $simulationResponse->getCode());

		// cancel preauth
		$cancelPreauthResponse = $client->cancelPreauth($transId);
		$I->assertEquals(RequestCode::OK, $cancelPreauthResponse->getCode());

		// check status CANCELLED
		$statusResponse = $client->getStatus($paymentsCreateResponse->getTransId());
		$I->assertEquals(PaymentStatusCode::CANCELLED, $statusResponse->getStatus());
	}

	#[Group('preauth')]
	public function capturePreauthExceptionTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$payment->setPreauth(true);
		$paymentsCreateResponse = $client->createPayment($payment);
		$transId = $paymentsCreateResponse->getTransId();
		$amount = Money::ofCents(100);

		// try to capture preauth
		$I->expectThrowable(PreauthException::class,
			function () use ($client, $transId, $amount) {
				$capturePreauthResponse = $client->capturePreauth($transId, $amount);
			}
		);
	}

	#[Group('preauth')]
	public function cancelPreauthExceptionTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$payment->setPreauth(true);
		$paymentsCreateResponse = $client->createPayment($payment);
		$transId = $paymentsCreateResponse->getTransId();

		// try to cancel preauth
		$I->expectThrowable(PreauthException::class,
			function () use ($client, $transId) {
				$cancelPreauthResponse = $client->cancelPreauth($transId);
			}
		);
	}

	#[Group('refund')]
	public function refundPaymentTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$paymentsCreateResponse = $client->createPayment($payment);
		$transId = $paymentsCreateResponse->getTransId();

		// create a refund
		$refund = new Refund();
		$refund->setAmount(Money::ofFloat(1.0))
			->setTransId($transId);
		$refundResponse = $client->refundPayment($refund);

		$I->assertEquals(RequestCode::OK, $refundResponse->getCode());
		$I->assertInstanceOf(RefundResponse::class, $refundResponse);
	}

	#[Group('recurring')]
	public function initRecurringPaymentTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		// create a payment
		$payment = $I->createPayment();
		$payment->setInitRecurring(true)
			->setMethods([PaymentMethodCode::CARD_CARD_CZ_CSOB_2])
			->setTest(true);
		$paymentsCreateResponse = $client->createPayment($payment);
		$initTransId = $paymentsCreateResponse->getTransId();
		$I->assertNotEmpty($initTransId);

		// set PAID
		$simulationResponse = $client->simulation(
			[
				'subject' => 'payment',
				'transId' => $paymentsCreateResponse->getTransId(),
				'task' => PaymentStatusCode::PAID,
			]
		);
		$I->assertEquals(RequestCode::OK, $simulationResponse->getCode());

		// create a recurring payment
		$recurringPayment = $I->createPayment();
		$recurringPayment->setTest(true);
		$recurringPayment->setInitRecurringId($initTransId);
		$recurringResponse = $client->initRecurringPayment($recurringPayment);

		$I->assertInstanceOf(RecurringPaymentResponse::class, $recurringResponse);
		$I->assertNotEmpty($recurringResponse->getTransId());
	}

	#[Group('transfer')]
	public function transferListTest(IntegrationTester $I)
	{
		$client = $this->getClient();
		$date = new DateTime('2023-02-01');

		$transferListResponse = $client->transferList($date, true);
		$I->assertInstanceOf(TransferListResponse::class, $transferListResponse);
		$listOfTransfers = $transferListResponse->getTransferList();
		$I->assertCount(1, $listOfTransfers); // the test returns exactly one transfer

		foreach ($listOfTransfers as $singleTransfer) {
			$I->assertInstanceOf(Transfer::class, $singleTransfer);

			// test data
			$I->assertEquals(1, $singleTransfer->getTransferId());
			$I->assertEquals('0/0000', $singleTransfer->getAccountCounterparty());
			$I->assertEquals('1/0000', $singleTransfer->getAccountOutgoing());
			$I->assertEquals('0123456789', $singleTransfer->getVariableSymbol());
		}
	}

	#[Group('transfer')]
	public function singleTransferTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		$transferId = 112233;
		$singleTransferResponse = $client->singleTransfer($transferId, true);
		$I->assertInstanceOf(SingleTransferResponse::class, $singleTransferResponse);
		$I->assertIsArray($singleTransferResponse->getPaymentsList());
		$I->assertNotEmpty($singleTransferResponse->getPaymentsList());
	}

	#[Group('transfer')]
	public function csvSingleTransferTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		$transferId = 112233;
		$singleTransferResponse = $client->csvSingleTransfer($transferId, true);
		$I->assertInstanceOf(CsvSingleTransferResponse::class, $singleTransferResponse);

		$systemTempDir = sys_get_temp_dir();
		$singleTransferResponse->saveToFile($systemTempDir, $singleTransferResponse->getFilename());
		$I->assertFileExists($systemTempDir . DIRECTORY_SEPARATOR . $singleTransferResponse->getFilename());
	}

	#[Group('transfer')]
	public function csvSingleTransferErrotTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		$transferId = 112233;

		$I->expectThrowable(ApiException::class, function () use ($client, $transferId) {
			$client->csvSingleTransfer($transferId, false);
		});
	}

	#[Group('transfer')]
	#[DataProvider('getAboSingleTransferParams')]
	public function aboSingleTransferTest(IntegrationTester $I, Example $example)
	{
		$client = $this->getClient();

		$transferId = 112233;
		$singleTransferResponse = $client->aboSingleTransfer(
			$transferId,
			true,
			$example['type'],
			$example['encoding']
		);
		$I->assertInstanceOf(AboSingleTransferResponse::class, $singleTransferResponse);

		$systemTempDir = sys_get_temp_dir();
		$fullFilePath = $systemTempDir . DIRECTORY_SEPARATOR . $singleTransferResponse->getFilename();
		$I->assertFileDoesNotExist($fullFilePath);

		$singleTransferResponse->saveToFile($systemTempDir, $singleTransferResponse->getFilename());
		$I->assertFileExists($systemTempDir . DIRECTORY_SEPARATOR . $singleTransferResponse->getFilename());

		unlink($fullFilePath);
		$I->assertFileDoesNotExist($fullFilePath);
	}

	protected function getAboSingleTransferParams()
	{
		return [
			'v1 utf8' => [
				'type' => AboSingleTransferRequest::ABO_TYPE_V1,
				'encoding' => AboSingleTransferRequest::ABO_ENCODING_UTF8,
			],
			'v2 utf8' => [
				'type' => AboSingleTransferRequest::ABO_TYPE_V2,
				'encoding' => AboSingleTransferRequest::ABO_ENCODING_UTF8,
			],
			'v1 windows' => [
				'type' => AboSingleTransferRequest::ABO_TYPE_V1,
				'encoding' => AboSingleTransferRequest::ABO_ENCODING_WINDOWS,
			],
			'v2 windows' => [
				'type' => AboSingleTransferRequest::ABO_TYPE_V2,
				'encoding' => AboSingleTransferRequest::ABO_ENCODING_WINDOWS,
			],
		];
	}

	#[Group('transfer')]
	public function aboSingleTransferErrotTest(IntegrationTester $I)
	{
		$client = $this->getClient();

		$transferId = 112233;

		$I->expectThrowable(ApiException::class, function () use ($client, $transferId) {
			$client->aboSingleTransfer(
				$transferId,
				false,
				AboSingleTransferRequest::ABO_TYPE_V1,
				AboSingleTransferRequest::ABO_ENCODING_WINDOWS
			);
		});
	}

	/**
	 * @return \Comgate\SDK\Client
	 */
	private function getClient(): Client
	{
		$client = Comgate::defaults()
			->setMerchant($_ENV['API_MERCHANT'])
			->setSecret($_ENV['API_SECRET'])
			->setUrl($_ENV['API_URL'])
			->createClient();
		return $client;
	}
}
