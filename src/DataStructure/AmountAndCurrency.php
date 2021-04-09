<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\DataStructure;

use PavelOmelchuk\CommissionTask\Exception\Validation\Amount\InvalidNumber as InvalidAmountNumberException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Currency\InvalidCode as InvalidCurrencyCodeException;
use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Math as MathServiceFactory;
use PavelOmelchuk\CommissionTask\Factory\Validator\Operation as OperationValidatorFactory;

/**
 * Class AmountAndCurrency.
 *
 * An entity that implies inseparable pair of "Operation Amount" and "Operation Currency".
 * Provides with helpful methods to work with operation amount and currency.
 */
class AmountAndCurrency
{
    /**
     * Operation amount.
     *
     * @var string
     */
    protected $amount;

    /**
     * Operation currency.
     *
     * @var string
     */
    protected $currency;

    /**
     * AmountAndCurrency constructor.
     */
    public function __construct(string $amount, string $currency)
    {
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @throws InvalidAmountNumberException
     */
    public function setAmount(string $amount)
    {
        if (!OperationValidatorFactory::getInstance()->isAmountValid($amount)) {
            throw new InvalidAmountNumberException($amount);
        }

        $this->amount = $amount;
    }

    /**
     * @throws
     */
    public function setCurrency(string $currencyCode)
    {
        $currencyCode = strtolower($currencyCode);

        if (!OperationValidatorFactory::getInstance()->isCurrencyCodeValid($currencyCode)) {
            throw new InvalidCurrencyCodeException($currencyCode);
        }

        $this->currency = $currencyCode;
    }

    /**
     * Summarizes two AmountAndCurrency instances: current and passed
     * Returns result as a new instance of AmountAndCurrency DS.
     * Converts the result to currency of initial ($this) instance.
     */
    public function add(AmountAndCurrency $another): AmountAndCurrency
    {
        // convert into current currency
        $anotherConverted = $another->toCurrency($this->getCurrency());
        // summarize amounts
        $totalAmount = MathServiceFactory::getInstance()->add($this->getAmount(), $anotherConverted->getAmount());
        // return result as a new instance
        return new static($totalAmount, $this->getCurrency());
    }

    /**
     * Subtracts $another instance from current.
     * Returns result as a new instance of AmountAndCurrency DS.
     * Converts the result to currency of initial ($this) instance.
     *
     * RETURNS ZERO AMOUNT AND CURRENCY IF $another GREATER THEN CURRENT AMOUNT
     */
    public function sub(AmountAndCurrency $another): AmountAndCurrency
    {
        // convert into current currency
        $anotherConverted = $another->toCurrency($this->getCurrency());

        // return 0 if $another is greater then current instance
        if ($another->isAmountGreaterThen($this)) {
            return new static('0', $this->getCurrency());
        }

        // get difference between current and another amounts
        $difference = MathServiceFactory::getInstance()->sub($this->getAmount(), $anotherConverted->getAmount());
        // return result as a new instance
        return new static($difference, $this->getCurrency());
    }

    /**
     * Determines whether the current amount greater then $another's amount in current currency.
     */
    public function isAmountGreaterThen(AmountAndCurrency $another): bool
    {
        $mathService = MathServiceFactory::getInstance();

        // $another AmountAndCurrency instance converted to currency of $this instance
        $anotherConverted = $another->toCurrency($this->getCurrency());

        // whether the current instance's amount greater then $another instance's amount
        $comparisonResult = $mathService->comp($this->getAmount(), $anotherConverted->getAmount());

        return $comparisonResult === $mathService::COMP_GREATER_LEFT;
    }

    /**
     * Converts current amount to the new based on "Current Currency -> Target Currency" conversion rate.
     * Returns as a new instance of AmountAndCurrency DS.
     *
     * @throws ValidationException
     */
    public function toCurrency(string $targetCurrencyCode): AmountAndCurrency
    {
        // if current currency already equals to the target then do nothing
        if ($this->getCurrency() === $targetCurrencyCode) {
            return $this;
        }

        // currency service
        $currencyService = CurrencyServiceFactory::getInstance();
        // convert amount to the new currency
        $convertedAmount = $currencyService->convert($this->getAmount(), $this->getCurrency(), $targetCurrencyCode);

        // create new instance of AmountAndCurrency
        return new static($convertedAmount, $targetCurrencyCode);
    }
}
