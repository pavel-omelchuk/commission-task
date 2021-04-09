<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\Currency;

use PavelOmelchuk\CommissionTask\Contract\Service\Currency as CurrencyServiceContract;
use PavelOmelchuk\CommissionTask\Exception\Runtime\Singleton\WakeUpAttempt as SingletonWakeUpAttemptException;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Math as MathServiceFactory;

/**
 * Class ConfigBased.
 * Currency service implements all necessary functionality based on configuration sources.
 * ---------------
 * Implements the Singleton design pattern
 * ---------------.
 */
class ConfigBased implements CurrencyServiceContract
{
    /**
     * Conversion rate configurations in format:
     * ['currency from' => ['currency to' => 'conversion rate']].
     *
     * @var array
     */
    protected $conversionRates;

    /** @var array */
    private static $instances = [];

    /**
     * Hidden constructor in terms of Singleton pattern.
     */
    protected function __construct(array $conversionRates)
    {
        $this->setConversionRates($conversionRates);
    }

    /**
     * Returns new or existing instance of the Config class.
     */
    public static function getInstance(): ConfigBased
    {
        $className = static::class;

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = static::makeFromConfigurations();
        }

        return self::$instances[$className];
    }

    /** {@inheritdoc} */
    public function getConversionRates(): array
    {
        return $this->conversionRates;
    }

    /** {@inheritdoc} */
    public function setConversionRates(array $conversionRates)
    {
        $this->conversionRates = $conversionRates;
    }

    /** {@inheritdoc} */
    public function getConversionRateFromTo(string $fromCurrency, string $toCurrency): string
    {
        return $this->conversionRates[$fromCurrency][$toCurrency];
    }

    /** {@inheritdoc} */
    public function convert(string $amount, string $fromCurrency, string $toCurrency): string
    {
        // get conversion rate for "Current currency -> Target currency"
        $conversionRate = $this->getConversionRateFromTo($fromCurrency, $toCurrency);

        // calculate new amount based on conversion rate
        return MathServiceFactory::getInstance()->mul($amount, $conversionRate);
    }

    /** {@inheritdoc} */
    public function round(string $amount, string $currency): string
    {
        $mathService = MathServiceFactory::getInstance();

        // round accuracy
        $currencyPrecision = $this->getPrecisionForCurrency($currency);
        // output format
        $decimalSeparator = $this->getDecimalSeparatorForOutput();
        $thousandsSeparator = $this->getThousandsSeparatorForOutput();

        $currencySmallestItemMultiplier = (string) 10 ** $currencyPrecision;
        // convert operation's amount to a format in currency's smallest items
        $smallestItemAmountFormat = $mathService->mul($amount, $currencySmallestItemMultiplier);
        // round operation's amount in currency's smallest items
        $roundedSmallestItemAmountFormat = (string) ceil($smallestItemAmountFormat);
        // convert back operation's amount from smallest items to standard
        $roundedAmount = $mathService->div($roundedSmallestItemAmountFormat, $currencySmallestItemMultiplier);

        return number_format((float) $roundedAmount, $currencyPrecision, $decimalSeparator, $thousandsSeparator);
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

    /**
     * Creates new instance of ConfigBased currency service based on parameters stored in configs.
     */
    private static function makeFromConfigurations(): ConfigBased
    {
        $conversionRates = ConfigFactory::getInstance()->get('currencies.conversions');

        return new static($conversionRates);
    }

    /**
     * Returns precision defined in configurations for the passed currency.
     */
    private function getPrecisionForCurrency(string $currencyCode): int
    {
        return ConfigFactory::getInstance()->get("currencies.commission_precision.{$currencyCode}");
    }

    /**
     * Returns character to separate integer and decimal parts of commission fee for output.
     */
    private function getDecimalSeparatorForOutput(): string
    {
        return ConfigFactory::getInstance()->get('app.format.commission.decimal_separator');
    }

    /**
     * Returns character to separate thousands of commission fee for output.
     */
    private function getThousandsSeparatorForOutput(): string
    {
        return ConfigFactory::getInstance()->get('app.format.commission.thousands_separator');
    }
}
