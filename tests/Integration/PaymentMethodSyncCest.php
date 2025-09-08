<?php

namespace Integration;

use Codeception\Example;
use Comgate\SDK\Entity\Money;
use DateTime;
use ReflectionClass;
use ReflectionMethod;
use Tests\Support\IntegrationTester;

class PaymentMethodSyncCest
{
	private $skippedMethods = ["createMotoPayment", "getTransport", "setTransport", "simulation"];

	/**
	 * @param IntegrationTester $I
	 * @return void
	 * @group method-sync
	 * @dataProvider getLocalMethodNames
	 */
	public function methodsSyncTest(IntegrationTester $I, Example $example): void
	{
		$properties = $this->getMethodsProperties();
		$methodProperties = $properties[$example['name']];
		$I->assertNotEmpty($methodProperties, "Properties for method {$example['name']} not found. Add them to getMethodsProperties() method.");

		$remoteMethods = $this->getRemoteMethods();
		$I->assertNotEmpty($remoteMethods, "Remote methods not found. Check if https://apidoc.comgate.cz/cs_v1.yaml is available.");

		$currentRemoteMethodParams = [];
		foreach ($remoteMethods as $method) {
			if ($method['url'] === $methodProperties['url']) {
				$currentRemoteMethodParams = $method['params'];
				break;
			}
		}

		$I->assertNotEmpty($currentRemoteMethodParams, "No remote params found for method {$example['name']}. Is it intentional?");

		$namespace = $methodProperties['namespace'] ?? "Comgate\SDK\Entity\Request\\" . $methodProperties['class'];

		$localMethodParams = $this->getLocalMethodParams($namespace, $methodProperties['args'] ?? null);
		$diffRemote = array_diff($currentRemoteMethodParams, $localMethodParams);
		$diffLocal = array_diff($currentRemoteMethodParams, $localMethodParams);
		$I->assertEmpty($diffRemote, "Local implementation is missing some parameters.");
		$I->assertEmpty($diffLocal, "Local implementation has more parameters than remote.");
	}

	/** Vrátí všechny dostupné názvy lokálních metod z Client.php, používá se jako jednotlivé scénáře
	 * @return array
	 */
	private function getLocalMethodNames(): array
	{
		$mirror = new ReflectionClass(\Comgate\SDK\Client::class);

		$methodNamesRaw = array_map(function(ReflectionMethod $item) {
			return ['name' => $item->getName()];
		}, $mirror->getMethods());

		// odfiltrování konstruktoru a přeskočených metod - to jsou metody které např. nejsou přítomné v dokumentaci
		return array_filter($methodNamesRaw, function($item) {
			return $item['name'] !== '__construct' && !in_array($item['name'], $this->skippedMethods);
		});
	}

	/** Vrací všechny dostupné metody z https://apidoc.comgate.cz/cs_v1.yaml, které se nakonec namapují do párů `url:params`
	 * @return array
	 */
	private function getRemoteMethods(): array
	{
		// cURL požadavek
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://apidoc.comgate.cz/cs_v1.yaml');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		$parsed = yaml_parse($result);
		$availableMethods = array_keys($parsed['paths']);

		return array_map(function($method) use ($parsed) {
			// získání postových parametrů pro danou metodu
			$paramsParsed = $parsed['paths'][$method]['post']['requestBody']['content']['application/x-www-form-urlencoded']['schema']['properties'];
			$params = array_keys($paramsParsed);
			// odfiltrování merchant a secret aby kvůli nim nepadaly testy
			$filteredParams = array_filter($params, function($item) {
				return $item !== 'merchant' && $item !== 'secret';
			});
			return [
				'url' => $method,
				'params' => $filteredParams
			];
		}, $availableMethods);
	}

