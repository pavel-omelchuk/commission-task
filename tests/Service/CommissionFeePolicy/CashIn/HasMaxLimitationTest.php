<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Service\CommissionFeePolicy\CashIn;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Model\User;
use PavelOmelchuk\CommissionTask\Model\Operation;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Contract\Entity\Currency as AbstractCurrency;
use PavelOmelchuk\CommissionTask\Contract\Entity\Operation as AbstractOperation;
use PavelOmelchuk\CommissionTask\Contract\Entity\User as AbstractUser;
use PavelOmelchuk\CommissionTask\Contract\Service\Currency as CurrencyServiceContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashIn\HasMaxLimitation
    as HasMaxLimitationCommissionFeeService;

class HasMaxLimitationTest extends TestCase
{
    /** @var HasMaxLimitationCommissionFeeService */
    protected $commissionFeePolicy;

    /** @var CurrencyServiceContract */
    protected $currency;

    protected function setUp()
    {
        $this->commissionFeePolicy = new HasMaxLimitationCommissionFeeService(
            '0.0003',
            new AmountAndCurrency('5', AbstractCurrency::CODE_EUR)
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
            'cash in for user without conversion not over limitation' => [
                new Operation(
                    '2021-04-08',
                    AbstractOperation::TYPE_CASH_IN,
                    new User('1', AbstractUser::TYPE_NATURAL),
                    new AmountAndCurrency('10000', AbstractCurrency::CODE_EUR)
                ),
                '3.00'
            ],
            'cash in for user without conversion over limitation' => [
                new Operation(
                    '2021-04-07',
                    AbstractOperation::TYPE_CASH_IN,
                    new User('2', AbstractUser::TYPE_LEGAL),
                    new AmountAndCurrency('10000000', AbstractCurrency::CODE_EUR)
                ),
                '5.00'
            ],
            'cash in for user with conversion not over limitation' => [
                new Operation(
                    '2021-04-06',
                    Operation::TYPE_CASH_IN,
                    new User('3', AbstractUser::TYPE_NATURAL),
                    new AmountAndCurrency('20000', AbstractCurrency::CODE_USD)
                ),
                '6.00'
            ],
            'cash in for user with conversion over limitation' => [
                new Operation(
                    '2021-04-05',
                    Operation::TYPE_CASH_IN,
                    new User('4', AbstractUser::TYPE_LEGAL),
                    new AmountAndCurrency('9999999999', AbstractCurrency::CODE_JPY)
                ),
                '500'
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