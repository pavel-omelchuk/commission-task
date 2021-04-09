<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Validator;

use PavelOmelchuk\CommissionTask\Contract\Validator\User as UserValidatorContract;
use PavelOmelchuk\CommissionTask\Validator\User\InMemory as InMemoryUserValidator;

/**
 * User validator factory.
 */
class User
{
    /**
     * Provides with an User validator instance.
     */
    public static function getInstance(): UserValidatorContract
    {
        return InMemoryUserValidator::getInstance();
    }
}
