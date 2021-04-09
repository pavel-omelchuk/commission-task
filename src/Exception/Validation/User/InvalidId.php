<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation\User;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * User ID validation exception.
 */
class InvalidId extends ValidationException
{
    /**
     * InvalidId constructor.
     *
     * @param int $code
     */
    public function __construct(string $userId = '', $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $this->generateExceptionMessageWithInvalidId($userId);

        parent::__construct($exceptionMessage, $code, $previous);
    }

    protected function generateExceptionMessageWithInvalidId(string $invalidUserId): string
    {
        return "Invalid user ID = {$invalidUserId}";
    }
}
