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
use PavelOmelchuk\CommissionTask\Factory\Repository\Operation as OperationRepositoryFactory;
use PavelOmelchuk\CommissionTask\Contract\Repository\Operation as OperationRepositoryContract;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut\HasFreeOfCharge
    as HasFreeOfChargeCommissionFeeService;

class HasFreeOfChargeLimitationTest extends TestCase
{
    /** @var HasFreeOfChargeCommissionFeeService */
    protected $commissionFeePolicy;

    /** @var CurrencyServiceContract */
    protected $currency;

    /** @var OperationRepositoryContract */
    protected $operationRepository;

    protected function setUp()
    {
        $this->commissionFeePolicy = new HasFreeOfChargeCommissionFeeService(
            '0.003',
            '3',
            new AmountAndCurrency('1000', AbstractCurrency::CODE_EUR)
        );

        $this->currency = CurrencyServiceFactory::getInstance();

        $this->operationRepository = OperationRepositoryFactory::getInstance();

        $this->setConversionRates();
    }

    /** @dataProvider dataProviderForGetFeeForOperationTest */
    public function testGeeFeeForOperation(array $userOperationsAndExpectations)
    {
        foreach ($userOperationsAndExpectations as $userOperationAndExpectation) {
            $operation = $userOperationAndExpectation['operation'];
            $expectation = $userOperationAndExpectation['expectation'];

            $this->assertTrue($expectation === $this->commissionFeePolicy->getFeeForOperation($operation));

            $this->operationRepository->save($operation);
        }
    }

    public function dataProviderForGetFeeForOperationTest(): array
    {
        return [
            'first user`s operation not over the limit (without previous operations)' => [
                [
                    [
                        'operation' => new Operation(
                            '2021-04-09',
                            Operation::TYPE_CASH_OUT,
                            new User('1', User::TYPE_NATURAL),
                            new AmountAndCurrency('500', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ]
                ]
            ],
            'first user`s operation not over the limit (with previous operations)' => [
                [
                    [
                        'operation' => new Operation(
                            '2021-04-06',
                            Operation::TYPE_CASH_OUT,
                            new User('2', User::TYPE_NATURAL),
                            new AmountAndCurrency('500', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ],
                    [
                        'operation' => new Operation(
                            '2021-04-07',
                            Operation::TYPE_CASH_OUT,
                            new User('2', User::TYPE_NATURAL),
                            new AmountAndCurrency('200', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ],
                    [
                        'operation' => new Operation(
                            '2021-04-08',
                            Operation::TYPE_CASH_OUT,
                            new User('2', User::TYPE_NATURAL),
                            new AmountAndCurrency('100', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ]
                ]
            ],
            'first user`s operation over the limit' => [
                [
                    [
                        'operation' => new Operation(
                            '2021-04-09',
                            Operation::TYPE_CASH_OUT,
                            new User('3', User::TYPE_NATURAL),
                            new AmountAndCurrency('2000', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '3.00'
                    ]
                ]
            ],
            'user`s operation over the count-per-week limit' => [
                [
                    [
                        'operation' => new Operation(
                            '2021-04-06',
                            Operation::TYPE_CASH_OUT,
                            new User('4', User::TYPE_NATURAL),
                            new AmountAndCurrency('300', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ],
                    [
                        'operation' => new Operation(
                            '2021-04-07',
                            Operation::TYPE_CASH_OUT,
                            new User('4', User::TYPE_NATURAL),
                            new AmountAndCurrency('200', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ],
                    [
                        'operation' => new Operation(
                            '2021-04-08',
                            Operation::TYPE_CASH_OUT,
                            new User('4', User::TYPE_NATURAL),
                            new AmountAndCurrency('100', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ],
                    [
                        'operation' => new Operation(
                            '2021-04-09',
                            Operation::TYPE_CASH_OUT,
                            new User('4', User::TYPE_NATURAL),
                            new AmountAndCurrency('100', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.30'
                    ]
                ]
            ],
            'user`s operation over the amount-per-week limit' => [
                [
                    [
                        'operation' => new Operation(
                            '2021-04-06',
                            Operation::TYPE_CASH_OUT,
                            new User('5', User::TYPE_NATURAL),
                            new AmountAndCurrency('500', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '0.00'
                    ],
                    [
                        'operation' => new Operation(
                            '2021-04-07',
                            Operation::TYPE_CASH_OUT,
                            new User('5', User::TYPE_NATURAL),
                            new AmountAndCurrency('10500', AbstractCurrency::CODE_EUR)
                        ),
                        'expectation' => '30.00'
                    ]
                ]
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