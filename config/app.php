<?php

use PavelOmelchuk\CommissionTask\Contract\Entity\Currency;
use PavelOmelchuk\CommissionTask\Contract\Entity\Operation;
use PavelOmelchuk\CommissionTask\Contract\Entity\User;

return [
    'supported' => [
        // here you may specify supported input formats
        'input' => [
            'file' => [
                'extensions' => [
                    'csv',
                ]
            ]
        ],
        // list of currency codes supported by the application
        'currencies' => [
            Currency::CODE_EUR,
            Currency::CODE_USD,
            Currency::CODE_JPY,
        ],
        // list of operation types supported by the application
        'operation_types' => [
            Operation::TYPE_CASH_IN,
            Operation::TYPE_CASH_OUT,
        ],
        // list of user types supported by the application
        'user_types' => [
            User::TYPE_NATURAL,
            User::TYPE_LEGAL,
        ],
    ],

    'format' => [
        // specify format for all dates in the App
        'date' => 'Y-m-d',
        // specify format for calculated commission fee output
        'commission' => [
            'decimal_separator' => '.',
            'thousands_separator' => '',
        ],
    ],
];