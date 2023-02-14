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

To install latest version of `comgate/sdk` use [Composer](https://getcomposer.org).

```bash
composer require comgate/sdk
```

| State  | Version     | Branch   | PHP     |
|--------|-------------|----------|---------|
| dev    | `^0.2.0`    | `master` | `>=7.2` |
| stable | `^0.1.0`    | `master` | `>=7.2` |


### Documentation

- https://help.comgate.cz/docs/protocol-api-en (english version)
- https://help.comgate.cz/docs/api-protokol (czech version)

### Registration

1. At first register your account at our side [comgate.cz](https://www.comgate.cz).
2. You will get **merchant indentificator** and **secret**.
3. Allow your eshop server IPv4 address at [portal.comgate.cz](https://portal.comgate.cz).
4. Set PAID, CANCELLED, PENDING and STATUS URL at  [portal.comgate.cz](https://portal.comgate.cz).

### Example

There is a prepared [example project](/example), where you can test (or play) with ComGate Payments.

## Usage

- [Setup client](#setup-client)
- [Create payment](#create-payment)
- [Check payment status](#check-payment-status)
- [Handle notification](#handle-notification)

### Setup client

```php
use Comgate\SDK\Comgate;

$client = Comgate::defaults()
    ->setMerchant('123456') // get on portal.comgate.cz
    ->setSecret('foobarbaz') // get on portal.comgate.cz
    ->createClient();

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
    ->setRedirect()
    //->setIframe()
    // Price
    ->setPrice(Money::ofInt(50)) // 50 CZK
    ->setPrice(Money::ofFloat(50.25)) // 50,25 CZK
    ->setPrice(Money::ofCents(5025)) // 50,25 CZK
    // -----
    ->setCurrency(CurrencyCode::CZK)
    ->setTest(false)
    ->setLabel('Test item')
    // or ->setParam('label', 'Test item') // you can pass all params like this
    ->setReferenceId('test001')
    ->setEmail('foo@bar.tld')
    ->setMethod(PaymentMethodCode::ALL);



try {
    $res = $client->createPayment($payment);

    assert($res->isOk() === true);
    var_dump($res->getData());

    // Redirect the payer to ComGate payment gateway (use proper method of your framework)
    Helpers::redirect($res->getField('redirect'));
} catch (ComgateException $e) {
    var_dump($e->getPrevious()->getMessage());
}
```

Example of successfull response for `$client->createPayment`.

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

### Get methods

```php
use Comgate\SDK\Exception\Runtime\ComgateException;

try {
    $methods = $client->getMethods();

    assert($methods->isOk() === true);
    var_dump($methods->getData());
} catch (ComgateException $e) {
    var_dump($e->getPrevious()->getMessage());
}
```


### Finish the order and show payment status to returning payer
 - Example PAID URL: https://your-eshop.tld/order-finish.php?id=${id}&refId=${refId}&status=PAID
 - Example PAID CANCELLED: https://your-eshop.tld/order-finish.php?id=${id}&refId=${refId}&status=CANCELLED
 - Example PAID PENDING: https://your-eshop.tld/order-finish.php?id=${id}&refId=${refId}&status=PENDING

```php
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentNotification;
use Comgate\SDK\Entity\Codes\PaymentStatusCode;
use Comgate\SDK\Exception\Runtime\ComgateException;

$transactionId = $_GET['id']; // XXXX-YYYY-ZZZZ
$refId = $_GET['refId']; // your order number

// Create payment with transaction ID and check status
$payment = Payment::create()
    ->withTransactionId($transactionId);

try {
    $res = $client->getStatus($payment);
    $data = $res->getData();

    switch ($data['status']){
        case PaymentStatusCode::PAID:
            // Your code (set order as paid)
            echo "Your payment was PAID successfully.";
            break;

        case PaymentStatusCode::CANCELLED:
            // Your code (set order as cancelled)
            echo "Your order was CANCELLED.";
            break;

        case PaymentStatusCode::PENDING:
            // Your code (order is still pending)
            echo "We are waiting for the payment.";
            break;

        case PaymentStatusCode::AUTHORIZED:
            // Your code (set order as authorized)
            echo "Payment was authorized successfully.";
            break;
    }

    echo "OK"; // important response with HTTP code 200

} catch (ComgateException $e) {
    var_dump($e->getPrevious()->getMessage());
}
```

### Receive payment notification (server-to-server)
Example URL: https://your-eshop.tld/notify.php

```php
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Entity\PaymentNotification;
use Comgate\SDK\Entity\Codes\PaymentStatusCode;
use Comgate\SDK\Exception\Runtime\ComgateException;

// Create from $_POST global variable
// $notification = PaymentNotification::createFromGlobals();

// Create from your framework
$data = $framework->getHttpRequest()->getPostData();
$notification = PaymentNotification::createFrom($data);

// Create payment with transaction ID and check status
$payment = Payment::create()
    ->withTransactionId($notification->getTransactionId());

try {
    $res = $client->getStatus($payment);
    $data = $res->getData();

    switch ($data['status']){
        case PaymentStatusCode::PAID:
            // Your code (set order as paid)
            break;

        case PaymentStatusCode::CANCELLED:
            // Your code (set order as cancelled)
            break;

        case PaymentStatusCode::AUTHORIZED:
            // Your code (set order as authorized)
            break;

        // PaymentStatusCode::PENDING - is NOT send via push notification
    }

    echo "OK"; // important response with HTTP code 200

} catch (ComgateException $e) {
    var_dump($e->getPrevious()->getMessage());
}
```

Example of successfull response for `$client->getStatus`.

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

### Debugging

#### Logging

> We are using [PSR-3](https://www.php-fig.org/psr/psr-3/) for logging.

```php
use Comgate\SDK\Comgate;
use Comgate\SDK\Logging\FileLogger;
use Comgate\SDK\Logging\StdoutLogger;

$client = Comgate::defaults()
    ->withLogger(new FileLogger(__DIR__ . '/comgate.log'))
    // ->withLogger(new StdoutLogger())
    ->createClient();
```

Take a look at [our tests](https://github.com/comgate-payments/sdk-php/blob/master/tests/fixtures) to see the logger format.

#### Custom middleware

> We are using Guzzle under the hood. Take a look at [documentation](https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html).

```php
use Comgate\SDK\Comgate;
use Psr\Http\Message\RequestInterface;

$client = Comgate::defaults()
    ->withMiddleware(
        function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                // Your code

                $ret = $handler($request, $options);

                // Your code

                return $ret;
            };
        }
    )
    ->createClient();
```

## Maintenance

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars.githubusercontent.com/f3l1x">
</a>

If you find a bug, please submit the issue in [Github](https://github.com/comgate-payments/sdk-php/issues) directly.

Thank you for using our ComGate Payments.

## License

Copyright (c) 2021 ComGate Payments. [MIT Licensed](LICENSE).
