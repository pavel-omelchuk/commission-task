<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Validator;

use PavelOmelchuk\CommissionTask\Contract\Validator\Input as InputValidatorContract;
use PavelOmelchuk\CommissionTask\Validator\Input\LocalFileSystem as LocalFileValidator;

/**
 * Input validator factory.
 */
class Input
{
    /**
     * Provides with an Input validator instance.
     */
    public static function getInstance(): InputValidatorContract
    {
        return new LocalFileValidator();
    }
}
