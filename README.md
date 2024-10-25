<h1 align=center>Comgate / PHP SDK</h1>

<p align=center>
   💰 PHP library for communication with Comgate.
</p>

<p align=center>
  <a href="https://github.com/comgate-payments/sdk-php/actions"><img src="https://badgen.net/github/checks/comgate-payments/sdk-php"></a>
  <a href="https://packagist.org/packages/comgate/sdk"><img src="https://badgen.net/packagist/dm/comgate/sdk"></a>
  <a href="https://packagist.org/packages/comgate/sdk"><img src="https://badgen.net/packagist/v/comgate/sdk"></a>
  <a href="https://packagist.org/packages/comgate/sdk"><img src="https://badgen.net/packagist/php/comgate/sdk"></a>
  <a href="https://github.com/comgate-payments/sdk-php"><img src="https://badgen.net/github/license/comgate-payments/sdk-php"></a>
</p>

## Getting Started

### Installation

To install latest version of `comgate/sdk` use [Composer](https://getcomposer.org).

```bash
composer require comgate/sdk
```
You have to install php-xml extension on your PHP server (e.g.: Ubuntu: apt-get install php-xml).

### Documentation

- https://help.comgate.cz/docs/protocol-api-en (english version)
- https://help.comgate.cz/docs/api-protokol (czech version)

### Registration

1. At first register your account at our side [comgate.cz](https://www.comgate.cz).
2. You will get **merchant indentificator** and **secret**.
3. Allow your eshop server IPv4 address at [portal.comgate.cz](https://portal.comgate.cz).
4. Set PAID, CANCELLED, PENDING and STATUS URL at  [portal.comgate.cz](https://portal.comgate.cz).

## Usage

- [Setup client](#setup-client)
- [Create payment](#create-payment)
- [Check payment status](#finish-the-order-and-show-payment-status-to-returning-payer)
- [Handle notification](#receive-payment-notification-server-to-server)

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
use Comgate\SDK\Entity\Codes\CategoryCode;
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\DeliveryCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;
use Comgate\SDK\Utils\Helpers;
use Comgate\SDK\Entity\Codes\RequestCode;
use Comgate\SDK\Exception\ApiException;

$payment = new Payment();
$payment
    ->setPrice(Money::ofInt(50)) // 50 CZK
    ->setPrice(Money::ofFloat(50.25)) // 50,25 CZK
    ->setPrice(Money::ofCents(5025)) // 50,25 CZK
    // -----
    ->setCurrency(CurrencyCode::CZK)
    ->setLabel('Test item')
    // or ->setParam('label', 'Test item') // you can pass all params like this
    ->setReferenceId('test001')
    ->setEmail('foo@bar.tld')
    ->addMethod(PaymentMethodCode::ALL)
    //->setRedirect()
    //->setIframe()
    ->setTest(false)
    ->setFullName('Jan Novák')
    ->setCategory(CategoryCode::PHYSICAL_GOODS_ONLY)
    ->setDelivery(DeliveryCode::HOME_DELIVERY);


try {
    $createPaymentResponse = $client->createPayment($payment);
    if ($createPaymentResponse->getCode() === RequestCode::OK) {
        // Redirect the payer to Comgate payment gateway (use proper method of your framework)
        Helpers::redirect($createPaymentResponse->getRedirect());
    } else {
        var_dump($createPaymentResponse->getMessage());
    }
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

Example of successfull response for `$client->createPayment`.

```php
$transactionId = $createPaymentResponse->getTransId(); // XXXX-YYYY-ZZZZ
$code = $createPaymentResponse->getCode(); // 0
$message = $createPaymentResponse->getMessage(); // OK
$redirect = $createPaymentResponse->getRedirect(); // https://payments.comgate.cz/client/instructions/index?id=XXXX-YYYY-ZZZZ
```

Example of error response for `$client->createPayment`.

```php
$code = $e->getCode(); // 1109
$message = $e->getMessage(); // Invalid payment method [fake]
```

### Get methods

```php
use Comgate\SDK\Exception\ApiException;

try {
    $methodsResponse = $client->getMethods();
    foreach ($methodsResponse->getMethodsList() as $method) {
        var_dump([
            $method->getId(),
            $method->getName(),
            $method->getDescription(),
            $method->getLogo(),
        ]);
    }
} catch (ApiException $e) {
    var_dump($e->getMessage());
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

$transactionId = $_GET['id']; // XXXX-YYYY-ZZZZ
$refId = $_GET['refId']; // your order number

try {
    $paymentStatusResponse = $client->getStatus($transactionId);

    switch ($paymentStatusResponse->getStatus()){
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
} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

### Receive payment notification (server-to-server)
Example URL: https://your-eshop.tld/notify.php

```php
use Comgate\SDK\Entity\PaymentNotification;
use Comgate\SDK\Entity\Codes\PaymentStatusCode;
use Comgate\SDK\Exception\ApiException;

// Create from $_POST global variable
// $notification = PaymentNotification::createFromGlobals();

// Create from your framework
$data = $framework->getHttpRequest()->getPostData();
$notification = PaymentNotification::createFrom($data);
$transactionId = $notification->getTransactionId();

try {
    // it's important to check the status from API
    $paymentStatusResponse = $client->getStatus($transactionId);

    switch ($paymentStatusResponse->getStatus()){
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

} catch (ApiException $e) {
    var_dump($e->getMessage());
}
```

### Create a refund
```php
use Comgate\SDK\Entity\Refund;
use Comgate\SDK\Exception\ApiException;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Codes\RequestCode;

$refund = new Refund();
$refund->setTransId('XXXX-YYYY-ZZZZ')
    ->setAmount(Money::ofCents(100))
    ->setRefId('11bb22');

try{
    $refundResult = $client->refundPayment($refund);
    if($refundResult->getCode() == RequestCode::OK) {
        // refund created successfully
    }
} catch (ApiException $e){
    var_dump($e->getMessage());
}
```


### Debugging

#### Logging

> We are using [PSR-3](https://www.php-fig.org/psr/psr-3/) for logging.

```php
use Comgate\SDK\Comgate;
use Comgate\SDK\Logging\FileLogger;
use Comgate\SDK\Logging\StdoutLogger;


$client = Comgate::defaults()
    ->setLogger(new FileLogger(__DIR__ . '/comgate.log'))
    ->createClient();
```

Take a look at [our tests](https://github.com/comgate-payments/sdk-php/blob/master/tests/fixtures) to see the logger format.

## Maintenance

If you find a bug, please submit the issue in [Github](https://github.com/comgate-payments/sdk-php/issues) directly.

Thank you for using our Comgate payment.

## License

Copyright (c) 2024 Comgate a.s. [MIT Licensed](LICENSE).
