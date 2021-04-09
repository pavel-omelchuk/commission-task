<?php

use PavelOmelchuk\CommissionTask\Contract\Entity\Currency;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashIn\HasMaxLimitation;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut\HasFreeOfCharge;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut\HasMinLimitation;

return [
    /*
    |--------------------------------------------------------------------------
    | Commission fee policies
    |--------------------------------------------------------------------------
    |
    | Here you may specify all implemented commission fee policies and their own configurations.
    | Here is no united structure for a policy configuration as every policy being handling by it's own handler-class.
    |
    */

    HasMaxLimitation::POLICY_NAME => [
        'percent' => '0.0003', // 0.03%
        'max' => [
            'amount' => '5',
            'currency' => Currency::CODE_EUR,
        ],
        'policy_handler' => HasMaxLimitation::class,
    ],

    HasMinLimitation::POLICY_NAME => [
        'percent' => '0.003', // 0.3%
        'min' => [
            'amount' => '0.5',
            'currency' => Currency::CODE_EUR,
        ],
        'policy_handler' => HasMinLimitation::class,
    ],

    HasFreeOfCharge::POLICY_NAME => [
        'percent' => '0.003', // 0.3%
        'free_of_charge' => [
            'amount' => '1000',
            'currency' => Currency::CODE_EUR,
            'max_operations' => '3',
        ],
        'policy_handler' => HasFreeOfCharge::class,
    ],
];
