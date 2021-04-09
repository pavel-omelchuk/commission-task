<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Service;

use PavelOmelchuk\CommissionTask\Contract\Service\Math as MathComponentContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Service\Math\BCMath;

/**
 * Math service factory.
 */
class Math
{
    /**
     * Provides with a Math Component instance.
     */
    public static function getInstance(): MathComponentContract
    {
        $mathServiceScale = ConfigFactory::getInstance()->get('math.scale');

        return new BCMath($mathServiceScale);
    }
}
