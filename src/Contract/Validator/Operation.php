<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Validator;

use PavelOmelchuk\CommissionTask\Exception\Validation\Amount\InvalidNumber as InvalidAmountNumberException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Currency\InvalidCode as InvalidCurrencyCodeException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Operation\InvalidDate as InvalidOperationDateException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Operation\InvalidType as InvalidOperationTypeException;

/**
 * Interface Operation
 * Describes an operation validator component's interface.
 */
interface Operation
{
    /**
     * @throws InvalidOperationDateException
     */
    public function isDateValid(string $date): bool;

    /**
     * @throws InvalidOperationTypeException
     */
    public function isOperationTypeValid(string $operationType): bool;

    /**
     * @throws InvalidAmountNumberException
     */
    public function isAmountValid(string $amount): bool;

    /**
     * @throws InvalidCurrencyCodeException
     */
    public function isCurrencyCodeValid(string $currencyCode): bool;
}
