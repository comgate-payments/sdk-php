<?php declare(strict_types = 1);

use Comgate\SDK\Client;
use Comgate\SDK\Comgate;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentStatus;
use Comgate\SDK\Http\Transport;
use GuzzleHttp\Client as GuzzleClient;

require __DIR__ . '/../../vendor/autoload.php';

function createPayment(): void
{
	$transport = new Transport(
		new GuzzleClient(['base_url' => Comgate::URL]),
		new Comgate('foo', 'bar', true)
	);

	$client = new Client($transport);

	$payment = Payment::create()
		->withPrice(Money::of(50))
		->withCurrency(CurrencyCode::CZK)
		->withLabel('Test item')
		->withReferenceId('test001')
		->withEmail('dev@comgate.cz')
		->withMethod(PaymentMethodCode::ALL);

	$res1 = $client->create($payment);
	assert($res1->isOk() === true);
	// var_dump($res->getData());

	$status = PaymentStatus::create()
		->withTransactionId($res1->getData()['transId']);

	$res2 = $client->status($status);
	assert($res2->isOk() === true);
	// var_dump($res2->getData());
}

(function (): void {
	// createPayment();
})();
