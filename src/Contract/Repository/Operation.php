<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Repository;

use PavelOmelchuk\CommissionTask\Model\Operation as OperationModel;
use PavelOmelchuk\CommissionTask\Model\User as UserModel;

/**
 * Interface Operation.
 * Provides a set of methods to store and find Operation in a storage.
 */
interface Operation
{
    /**
     * Stores Operation instance in the storage.
     *
     * @return mixed
     */
    public function save(OperationModel $operation);

    /**
     * Returns all Operation instances from the storage related to the passed User.
     *
     * @return OperationModel[]
     */
    public function getAllByUser(UserModel $user): array;

    /**
     * Returns all Operation instances from the storage related to the passed User and performed between 2 dates.
     * $from and $to dates are included in the search period.
     *
     * @return OperationModel[]
     */
    public function getAllByUserBetweenDates(UserModel $user, string $dateFrom, string $dateTo): array;
}
