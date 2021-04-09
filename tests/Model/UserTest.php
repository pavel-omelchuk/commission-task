<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Model;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Model\User;
use PavelOmelchuk\CommissionTask\Contract\Entity\User as AbstractUserEntity;
use PavelOmelchuk\CommissionTask\Exception\Validation\User\InvalidId as InvalidUserIdException;
use PavelOmelchuk\CommissionTask\Exception\Validation\User\InvalidType as InvalidUserTypeException;

class UserTest extends TestCase
{
    /** @var User */
    protected $user;

    protected function setUp()
    {
        $this->user = new User('1', AbstractUserEntity::TYPE_NATURAL);
    }

    /**
     * @dataProvider dataProviderForSuccessCreatedTest
     */
    public function testSuccessCreated(string $id, string $type)
    {
        $newUser = new User($id, $type);

        $this->assertInstanceOf(User::class, $newUser);
    }

    public function testGetId()
    {
        $this->assertEquals(
            '1',
            $this->user->getId()
        );
    }

    public function testGetType()
    {
        $this->assertEquals(
            AbstractUserEntity::TYPE_NATURAL,
            $this->user->getType()
        );
    }

    /**
     * @dataProvider dataProviderForSetIdTest
     */
    public function testSuccessfulSetId(string $newId)
    {
        $this->user->setId($newId);

        $this->assertEquals(
            $newId,
            $this->user->getId()
        );
    }

    /**
     * @dataProvider dataProviderForSetTypeTest
     */
    public function testSuccessfulSetType(string $newType)
    {
        $this->user->setType($newType);

        $this->assertEquals(
            $newType,
            $this->user->getType()
        );
    }

    /**
     * @dataProvider dataProviderForValidationErrorOnSetIdTest
     */
    public function testValidationErrorOnSetIncorrectId(string $newIncorrectId)
    {
        $this->expectException(InvalidUserIdException::class);

        $this->user->setId($newIncorrectId);
    }

    /**
     * @dataProvider dataProviderForValidationErrorOnSetTypeTest
     */
    public function testValidationErrorOnSetIncorrectType(string $newIncorrectType)
    {
        $this->expectException(InvalidUserTypeException::class);

        $this->user->setType($newIncorrectType);
    }

    public function dataProviderForSuccessCreatedTest(): array
    {
        return [
            'create valid legal user' => ['1', AbstractUserEntity::TYPE_LEGAL],
            'create valid natural user' => ['2', AbstractUserEntity::TYPE_NATURAL],
        ];
    }

    public function dataProviderForSetIdTest(): array
    {
        return ['set id' => ['15']];
    }

    public function dataProviderForSetTypeTest(): array
    {
        return [
            'set type to natural' => [AbstractUserEntity::TYPE_NATURAL],
            'set type to legal' => [AbstractUserEntity::TYPE_LEGAL],
        ];
    }

    public function dataProviderForValidationErrorOnSetIdTest(): array
    {
        return [
            'validation error on non numeric user id' => ['non numeric id'],
        ];
    }

    public function dataProviderForValidationErrorOnSetTypeTest(): array
    {
        return [
            'validation error on not supported user type' => ['non supported user type']
        ];
    }
}