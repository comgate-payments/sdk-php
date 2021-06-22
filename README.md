<h1 align=center>ComGate Payments / PHP SDK</h1>

<p align=center>
   💰 PHP library for communication with ComGate Payments.
</p>

<p align=center>
  <a href="https://github.com/comgate-payments/sdk-php/actions"><img src="https://badgen.net/github/checks/comgate-payments/sdk-php"></a>
  <a href="https://coveralls.io/r/comgate-payments/sdk-php"><img src="https://badgen.net/coveralls/c/github/comgate-payments/sdk-php"></a>
  <a href="https://packagist.org/packages/comgate-payments/sdk-php"><img src="https://badgen.net/packagist/dm/comgate-payments/sdk-php"></a>
  <a href="https://packagist.org/packages/comgate-payments/sdk-php"><img src="https://badgen.net/packagist/v/comgate-payments/sdk-php"></a>
  <a href="https://packagist.org/packages/comgate-payments/sdk-php"><img src="https://badgen.net/packagist/php/comgate-payments/sdk-php"></a>
  <a href="https://github.com/comgate-payments/sdk-php"><img src="https://badgen.net/github/license/comgate-payments/sdk-php"></a>
</p>

## Getting Started

### Installation

To install latest version of `comgate/sdk` use [Composer](https://getcomposer.com).

```bash
composer require comgate/sdk
```

### Registration

1. At first register your account at our side [https://comgate.cz](comgate.cz).
2. You will get **merchant indentificator** and **secret**.

### Demo

There is a prepared playground project, where you can test (or play) with ComGate Payments.

https://github.com/comgate-payments/playground

## Usage

- [Setup client](#setup-client)
- [Create payment](#create-payment)
- [Check payment status](#check-payment-status)
- [Handle notification](#handle-notification)

### Setup client

```php
use Comgate\SDK\Comgate;

// Production usage
$client = Comgate::defaults()
  ->withMerchant('123456') // get on portal.comgate.cz
  ->withSecret('foobarbaz') // get on portal.comgate.cz
  ->create();

// For testing purpose
$client = Comgate::testing()
  ->withMerchant('123456') // get on portal.comgate.cz
  ->withSecret('foobarbaz') // get on portal.comgate.cz
  ->create();
```

### Create payment

```php
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Exception\Runtime\ComgateException;
use Comgate\SDK\Utils\Helpers;

$payment = Payment::create()
    ->withRedirect()
    //->withIframe()
    // Price
    ->withPrice(Money::ofInt(50)) // 50 CZK
    ->withPrice(Money::ofFloat(50.25)) // 50,25 CZK
    ->withPrice(Money::ofCents(5025)) // 50,25 CZK
    // -----
    ->withCurrency(CurrencyCode::CZK)
    ->withLabel('Test item')
    ->withReferenceId('test001')
    ->withEmail('foo@bar.tld')
    ->withMethod(PaymentMethodCode::ALL);

try {
    $res = $client->createPayment($payment);

    assert($res->isOk() === true);
    var_dump($res->getData());

    // Redirect to ComGate (use proper method of your framework)
    Helpers::redirect($res->getField('redirect'));
} catch (ComgateException $e) {
    var_dump($e->getPrevious()->getMessage());
}
```

Example of success response for `$client->createPayment`.

```php
$data = $res->getData();
$data = [
  'code' => '0',
  'message' => 'OK',
  'transId' => 'XXXX-YYYY-ZZZZ',
  'redirect' => 'https://payments.comgate.cz/client/instructions/index?id=XXXX-YYYY-ZZZZ',
];
```

Example of error response for `$client->createPayment`.

```php
$data = $res->getData();
$data = [
    'code' => '1109',
    'message' => 'Invalid payment method [fake]',
];
```

### Check payment status

```php
use Comgate\SDK\Entity\PaymentStatus;
use Comgate\SDK\Exception\Runtime\ComgateException;

$status = PaymentStatus::create()
    ->withTransactionId('123456ABCDEFG');

try {
    $res = $client->getStatus($status);

    assert($res->isOk() === true);
    var_dump($res->getData());
} catch (ComgateException $e) {
    var_dump($e->getPrevious()->getMessage());
}
```

Example of success response for `$client->getStatus`.

```php
$data = $res->getData();
$data = [
  'code' => '0',
  'message' => 'OK',
  'merchant' => '123456',
  'test' => 'true',
  'price' => '500',
  'curr' => 'CZK',
  'label' => 'Test item',
  'refId' => 'test001',
  'method' => 'CARD_CZ_BS',
  'email' => 'dev@comgate.cz',
  'name' => '',
  'transId' => 'XXXX-YYYY-ZZZZ',
  'secret' => 'foobarbaz',
  'status' => 'PAID',
  'fee' => 'unknown',
  'vs' => '123456789',
  'payer_acc' => '',
  'payerAcc' => '',
  'payer_name' => '',
  'payerName' => '',
];
```

Example of error response for `$client->getStatus`.

```php
$data = $res->getData();
$data = [
    'code' => '1400',
    'message' => 'Payment not found'
];
```

### Handle notification

Notification is being sent from ComGate servers as POST request.

```php
use Comgate\SDK\Entity\PaymentNotification;

// Create from $_POST global variable
$notification = PaymentNotification::createFromGlobals();

// Create from your framework
$data = $framework->getHttpRequest()->getPostData();
$notification = PaymentNotification::createFrom($data);
```

## Maintenance

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars.githubusercontent.com/f3l1x">
</a>

If you find a bug, please submit the issue in [Github](https://github.com/comgate-payments/sdk-php/issues) directly.

Thank you for using our ComGate Payments.

## License

Copyright (c) 2021 ComGate Payments. [MIT Licensed](LICENSE).
