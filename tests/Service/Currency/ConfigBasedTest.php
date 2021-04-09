<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Service\Currency;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Contract\Entity\Currency as AbstractCurrency;
use PavelOmelchuk\CommissionTask\Service\Currency\ConfigBased as ConfigBasedCurrencyService;
use PavelOmelchuk\CommissionTask\Contract\Service\Currency as CurrencyServiceContract;

class ConfigBasedTest extends TestCase
{
    /** @var CurrencyServiceContract */
    protected $currency;

    protected function setUp()
    {
        $this->currency = ConfigBasedCurrencyService::getInstance();

        $this->setConversionRates();
    }

    /** @dataProvider dataProviderForRoundTest */
    public function testRound(string $amount, string $currency, $expectation)
    {
        $this->assertTrue($expectation === $this->currency->round($amount, $currency));
    }

    /** @dataProvider dataProviderForConvertTest */
    public function testConvert(string $amount, string $fromCurrency, string $toCurrency, string $expectation)
    {
        $this->assertEquals($expectation, $this->currency->convert($amount, $fromCurrency, $toCurrency));
    }

    public function dataProviderForRoundTest(): array
    {
        return [
            'round integer eur' => ['100.00', AbstractCurrency::CODE_EUR, '100.00'],
            'round integer usd' => ['100.00', AbstractCurrency::CODE_USD, '100.00'],
            'round integer jpy' => ['100.00', AbstractCurrency::CODE_JPY, '100'],
            'round float eur' => ['100.233', AbstractCurrency::CODE_EUR, '100.24'],
            'round float eur with zero suffix' => ['100.240', AbstractCurrency::CODE_EUR, '100.24'],
            'round float usd' => ['100.555', AbstractCurrency::CODE_USD, '100.56'],
            'round float usd with zero suffix' => ['100.240', AbstractCurrency::CODE_USD, '100.24'],
            'round float jpy' => ['100.0001', AbstractCurrency::CODE_JPY, '101'],
            'round float jpy with zero suffix' => ['100.00', AbstractCurrency::CODE_JPY, '100'],
        ];
    }

    public function dataProviderForConvertTest(): array
    {
        return [
            'convert integer eur to usd' => ['100', AbstractCurrency::CODE_EUR, AbstractCurrency::CODE_USD, '200'],
            'convert float eur to usd' => ['15.50', AbstractCurrency::CODE_EUR, AbstractCurrency::CODE_USD, '31'],
            'convert integer eur to jpy' => ['10', AbstractCurrency::CODE_EUR, AbstractCurrency::CODE_JPY, '1000'],
            'convert float eur to jpy' => ['15.50', AbstractCurrency::CODE_EUR, AbstractCurrency::CODE_JPY, '1550'],
            'convert integer usd to eur' => ['10', AbstractCurrency::CODE_USD, AbstractCurrency::CODE_EUR, '5'],
            'convert float usd to eur' => ['10.50', AbstractCurrency::CODE_USD, AbstractCurrency::CODE_EUR, '5.25'],
            'convert integer jpy to eur' => ['2000', AbstractCurrency::CODE_JPY, AbstractCurrency::CODE_EUR, '20'],
            'convert float jpy to eur' => ['100.50', AbstractCurrency::CODE_JPY, AbstractCurrency::CODE_EUR, '1.005'],
        ];
    }

    protected function setConversionRates()
    {
        $conversionRates = $this->currency->getConversionRates();

        $conversionRates[AbstractCurrency::CODE_EUR][AbstractCurrency::CODE_USD] = '2';
        $conversionRates[AbstractCurrency::CODE_USD][AbstractCurrency::CODE_EUR] = '0.5';
        $conversionRates[AbstractCurrency::CODE_EUR][AbstractCurrency::CODE_JPY] = '100';
        $conversionRates[AbstractCurrency::CODE_JPY][AbstractCurrency::CODE_EUR] = '0.01';

        $this->currency->setConversionRates($conversionRates);
    }
}