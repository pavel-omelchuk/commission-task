<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Repository;

use PavelOmelchuk\CommissionTask\Contract\Repository\Operation as OperationRepositoryContract;
use PavelOmelchuk\CommissionTask\Repository\Operation\InMemory as InMemoryOperationRepository;

/**
 * Operation factory.
 */
class Operation
{
    /**
     * Provides with an Operation Repository instance.
     */
    public static function getInstance(): OperationRepositoryContract
    {
        return InMemoryOperationRepository::getInstance();
    }
}
