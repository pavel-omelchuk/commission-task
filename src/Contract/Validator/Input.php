<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Validator;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;

/**
 * Interface Input
 * Describes an input validator component's interface.
 */
interface Input
{
    /**
     * Validates input argument string.
     *
     * @throws ValidationException
     */
    public function isValidInput(string $input): bool;
}
