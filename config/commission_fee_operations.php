<?php

use PavelOmelchuk\CommissionTask\Contract\Entity\User;
use PavelOmelchuk\CommissionTask\Contract\Entity\Operation;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashIn\HasMaxLimitation;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut\HasFreeOfCharge;
use PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut\HasMinLimitation;

return [
    /*
    |--------------------------------------------------------------------------
    | Commission fee operations
    |--------------------------------------------------------------------------
    |
    | Here you may specify commission fee policy for each operation type by user type.
    |
    */

    'operation_type' => [
        Operation::TYPE_CASH_IN => [
            'user_type' => [
                User::TYPE_NATURAL => [
                    'policy' => HasMaxLimitation::POLICY_NAME,
                ],

                User::TYPE_LEGAL => [
                    'policy' => HasMaxLimitation::POLICY_NAME,
                ]
            ]
        ],

        Operation::TYPE_CASH_OUT => [
            'user_type' => [
                User::TYPE_NATURAL => [
                    'policy' => HasFreeOfCharge::POLICY_NAME,
                ],

                User::TYPE_LEGAL => [
                    'policy' => HasMinLimitation::POLICY_NAME,
                ]
            ]
        ]
    ]
];
