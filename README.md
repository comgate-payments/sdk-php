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

1. At first register your account at our side [https://comgate.cz](https://www.comgate.cz).
2. You will get **merchant indentificator** and **secret**.

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

// Production usage
$client = Comgate::defaults()
    ->withMerchant('123456') // get on portal.comgate.cz
    ->withSecret('foobarbaz') // get on portal.comgate.cz
    ->createClient();

// For testing purpose
$client = Comgate::testing()
    ->withMerchant('123456') // get on portal.comgate.cz
    ->withSecret('foobarbaz') // get on portal.comgate.cz
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

    // Redirect the payer to ComGate payment gateway (use proper method of your framework)
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

### Finish order


```php
use Comgate\SDK\Entity\PaymentNotification;
use Comgate\SDK\Entity\Codes\PaymentStatusCode;
use Comgate\SDK\Exception\Runtime\ComgateException;

// Create from $_POST global variable
// $notification = PaymentNotification::createFromGlobals();

// Create from your framework
$data = $framework->getHttpRequest()->getPostData();
$notification = PaymentNotification::createFrom($data);

$transactionId = $notification->getTransactionId();

$payment = Payment::create()
    ->withTransactionId($transactionId);

try {
    $res = $client->getStatus($payment);

    // var_dump($res->getData());
    switch ($res->getStatus()){
        case PaymentStatusCode::PAID:
            $order->setPaid();
            break;
        case PaymentStatusCode::CANCELLED:
            $order->setCancelled();
            break;
        case PaymentStatusCode::AUTHORIZED:
            $order->setAuthorized();
            break;
    }
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

### Debugging

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

## Maintenance

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars.githubusercontent.com/f3l1x">
</a>

If you find a bug, please submit the issue in [Github](https://github.com/comgate-payments/sdk-php/issues) directly.

Thank you for using our ComGate Payments.

## License

Copyright (c) 2021 ComGate Payments. [MIT Licensed](LICENSE).
