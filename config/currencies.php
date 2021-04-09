<?php

use PavelOmelchuk\CommissionTask\Contract\Entity\Currency;

return [
    // here you may specify
    'commission_precision' => [
        Currency::CODE_EUR => 2,
        Currency::CODE_USD => 2,
        Currency::CODE_JPY => 0,
    ],

    // list of defined conversion rates for all currencies
    // upper level currency code means <from>
    // lower level currency code means <to>
    'conversions' => [
        Currency::CODE_EUR => [
            Currency::CODE_EUR => '1',
            Currency::CODE_USD => '1.1497',
            Currency::CODE_JPY => '129.53',
        ],
        Currency::CODE_USD => [
            Currency::CODE_USD => '1',
            Currency::CODE_EUR => '0.8697',
            Currency::CODE_JPY => '112.6522',
        ],
        Currency::CODE_JPY => [
            Currency::CODE_JPY => '1',
            Currency::CODE_EUR => '0.0077',
            Currency::CODE_USD => '0.0088',
        ],
    ],
];
