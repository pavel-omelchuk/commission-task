<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\Config;

use PavelOmelchuk\CommissionTask\Contract\Service\Config as ConfigAccessorContract;
use PavelOmelchuk\CommissionTask\Exception\Runtime\Singleton\WakeUpAttempt as SingletonWakeUpAttemptException;

/**
 * Provides a dot-access to app configuration.
 * ---------------
 * Implements the Singleton design pattern
 * ---------------.
 */
class InFile implements ConfigAccessorContract
{
    /**
     * Loaded configurations.
     *
     * @var array
     */
    protected $configurations = [];

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * Hidden constructor in terms of Singleton pattern.
     */
    protected function __construct()
    {
        $this->loadConfigurationFromFiles();
    }

    /**
     * Returns new or existing instance of the Config class.
     */
    public static function getInstance(): InFile
    {
        $className = static::class;

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new static();
        }

        return self::$instances[$className];
    }

    /**
     * Returns configuration's value founded by passed $path.
     * Returns $default value if configuration by passed $path was not found.
     *
     * First section of the $path should be a target config's file name.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        // copy configuration repository
        $configuration = $this->configurations;

        if (!empty($path)) {
            // split $path onto sections by dot char
            $sections = explode('.', $path);

            foreach ($sections as $section) {
                // narrow the search by $section key or return $default value if $key doesn't exist in configurations
                if (isset($configuration[$section])) {
                    $configuration = $configuration[$section];
                } else {
                    return $default;
                }
            }
        }

        return $configuration;
    }

    /**
     * Stores new configuration value by specified $path.
     *
     * @param $value
     */
    public function set(string $path, $value)
    {
        $configurations = &$this->configurations;

        // split $path onto sections by dot char
        $sections = explode('.', $path);

        while (count($sections) > 1) {
            // get next path section
            $section = array_shift($sections);

            // initialize array on current path
            if (!isset($configurations[$section]) || !is_array($configurations[$section])) {
                $configurations[$section] = [];
            }

            // go to the next section
            $configurations = &$configurations[$section];
        }

        $lastSection = array_shift($sections);
        // set last section
        $configurations[$lastSection] = $value;
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
     * Loads all files from "project_root/config" directory to the $configuration class property.
     * Provides a dot-access to configuration items.
     * Config file name implies a first-level key.
     */
    private function loadConfigurationFromFiles()
    {
        $configDirPath = getcwd().DIRECTORY_SEPARATOR.'config';

        foreach (scandir($configDirPath) as $directoryItem) {
            $directoryItemFullPath = $configDirPath.DIRECTORY_SEPARATOR.$directoryItem;

            if (is_file($directoryItemFullPath) && pathinfo($directoryItem, PATHINFO_EXTENSION) === 'php') {
                $fileName = pathinfo($directoryItem, PATHINFO_FILENAME);

                $this->configurations[$fileName] = require $directoryItemFullPath;
            }
        }
    }
}
