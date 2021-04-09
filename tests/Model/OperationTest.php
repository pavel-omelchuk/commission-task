<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Model;

use DateTime;
use PavelOmelchuk\CommissionTask\Model\User;
use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Model\Operation;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Contract\Entity\Operation as AbstractOperationEntity;
use PavelOmelchuk\CommissionTask\Contract\Entity\User as AbstractUserEntity;
use PavelOmelchuk\CommissionTask\Contract\Entity\Currency as AbstractCurrencyEntity;
use PavelOmelchuk\CommissionTask\Exception\Validation\Operation\InvalidDate as InvalidOperationDateException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Operation\InvalidType as InvalidOperationTypeException;

class OperationTest extends TestCase
{
    /** @var Operation */
    protected $operation;

    protected function setUp()
    {
        $operationUser = new User('1', AbstractUserEntity::TYPE_NATURAL);
        $operationAmountAndCurrency = new AmountAndCurrency('1', AbstractCurrencyEntity::CODE_EUR);

        $this->operation = new Operation(
            '2021-04-08',
            AbstractOperationEntity::TYPE_CASH_OUT,
            $operationUser,
            $operationAmountAndCurrency
        );
    }

    public function testGetDate()
    {
        $this->assertEquals(new DateTime('2021-04-08'), $this->operation->getDate());
    }

    public function testGetType()
    {
        $this->assertEquals(AbstractOperationEntity::TYPE_CASH_OUT, $this->operation->getType());
    }

    public function testGetUser()
    {
        $this->assertInstanceOf(User::class, $this->operation->getUser());

        $this->assertEquals('1', $this->operation->getUser()->getId());
        $this->assertEquals(AbstractUserEntity::TYPE_NATURAL, $this->operation->getUser()->getType());
    }

    public function testGetAmountAndCurrency()
    {
        $this->assertInstanceOf(AmountAndCurrency::class, $this->operation->getAmountAndCurrency());

        $this->assertEquals('1', $this->operation->getAmountAndCurrency()->getAmount());
        $this->assertEquals(
            AbstractCurrencyEntity::CODE_EUR,
            $this->operation->getAmountAndCurrency()->getCurrency()
        );
    }

    /** @dataProvider dataProviderForSuccessSetDateTest */
    public function testSuccessSetDate(string $newDate)
    {
        $this->operation->setDate($newDate);

        $this->assertEquals(new DateTime($newDate), $this->operation->getDate());
    }

    /** @dataProvider dataProviderForSuccessSetTypeTest */
    public function testSuccessSetType(string $newType)
    {
        $this->operation->setType($newType);

        $this->assertEquals($newType, $this->operation->getType());
    }

    /** @dataProvider dataProviderForSuccessSetUserTest */
    public function testSuccessSetUser(User $newUser)
    {
        $this->operation->setUser($newUser);

        $this->assertEquals($newUser->getId(), $this->operation->getUser()->getId());
        $this->assertEquals($newUser->getType(), $this->operation->getUser()->getType());
    }

    /** @dataProvider dataProviderForSuccessSetAmountAndCurrencyTest */
    public function testSuccessSetAmountAndCurrency(AmountAndCurrency $newAmountAndCurrency)
    {
        $this->operation->setAmountAndCurrency($newAmountAndCurrency);

        $this->assertEquals(
            $newAmountAndCurrency->getAmount(),
            $this->operation->getAmountAndCurrency()->getAmount()
        );

        $this->assertEquals(
            $newAmountAndCurrency->getCurrency(),
            $this->operation->getAmountAndCurrency()->getCurrency()
        );
    }

    /** @dataProvider dataProviderForSuccessCreatedTest */
    public function testSuccessCreated(string $date, string $type, User $user, AmountAndCurrency $amountAndCurrency)
    {
        $newOperation = new Operation($date, $type, $user, $amountAndCurrency);

        $this->assertInstanceOf(Operation::class, $newOperation);
    }

    /** @dataProvider dataProviderForValidationErrorOnSetIncorrectDateTest */
    public function testValidationErrorOnSetIncorrectDate(string $newIncorrectDate)
    {
        $this->expectException(InvalidOperationDateException::class);

        $this->operation->setDate($newIncorrectDate);
    }

    /** @dataProvider dataProviderForValidationErrorOnSetIncorrectTypeTest */
    public function testValidationErrorOnSetIncorrectType(string $newIncorrectType)
    {
        $this->expectException(InvalidOperationTypeException::class);

        $this->operation->setType($newIncorrectType);
    }

    public function dataProviderForSuccessSetDateTest(): array
    {
        return [
            'set new date in valid format' => ['2020-01-01'],
        ];
    }

    public function dataProviderForSuccessSetTypeTest(): array
    {
        return [
            'set new valid CASH OUT operation type' => [AbstractOperationEntity::TYPE_CASH_OUT],
            'set new valid CASH IN operation type' => [AbstractOperationEntity::TYPE_CASH_IN],
        ];
    }

    public function dataProviderForSuccessSetUserTest(): array
    {
        return [
            'set new Valid User Natural' => [new User('1', AbstractUserEntity::TYPE_NATURAL)],
            'set new Valid User Legal' => [new User('2', AbstractUserEntity::TYPE_LEGAL)],
        ];
    }

    public function dataProviderForSuccessSetAmountAndCurrencyTest(): array
    {
        return [
            'set new valid amount and currency EUR' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_EUR)
            ],
            'set new valid amount and currency USD' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_USD)
            ],
            'set new valid amount and currency JPY' => [
                new AmountAndCurrency('100', AbstractCurrencyEntity::CODE_JPY)
            ],
        ];
    }

    public function dataProviderForSuccessCreatedTest(): array
    {
        return [
            'create new operation with valid data' => [
                '2020-03-03',
                AbstractOperationEntity::TYPE_CASH_OUT,
                new User('1', AbstractUserEntity::TYPE_NATURAL),
                new AmountAndCurrency('200', AbstractCurrencyEntity::CODE_EUR)
            ]
        ];
    }

    public function dataProviderForValidationErrorOnSetIncorrectDateTest(): array
    {
        return [
            'incorrect date' => ['incorrect date at all'],
            'incorrect date format' => ['2020/08/25'],
        ];
    }

    public function dataProviderForValidationErrorOnSetIncorrectTypeTest(): array
    {
        return [
            'unsupported operation type' => ['unsupported operation type']
        ];
    }
}