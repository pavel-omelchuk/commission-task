<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Model;

use PavelOmelchuk\CommissionTask\Contract\Entity\User as UserEntityContract;
use PavelOmelchuk\CommissionTask\Exception\Validation\User\InvalidId as InvalidUserIdException;
use PavelOmelchuk\CommissionTask\Exception\Validation\User\InvalidType as InvalidUserTypeException;
use PavelOmelchuk\CommissionTask\Factory\Validator\User as UserValidatorFactory;

/**
 * Describes User entity.
 */
class User implements UserEntityContract
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $type;

    public function __construct(string $id, string $type)
    {
        $this->setId($id);
        $this->setType($type);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets user id or throws an exception if passed $currencyCode is not valid.
     *
     * @throws InvalidUserIdException
     */
    public function setId(string $id)
    {
        if (!UserValidatorFactory::getInstance()->isIdValid($id)) {
            throw new InvalidUserIdException($id);
        }

        $this->id = $id;
    }

    /**
     * Sets user's type or throws an exception if passed $userType is not valid.
     *
     * @throws InvalidUserTypeException
     */
    public function setType(string $type)
    {
        if (!UserValidatorFactory::getInstance()->isTypeValid($type)) {
            throw new InvalidUserTypeException($type);
        }

        $this->type = $type;
    }
}
