<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation\Input;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * Argument count validation exception.
 */
class InvalidArgumentCount extends ValidationException
{
    /**
     * InvalidArgumentCount constructor.
     *
     * @param int $code
     */
    public function __construct(string $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
