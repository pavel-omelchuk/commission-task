<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Service;

use PavelOmelchuk\CommissionTask\Contract\Service\CommissionFeePolicy as CommissionFeePolicyContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;

/**
 * Class CommissionFeePolicy.
 *
 * Provides with instances of Commission Fee Policy resolvers.
 */
class CommissionFeePolicy
{
    /**
     * Provides with a Commission Fee Policy instance based on passed policy name.
     */
    public static function getInstanceByName(string $commissionFeePolicyName): CommissionFeePolicyContract
    {
        // get full name of target policy handler class
        $policyHandlerClassName = ConfigFactory::getInstance()
            ->get("commission_fee_policies.$commissionFeePolicyName.policy_handler");

        // builds new instance with parameters defined in configurations
        return $policyHandlerClassName::makeFromConfigurations();
    }
}
