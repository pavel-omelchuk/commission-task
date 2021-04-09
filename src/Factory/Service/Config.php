<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Service;

use PavelOmelchuk\CommissionTask\Contract\Service\Config as ConfigAccessorContract;
use PavelOmelchuk\CommissionTask\Service\Config\InFile as InFileConfig;

/**
 * Config factory.
 */
class Config
{
    /**
     * Provides with a Config accessor instance.
     */
    public static function getInstance(): ConfigAccessorContract
    {
        return InFileConfig::getInstance();
    }
}
