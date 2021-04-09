<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Exception\Validation\User;

use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use Throwable;

/**
 * User Type validation exception.
 */
class InvalidType extends ValidationException
{
    /**
     * InvalidType constructor.
     *
     * @param int $code
     */
    public function __construct(string $userType = '', $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $this->generateExceptionMessageWithInvalidType($userType);

        parent::__construct($exceptionMessage, $code, $previous);
    }

    protected function generateExceptionMessageWithInvalidType(string $invalidUserType): string
    {
        return "Unsupported user type = {$invalidUserType}";
    }
}
