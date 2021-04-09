<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\DataStructure;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Contract\Entity\Currency as AbstractCurrencyEntity;
use PavelOmelchuk\CommissionTask\Exception\Validation\Amount\InvalidNumber as InvalidAmountNumberException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Currency\InvalidCode as InvalidCurrencyCodeException;
use PavelOmelchuk\CommissionTask\Contract\Service\Currency as CurrencyServiceContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;

class AmountAndCurrencyTest extends TestCase
{
    /** @var AmountAndCurrency */
    protected $amountAndCurrency;

    /** @var CurrencyServiceContract */
    protected $currencyService;

    protected function setUp()
    {
        $this->amountAndCurrency = new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_EUR);

        $this->currencyService = CurrencyServiceFactory::getInstance();
        $this->setConversionRates();
    }

    public function testGetAmount()
    {
        $this->assertEquals(
            '100',
            $this->amountAndCurrency->getAmount()
        );
    }

    public function testGetCurrency()
    {
        $this->assertEquals(
            AbstractCurrencyEntity::CODE_EUR,
            $this->amountAndCurrency->getCurrency()
        );
    }

    /** @dataProvider dataProviderForSuccessSetAmountTest */
    public function testSuccessSetAmount(string $newAmount)
    {
        $this->amountAndCurrency->setAmount($newAmount);

        $this->assertEquals(
            $newAmount,
            $this->amountAndCurrency->getAmount()
        );
    }

    /** @dataProvider dataProviderForSuccessSetCurrencyTest */
    public function testSuccessSetCurrency(string $newCurrencyCode)
    {
        $this->amountAndCurrency->setCurrency($newCurrencyCode);

        $this->assertEquals(
            $newCurrencyCode,
            $this->amountAndCurrency->getCurrency()
        );
    }

    /** @dataProvider dataProviderForSuccessCreatedTest */
    public function testSuccessCreated(string $amount, string $currencyCode)
    {
        $newAmountAndCurrency = new AmountAndCurrency($amount, $currencyCode);

        $this->assertInstanceOf(AmountAndCurrency::class, $newAmountAndCurrency);
    }

    /** @dataProvider dataProviderForValidationErrorOnSetIncorrectAmountTest */
    public function testValidationErrorOnSetIncorrectAmount(string $newIncorrectAmount)
    {
        $this->expectException(InvalidAmountNumberException::class);

        $this->amountAndCurrency->setAmount($newIncorrectAmount);
    }

    /** @dataProvider dataProviderForValidationErrorOnSetIncorrectCurrencyTest */
    public function testValidationErrorOnSetIncorrectCurrency(string $newIncorrectCurrencyCode)
    {
        $this->expectException(InvalidCurrencyCodeException::class);

        $this->amountAndCurrency->setCurrency($newIncorrectCurrencyCode);
    }

    /** @dataProvider dataProviderForAddTest */
    public function testAdd(
        AmountAndCurrency $anotherAmountAndCurrency,
        string $amountExpectation,
        string $currencyExpectation
    ) {
        $resultAmountAndCurrency = $this->amountAndCurrency->add($anotherAmountAndCurrency);

        $this->assertEquals($amountExpectation, $resultAmountAndCurrency->getAmount());
        $this->assertEquals($currencyExpectation, $resultAmountAndCurrency->getCurrency());
    }

    /** @dataProvider dataProviderForSubTest */
    public function testSub(
        AmountAndCurrency $anotherAmountAndCurrency,
        string $amountExpectation,
        string $currencyExpectation
    ) {
        $resultAmountAndCurrency = $this->amountAndCurrency->sub($anotherAmountAndCurrency);

        $this->assertEquals($amountExpectation, $resultAmountAndCurrency->getAmount());
        $this->assertEquals($currencyExpectation, $resultAmountAndCurrency->getCurrency());
    }

    /** @dataProvider dataProviderForIsAmountGreaterThenTest */
    public function testIsAmountGreaterThen(AmountAndCurrency $anotherAmountAndCurrency, bool $expectation)
    {
        $this->assertEquals($expectation, $this->amountAndCurrency->isAmountGreaterThen($anotherAmountAndCurrency));
    }

    /** @dataProvider dataProviderForToCurrencyTest */
    public function testToCurrency(
        string $toCurrencyCode,
        string $amountExpectation,
        string $currencyExpectation
    ) {
        $convertedAmountAndCurrency = $this->amountAndCurrency->toCurrency($toCurrencyCode);

        $this->assertEquals($amountExpectation, $convertedAmountAndCurrency->getAmount());
        $this->assertEquals($currencyExpectation, $convertedAmountAndCurrency->getCurrency());
    }

    public function dataProviderForSuccessCreatedTest(): array
    {
        return [
            'natural amount and EUR currency' => ['50', AbstractCurrencyEntity::CODE_EUR],
            'float amount and USD currency' => ['120.55', AbstractCurrencyEntity::CODE_USD],
            'natural amount and JPY currency' => ['99', AbstractCurrencyEntity::CODE_JPY],
        ];
    }

    public function dataProviderForSuccessSetAmountTest(): array
    {
        return [
            'success set natural amount' => ['100'],
            'success set float amount' => ['200.21']
        ];
    }

    public function dataProviderForSuccessSetCurrencyTest(): array
    {
        return [
            'success set EUR' => [AbstractCurrencyEntity::CODE_EUR],
            'success set USD' => [AbstractCurrencyEntity::CODE_USD],
            'success set JPY' => [AbstractCurrencyEntity::CODE_JPY],
        ];
    }

    public function dataProviderForValidationErrorOnSetIncorrectAmountTest(): array
    {
        return [
            'validation error on set non numeric amount' => ['non numeric amount'],
            'validation error on set amount less than 0' => ['-1'],
        ];
    }

    public function dataProviderForValidationErrorOnSetIncorrectCurrencyTest(): array
    {
        return [
            'validation error on set unsupported currency' => ['unsuppoerted currency code']
        ];
    }

    public function dataProviderForAddTest(): array
    {
        return [
            'add instance with the same currency' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_EUR),
                '200',
                AbstractCurrencyEntity::CODE_EUR
            ],
            'add instance with different currency' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_USD),
                '150',
                AbstractCurrencyEntity::CODE_EUR
            ],
        ];
    }

    public function dataProviderForSubTest(): array
    {
        return [
            'sub instance with the same currency' => [
                new AmountAndCurrency('50', AbstractCurrencyEntity::CODE_EUR),
                '50',
                AbstractCurrencyEntity::CODE_EUR
            ],
            'sub instance with different currency' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_JPY),
                '99',
                AbstractCurrencyEntity::CODE_EUR
            ],
        ];
    }

    public function dataProviderForIsAmountGreaterThenTest(): array
    {
        return [
            'amount is greater then less same currency' => [
                new AmountAndCurrency('50', AbstractCurrencyEntity::CODE_EUR),
                true
            ],
            'amount is less then greater same currency' => [
                new AmountAndCurrency('200', AbstractCurrencyEntity::CODE_EUR),
                false
            ],
            'amount is greater then less different currency' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_USD),
                true
            ],
            'amount is less then greater different currency' => [
                new AmountAndCurrency('1000', AbstractCurrencyEntity::CODE_USD),
                false
            ],
        ];
    }

    public function dataProviderForToCurrencyTest(): array
    {
        return [
            'to the same currency' => [AbstractCurrencyEntity::CODE_EUR, '100', AbstractCurrencyEntity::CODE_EUR],
            'to USD currency' => [AbstractCurrencyEntity::CODE_USD, '200', AbstractCurrencyEntity::CODE_USD],
            'to JPY currency' => [AbstractCurrencyEntity::CODE_JPY, '10000', AbstractCurrencyEntity::CODE_JPY],
        ];
    }

    private function setConversionRates()
    {
        $conversionRates = $this->currencyService->getConversionRates();

        $conversionRates[AbstractCurrencyEntity::CODE_EUR][AbstractCurrencyEntity::CODE_USD] = '2';
        $conversionRates[AbstractCurrencyEntity::CODE_USD][AbstractCurrencyEntity::CODE_EUR] = '0.5';

        $conversionRates[AbstractCurrencyEntity::CODE_EUR][AbstractCurrencyEntity::CODE_JPY] = '100';
        $conversionRates[AbstractCurrencyEntity::CODE_JPY][AbstractCurrencyEntity::CODE_EUR] = '0.01';

        $this->currencyService->setConversionRates($conversionRates);
    }
}