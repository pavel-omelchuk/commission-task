<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Service;

use PavelOmelchuk\CommissionTask\Contract\Service\Currency as CurrencyServiceContract;
use PavelOmelchuk\CommissionTask\Service\Currency\ConfigBased as ConfigBasedCurrencyService;

/**
 * Class Currency.
 */
class Currency
{
    /**
     * Provides with a Currency Service instance.
     */
    public static function getInstance(): CurrencyServiceContract
    {
        return ConfigBasedCurrencyService::getInstance();
    }
}
