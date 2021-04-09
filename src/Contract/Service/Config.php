<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Service;

/**
 * Interface Config
 * Describes a configuration accessor's interface.
 */
interface Config
{
    /**
     * Returns configuration's value founded by passed $path.
     * Returns $default value if configuration by passed $path was not found.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $path, $default = null);

    /**
     * Stores new configuration value by specified $path.
     *
     * @param $value
     */
    public function set(string $path, $value);
}
