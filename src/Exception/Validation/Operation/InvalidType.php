<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation\Operation;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * Operation Type validation exception.
 */
class InvalidType extends ValidationException
{
    /**
     * InvalidType constructor.
     *
     * @param int $code
     */
    public function __construct(string $operationType = '', $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $this->generateExceptionMessageWithInvalidType($operationType);

        parent::__construct($exceptionMessage, $code, $previous);
    }

    protected function generateExceptionMessageWithInvalidType(string $invalidOperationType): string
    {
        return "Unsupported operation type = {$invalidOperationType}";
    }
}
