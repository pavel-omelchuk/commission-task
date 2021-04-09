<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Service\Input;

use PavelOmelchuk\CommissionTask\Contract\Service\Input\File\OperationParser as OperationParserContract;
use PavelOmelchuk\CommissionTask\Service\Input\Parser\CSV as CsvFileParser;

class Parser
{
    /**
     * Provides with a parser instance to read operations from input.
     */
    public static function getInstanceByInput(string $input): OperationParserContract
    {
        return new CsvFileParser($input);
    }
}
