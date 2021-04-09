<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Validator\User;

use PavelOmelchuk\CommissionTask\Contract\Validator\User;
use PavelOmelchuk\CommissionTask\Exception\Runtime\Singleton\WakeUpAttempt as SingletonWakeUpAttemptException;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;

/**
 * Provides a set of methods to validate user input data.
 * ---------------
 * Implements the Singleton design pattern
 * ---------------.
 */
class InMemory implements User
{
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
    public function isIdValid(string $userId): bool
    {
        // could not check for existence due to In Memory storage
        return is_numeric($userId);
    }

    /** {@inheritdoc} */
    public function isTypeValid(string $userType): bool
    {
        $supportedUserTypes = ConfigFactory::getInstance()->get('app.supported.user_types');

        return in_array($userType, $supportedUserTypes, true);
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
}
