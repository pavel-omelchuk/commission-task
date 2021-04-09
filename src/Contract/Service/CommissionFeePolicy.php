<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Service;

use PavelOmelchuk\CommissionTask\Model\Operation;

/**
 * Interface CommissionFeePolicy.
 * Describes a commission fee policy's interface.
 * Implies a service to calculate commission fee for operations.
 */
interface CommissionFeePolicy
{
    /**
     * Creates instance of implemented class with parameters defined in configurations.
     */
    public static function makeFromConfigurations(): self;

    /**
     * Calculates commission fee for provided operation.
     * Calculation performs based on provided operation, it's user and his history.
     */
    public function getFeeForOperation(Operation $operation): string;
}
