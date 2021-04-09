<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Repository\Operation;

use PavelOmelchuk\CommissionTask\Contract\Repository\Operation as OperationRepositoryContract;
use PavelOmelchuk\CommissionTask\Exception\Runtime\Singleton\WakeUpAttempt as SingletonWakeUpAttemptException;
use PavelOmelchuk\CommissionTask\Factory\Service\Calendar as CalendarServiceFactory;
use PavelOmelchuk\CommissionTask\Model\Operation as OperationModel;
use PavelOmelchuk\CommissionTask\Model\User as UserModel;

/**
 * Class-repository InMemory.
 * -----------------------------------------------------------------------------
 * Implements the Singleton design pattern
 * -----------------------------------------------------------------------------
 * Operations are indexed (grouped) by composite Key "User ID -> Operation Date".
 * In other words: storage array has 2 nesting levels: User ID and then Operation Date.
 *
 * Example:
 * [
 *      '1' => [  // user id
 *          '2021-04-05' => [ // date
 *              ...operations...
 *          ]
 *      ]
 * ]
 * -----------------------------------------------------------------------------
 */
class InMemory implements OperationRepositoryContract
{
    /**
     * In-Memory storage.
     *
     * @var array
     */
    protected $storage = [];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * Hidden constructor in terms of Singleton pattern.
     */
    protected function __construct()
    {
    }

    /**
     * Returns new or existing instance of the InMemory class.
     */
    public static function getInstance(): InMemory
    {
        $className = static::class;

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new static();
        }

        return self::$instances[$className];
    }

    /** {@inheritdoc} */
    public function save(OperationModel $operation)
    {
        // get operation user id
        $operationUserId = $operation->getUser()->getId();
        // get date format supported by the App
        $supportedDateFormat = CalendarServiceFactory::getInstance()->getSupportedDateFormat();
        // get operation date in supported format
        $operationDate = $operation->getDate()->format($supportedDateFormat);

        $this->storage[$operationUserId][$operationDate][] = $operation;
    }

    /** {@inheritdoc} */
    public function getAllByUser(UserModel $user): array
    {
        // get all operations grouped by date for the $user OR empty array if no operations saved for the user.
        $operationsByUserId = $this->storage[$user->getId()] ?? [];

        // return plain single level array of Operation instances
        return $this->flattenMultidimensionalOperationsArray($operationsByUserId);
    }

    /** {@inheritdoc} */
    public function getAllByUserBetweenDates(UserModel $user, string $dateFrom, string $dateTo): array
    {
        // temp container
        $operations = [];
        // get all operations grouped by date for the $user OR empty array if no operations saved for the user.
        $userOperationsByDate = $this->storage[$user->getId()] ?? [];
        // get period of dates between $dateFrom and $dateTo (both included)

        if ($userOperationsByDate) {
            // array of dates (string) between $dateFrom and $dateTo (both included)
            $datePeriod = CalendarServiceFactory::getInstance()->getDateListBetweenPassedDates($dateFrom, $dateTo);

            // get all operations having date in specified period
            foreach ($datePeriod as $date) {
                // if user has operations in this $date
                if (array_key_exists($date, $userOperationsByDate)) {
                    // put array of operations in $date to $operations container
                    $operations[] = $userOperationsByDate[$date];
                }
            }

            // return plain single level array of Operation instances
            return $this->flattenMultidimensionalOperationsArray($operations);
        }

        // return empty array
        return $operations;
    }

    /**
     * Prevent object to be restored from a string.
     *
     * @throws SingletonWakeUpAttemptException
     */
    public function __wakeup()
    {
        throw new SingletonWakeUpAttemptException(static::class);
    }

    /**
     * Hidden __clone method in terms of Singleton pattern.
     */
    protected function __clone()
    {
    }

    /**
     * Transforms multidimensional array of operations with several nesting levels into plain single-level array.
     *
     * @param array $operations - multidimensional array with several nesting levels
     *
     * @return OperationModel[]
     */
    private function flattenMultidimensionalOperationsArray(array $operations): array
    {
        // temporary container
        $flattenOperations = [];

        // recursively walk through all operations regardless of nesting level and copy to the temp container
        array_walk_recursive(
            $operations,
            static function (OperationModel $operation) use (&$flattenOperations) {
                $flattenOperations[] = $operation;
            }
        );

        return $flattenOperations;
    }
}
