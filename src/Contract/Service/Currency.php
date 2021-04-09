<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Service;

/**
 * Interface Currency.
 * Provides a set of useful methods to work with currencies.
 */
interface Currency
{
    /**
     * Returns conversion rates array in format ['from currency' => ['to currency' => 'conversion rate']].
     */
    public function getConversionRates(): array;

    /**
     * Overrides current conversion rates with passed.
     */
    public function setConversionRates(array $conversionRates);

    /**
     * Returns conversion rate between passed currencies.
     */
    public function getConversionRateFromTo(string $fromCurrency, string $toCurrency): string;

    /**
     * Converts passed $amount with conversion rate between $fromCurrency -> $toCurrency.
     */
    public function convert(string $amount, string $fromCurrency, string $toCurrency): string;

    /**
     * Rounds passed amount to the smallest item of passed $currency.
     */
    public function round(string $amount, string $currency): string;
}
