<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation;

abstract class ValidationException extends \Exception
{
    /**
     * Output validation error's message.
     */
    public function log()
    {
        echo $this->message.PHP_EOL;
    }
}
