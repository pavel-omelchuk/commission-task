<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation\Currency;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * Currency Code validation exception.
 */
class InvalidCode extends ValidationException
{
    /**
     * InvalidCode constructor.
     *
     * @param int $code
     */
    public function __construct(string $currencyCode = '', $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $this->generateExceptionMessageWithInvalidCurrency($currencyCode);

        parent::__construct($exceptionMessage, $code, $previous);
    }

    protected function generateExceptionMessageWithInvalidCurrency(string $invalidCurrencyCode): string
    {
        return "Incorrect or unsupported currency = {$invalidCurrencyCode}";
    }
}
