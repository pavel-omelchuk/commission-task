<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Entity;

/**
 * Operation entity interface.
 */
interface Operation
{
    const TYPE_CASH_IN = 'cash_in';

    const TYPE_CASH_OUT = 'cash_out';
}
