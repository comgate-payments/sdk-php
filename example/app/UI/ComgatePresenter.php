<?php declare(strict_types = 1);

namespace App\UI;

use Comgate\SDK\Client;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentStatus;
use Comgate\SDK\Exception\Runtime\ComgateException;
use Tracy\Debugger;

class ComgatePresenter extends BasePresenter
{

	/** @inject */
	public Client $client;

	/**
	 * This is for creating payments to ComGate. It's GET request.
	 */
	public function actionPay(int $price): void
	{
		$payment = Payment::create()
			->withRedirect()
			->withPrice(Money::ofInt($price))
			->withCurrency(CurrencyCode::CZK)
			->withLabel('Test item')
			->withReferenceId('test001')
			->withEmail('dev@comgate.cz')
			->withMethod('fake');

		try {
			$res = $this->client->createPayment($payment);
			if ($res->isOk()) {
				$this->redirectUrl($res->getData()['redirect']);
			} else {
				$this->flashMessage('Handling comgate response failed. ' . $res->getField('message'), 'error');
				$this->redirect('Home:');
			}
		} catch (ComgateException $e) {
			Debugger::log($e);
			$this->flashMessage('Communication with comgate failed', 'error');
			$this->redirect('Home:');
		}
	}

	/**
	 * This is for creating inline payments in iframe. It's AJAX request.
	 */
	public function actionPayIframe(int $price): void
	{
		$payment = Payment::create()
			->withIframe()
			->withPrice(Money::ofInt($price))
			->withCurrency(CurrencyCode::CZK)
			->withLabel('Test item')
			->withReferenceId('test001')
			->withEmail('dev@comgate.cz')
			->withMethod(PaymentMethodCode::ALL);

		try {
			$res = $this->client->createPayment($payment);

			if ($res->isOk()) {
				$this->payload->status = 'ok';
				$this->payload->url = $res->getData()['redirect'];
				$this->sendPayload();
			} else {
				$this->payload->status = 'error';
				$this->payload->error = 'Handling comgate response failed. ' . $res->getField('message');
				$this->redirect('Home:');
			}
		} catch (ComgateException $e) {
			Debugger::log($e);
			$this->payload->status = 'error';
			$this->payload->error = 'Communication with comgate failed';
			$this->redirect('Home:');
		}
	}

	/**
	 * This is for handling incoming messages from ComGate. It's GET request.
	 * Type of requests: paid, cancelled, pending.
	 */
	public function actionHandle(): void
	{
		$data = $this->getParameters();

		// Validate there is ID in request data
		if (!isset($data['id'])) {
			$this->flashMessage('Missing payment ID', 'error');
			$this->redirect('Home:');
		}

		$status = PaymentStatus::create()
			->withTransactionId($data['id']);

		try {
			$res = $this->client->getStatus($status);
			if ($res->isOk()) {
				$this->template->data = $res->getData();
			} else {
				$this->flashMessage('Handling comgate response failed. ' . $res->getField('message'), 'error');
				$this->redirect('Home:');
			}
		} catch (ComgateException $e) {
			Debugger::log($e);
			$this->flashMessage('Communication with comgate failed', 'error');
			$this->redirect('Home:');
		}
	}

	/**
	 * This is for notification from ComGate. It's POST request.
	 */
	public function actionNotify(): void
	{
		$data = $this->getHttpRequest()->getPost();
		Debugger::log($data);
	}

}
