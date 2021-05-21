<h1 align=center>ComGate Payments / PHP SDK</h1>

<p align=center>
   💰 PHP library for communication with ComGate Payments.
</p>

<p align=center>
  <a href="https://github.com/comgate/sdk/actions"><img src="https://badgen.net/github/checks/comgate/sdk"></a>
  <a href="https://coveralls.io/r/comgate/sdk"><img src="https://badgen.net/coveralls/c/github/comgate/sdk"></a>
  <a href="https://packagist.org/packages/comgate/sdk"><img src="https://badgen.net/packagist/dm/comgate/sdk"></a>
  <a href="https://packagist.org/packages/comgate/sdk"><img src="https://badgen.net/packagist/v/comgate/sdk"></a>
  <a href="https://packagist.org/packages/comgate/sdk"><img src="https://badgen.net/packagist/php/comgate/sdk"></a>
  <a href="https://github.com/comgate/sdk"><img src="https://badgen.net/github/license/comgate/sdk"></a>
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

- Comgate client
- Create payment
- Check payment status

### Comgate client

```php
use Comgate\SDK\Bootstrap;

$client = Bootstrap::defaults()
  ->withMerchant('123456') // get on portal.comgate.cz
  ->withSecret('foobarbaz') // get portal.comgate.cz
  ->withTest(true) // set false on production
  ->create();
```

### Create payment

```php
use Comgate\SDK\Entity\Codes\CurrencyCode;
use Comgate\SDK\Entity\Codes\PaymentMethodCode;
use Comgate\SDK\Entity\Money;
use Comgate\SDK\Entity\Payment;

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

$res1 = $client->create($payment);
assert($res1->isOk() === true);
// var_dump($res->getCode());
// var_dump($res->getData());
```

### Check payment status

```php
use Comgate\SDK\Entity\PaymentStatus;

$status = PaymentStatus::create()
    ->withTransactionId('123456ABCDEFG');

$res = $client->status($status);
assert($res->isOk() === true);
// var_dump($res->getData());
```

## Maintenance

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars.githubusercontent.com/f3l1x">
</a>

If you find a bug, please submit the issue in [Github](https://github.com/comgate-payments/sdk-php/issues) directly.

Thank you for using our ComGate Payments.

## License

Copyright (c) 2021 ComGate Payments. [MIT Licensed](LICENSE).
