<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation\Input;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * Input file row validation exception.
 */
class InvalidRow extends ValidationException
{
    /**
     * InvalidRow constructor.
     *
     * @param int $code
     */
    public function __construct(string $inputFileRow = '', $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $this->generateExceptionMessageWithInvalidInputFileRow($inputFileRow);

        parent::__construct($exceptionMessage, $code, $previous);
    }

    protected function generateExceptionMessageWithInvalidInputFileRow(string $invalidInputFileRow): string
    {
        return "Invalid row in the file = {$invalidInputFileRow}";
    }
}
