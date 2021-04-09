<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Validator;

use PavelOmelchuk\CommissionTask\Contract\Validator\Operation as OperationValidatorContract;
use PavelOmelchuk\CommissionTask\Validator\Operation\RawInput as RawInputValidator;

/**
 * Operation validator factory.
 */
class Operation
{
    /**
     * Provides with an Operation validator instance.
     */
    public static function getInstance(): OperationValidatorContract
    {
        return RawInputValidator::getInstance();
    }
}
