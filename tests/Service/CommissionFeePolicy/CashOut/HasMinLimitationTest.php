<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Service\CommissionFeePolicy\CashOut;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Model\User;
use PavelOmelchuk\CommissionTask\Model\Operation;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Contract\Entity\Currency as AbstractCurrency;
use PavelOmelchuk\CommissionTask\Contract\Service\Currency as CurrencyServiceContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut\HasMinLimitation
    as HasMinLimitationCommissionFeeService;

class HasMinLimitationTest extends TestCase
{
    /** @var HasMinLimitationCommissionFeeService */
    protected $commissionFeePolicy;

    /** @var CurrencyServiceContract */
    protected $currency;

    protected function setUp()
    {
        $this->commissionFeePolicy = new HasMinLimitationCommissionFeeService(
            '0.003',
            new AmountAndCurrency('0.5', AbstractCurrency::CODE_EUR)
        );

        $this->currency = CurrencyServiceFactory::getInstance();

        $this->setConversionRates();
    }

    /** @dataProvider dataProviderForGetFeeForOperationTest */
    public function testGetFeeForOperation(Operation $operation, string $expectation)
    {
        $this->assertTrue($expectation === $this->commissionFeePolicy->getFeeForOperation($operation));
    }

    public function dataProviderForGetFeeForOperationTest(): array
    {
        return [
            'cash out without conversion not over the limitation' => [
                new Operation(
                    '2021-04-09',
                    Operation::TYPE_CASH_OUT,
                    new User('1', User::TYPE_LEGAL),
                    new AmountAndCurrency('1000', AbstractCurrency::CODE_EUR)
                ),
                '3.00'
            ],
            'cash out without conversion over the limitation' => [
                new Operation(
                    '2021-04-08',
                    Operation::TYPE_CASH_OUT,
                    new User('2', User::TYPE_LEGAL),
                    new AmountAndCurrency('100', AbstractCurrency::CODE_EUR)
                ),
                '0.50'
            ],
            'cash out with conversion not over the limitation' => [
                new Operation(
                    '2021-04-07',
                    Operation::TYPE_CASH_OUT,
                    new User('3', User::TYPE_LEGAL),
                    new AmountAndCurrency('500', AbstractCurrency::CODE_USD)
                ),
                '1.50'
            ],
            'cash out with conversion over the limitation' => [
                new Operation(
                    '2021-04-06',
                    Operation::TYPE_CASH_OUT,
                    new User('4', User::TYPE_LEGAL),
                    new AmountAndCurrency('10', AbstractCurrency::CODE_JPY)
                ),
                '50'
            ],
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