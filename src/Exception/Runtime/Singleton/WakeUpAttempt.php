<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Runtime\Singleton;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * Singleton class instance wake up attempt exception.
 */
class WakeUpAttempt extends ValidationException
{
    /**
     * WakeUpAttempt constructor.
     *
     * @param int $code
     */
    public function __construct(string $className = '', $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $this->generateExceptionMessageWithClassName($className);

        parent::__construct($exceptionMessage, $code, $previous);
    }

    protected function generateExceptionMessageWithClassName(string $className): string
    {
        return "Singleton is not unserializable! Class: {$className}";
    }
}
