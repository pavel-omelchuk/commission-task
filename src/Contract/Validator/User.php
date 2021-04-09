<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Validator;

use PavelOmelchuk\CommissionTask\Exception\Validation\User\InvalidId as InvalidUserIdException;
use PavelOmelchuk\CommissionTask\Exception\Validation\User\InvalidType as InvalidUserTypeException;

/**
 * Interface User
 * Describes an user validator component's interface.
 */
interface User
{
    /**
     * @throws InvalidUserIdException
     */
    public function isIdValid(string $userId): bool;

    /**
     * @throws InvalidUserTypeException
     */
    public function isTypeValid(string $userType): bool;
}
