<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Validator\Operation;

use DateTime;
use PavelOmelchuk\CommissionTask\Contract\Validator\Operation as OperationValidatorContract;
use PavelOmelchuk\CommissionTask\Exception\Runtime\Singleton\WakeUpAttempt as SingletonWakeUpAttemptException;
use PavelOmelchuk\CommissionTask\Factory\Service\Calendar as CalendarFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Math as MathFactory;

/**
 * Provides a set of methods to validate operation input data.
 * ---------------
 * Implements the Singleton design pattern
 * ---------------.
 */
class RawInput implements OperationValidatorContract
{
    /**
     * @var array
     */
    private static $instances = [];

    /**
     * Hidden constructor in terms of Singleton pattern.
     */
    protected function __construct()
    {
    }

    /**
     * Returns new or existing instance of the RawInput class.
     */
    public static function getInstance(): RawInput
    {
        $className = static::class;

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new static();
        }

        return self::$instances[$className];
    }

    /** {@inheritdoc} */
    public function isDateValid(string $date): bool
    {
        $supportedDateFormat = CalendarFactory::getInstance()->getSupportedDateFormat();
        $dateTime = DateTime::createFromFormat($supportedDateFormat, $date);

        // Should implies a date and has valid format
        return $dateTime && $dateTime->format($supportedDateFormat) === $date;
    }

    /** {@inheritdoc} */
    public function isOperationTypeValid(string $operationType): bool
    {
        $supportedOperationTypes = ConfigFactory::getInstance()->get('app.supported.operation_types');

        return in_array($operationType, $supportedOperationTypes, true);
    }

    /** {@inheritdoc} */
    public function isAmountValid(string $amount): bool
    {
        $mathService = MathFactory::getInstance();

        // amount should be numeric and greater or equals to 0
        return is_numeric($amount)
            && $mathService->comp($amount, '0') !== $mathService::COMP_GREATER_RIGHT;
    }

    /** {@inheritdoc} */
    public function isCurrencyCodeValid(string $currencyCode): bool
    {
        $supportedCurrencyCodes = ConfigFactory::getInstance()->get('app.supported.currencies');

        return in_array($currencyCode, $supportedCurrencyCodes, true);
    }

    /**
     * Prevent object to be restored from a string.
     *
     * @throws SingletonWakeUpAttemptException
     */
    public function __wakeup()
    {
        throw new SingletonWakeUpAttemptException(static::class);
    }

    /**
     * Hidden __clone method in terms of Singleton pattern.
     */
    protected function __clone()
    {
    }
}