	/** Vrací všechny názvy parametrů nějaké lokální třídy definované pomocí namespace
	 *
	 * @param string $namespace
	 * @param array|null $args
	 * @return array
	 * @throws \ReflectionException
	 */
	private function getLocalMethodParams(string $namespace, ?array $args): array
	{
		$mirror = new ReflectionClass($namespace);

		// zjištění jestli konstruktor potřebuje nějaké parametry
		$constructor = $mirror->getConstructor();
		if ($constructor && ($constructor->getNumberOfRequiredParameters() > 0)) {
			$instance = $mirror->newInstanceArgs($args);
		} else {
			$instance = $mirror->newInstance();
		}

		$properties = [];
		foreach ($mirror->getProperties() as $prop) {
			// je nutné nastavit tento flag aby byly přístupné i private a protected parametry
			$prop->setAccessible(true);
			// v payments jsou parametry v array params, proto se ověřují takto
			if($prop->getName() === 'params'){
				$properties = array_merge($properties, array_keys($prop->getValue($instance)));
			}else{
				$properties[] = $prop->getName();
			}
		}
		return $properties;
	}

	/** Slouží k získání parametrů jednotlivých metod z Client.php.
	 * Po přidání nové metody je potřeba ji zaregistrovat i zde, jelikož se přes ni získávají parametry pro jednotlivé třídy.
	 *
	 * Základní parametry:<br>
	 * `url` - url podle které se v remote metodách rozpozná o kterou se jedná<br>
	 * `class` - třída ve které se parametry budou kontrolovat<br>
	 * `args` - parametry pro vytváření specifické třídy.<br>
	 * `namespace` -  může sloužit v případě, kdy se parametry nezískávají přímo z request třídy.
	 *  @return array
	 */
	private function getMethodsProperties(): array
	{
		return [
			"createPayment" => [
				"url" => "/v1.0/create",
				"class" => "Payment",
				"namespace" => "Comgate\SDK\Entity\Payment",
			],
			"getStatus" => [
				"url" => "/v1.0/status",
				"class" => "PaymentStatusRequest",
				"args" => ["AAAA-BBBB-CCCC"]
			],
			"getMethods" => [
				"url" => "/v1.0/methods",
				"class" => "MethodsRequest",
			],
			"cancelPayment" => [
				"url" => "/v1.0/cancel",
				"class" => "PaymentCancelRequest",
				"args" => ["AAAA-BBBB-CCCC"]
			],
			"capturePreauth" => [
				"url" => "/v1.0/capturePreauth",
				"class" => "PreauthCaptureRequest",
				"args" => ["AAAA-BBBB-CCCC", Money::ofInt(1000)]
			],
			"cancelPreauth" => [
				"url" => "/v1.0/cancelPreauth",
				"class" => "PreauthCancelRequest",
				"args" => ["AAAA-BBBB-CCCC"]
			],
			"refundPayment" => [
				"url" => "/v1.0/refund",
				"class" => "Refund",
				"namespace" => "Comgate\SDK\Entity\Refund",
			],
			"initRecurringPayment" => [
				"url" => "/v1.0/recurring",
				"class" => "Payment",
				"namespace" => "Comgate\SDK\Entity\Payment",
			],
			"transferList" => [
				"url" => "/v1.0/transferList",
				"class" => "TransferListRequest",
				"args" => [new DateTime('2023-02-01')]
			],
			"singleTransfer" => [
				"url" => "/v1.0/singleTransfer",
				"class" => "SingleTransferRequest",
				"args" => [000000]
			],
			"csvSingleTransfer" => [
				"url" => "/v1.0/csvSingleTransfer",
				"class" => "CsvSingleTransferRequest",
				"args" => ["000000", false]
			],
			"aboSingleTransfer" => [
				"url" => "/v1.0/aboSingleTransfer",
				"class" => "AboSingleTransferRequest",
				"args" => ["000000", false, 'type', 'encoding']
			],
			"getAppleDomainAssociation" => [
				"url" => "/v1.0/appleDomainAssociation",
				"class" => "AppleDomainAssociationRequest",
				"args" => ['method']
			],
			"getCsvDownload" => [
				"url" => "/v1.0/csvDownload",
				"class" => "CsvDownloadRequest",
				"args" => ['date', false]
			],
			"getAboDownload" => [
				"url" => "/v1.0/aboDownload",
				"class" => "AboDownloadRequest",
				"args" => ['date', 'type', 'test', 'encoding']
			],
		];
	}
}
