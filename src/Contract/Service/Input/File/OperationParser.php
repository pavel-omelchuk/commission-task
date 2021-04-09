<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Service\Input\File;

use Generator;
use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use PavelOmelchuk\CommissionTask\Model\Operation;

/**
 * Interface OperationParser
 * Describes an input file parser's interface.
 * Provides with a method to iterate file row by row producing Operations.
 */
interface OperationParser
{
    /**
     * Returns full path to the input file.
     */
    public function getPathToFile(): string;

    /**
     * Sets full path to the input file.
     *
     * @throws ValidationException
     */
    public function setPathToFile(string $pathToFile);

    /**
     * Iterates rows in the input file one by one.
     * Returns each row as an Operation instance.
     *
     * @return Generator<Operation>
     *
     * @throws ValidationException
     */
    public function operations(): Generator;
}
