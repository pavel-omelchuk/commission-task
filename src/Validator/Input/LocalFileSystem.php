<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Validator\Input;

use PavelOmelchuk\CommissionTask\Contract\Validator\Input as InputValidatorContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;

/**
 * Class LocalFileSystem.
 * Provides a set of methods to validate input as a file in local file system.
 */
class LocalFileSystem implements InputValidatorContract
{
    /** {@inheritdoc} */
    public function isValidInput(string $input): bool
    {
        // check whether the passed $input is a correct path to valid file.
        if (is_file($input) && is_readable($input)) {
            // get supported input file extension list
            $supportedFileExtensions = ConfigFactory::getInstance()->get('app.supported.input.file.extensions');
            // get extension of target file passed as an input
            $targetFileExtension = pathinfo($input, PATHINFO_EXTENSION);

            if (in_array($targetFileExtension, $supportedFileExtensions, true)) {
                return true;
            }
        }

        return false;
    }
}
