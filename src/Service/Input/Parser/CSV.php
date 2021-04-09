<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\Input\Parser;

use Generator;
use PavelOmelchuk\CommissionTask\Contract\Service\Input\File\OperationParser as OperationParserContract;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Exception\Validation\Input\InvalidFilePath as InvalidFilePathException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Input\InvalidRow as InvalidRowException;
use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use PavelOmelchuk\CommissionTask\Factory\Validator\Input as InputFactory;
use PavelOmelchuk\CommissionTask\Model\Operation;
use PavelOmelchuk\CommissionTask\Model\User;

/**
 * Class CSV.
 * Iterates row by row through a CSV file and produces Operation instances.
 */
class CSV implements OperationParserContract
{
    /**
     * Target CSV file.
     *
     * @var string
     */
    protected $filePath;

    public function __construct(string $filePath)
    {
        $this->setPathToFile($filePath);
    }

    /** {@inheritdoc} */
    public function getPathToFile(): string
    {
        return $this->filePath;
    }

    /** {@inheritdoc} */
    public function setPathToFile(string $pathToFile)
    {
        if (!InputFactory::getInstance()->isValidInput($pathToFile)) {
            throw new InvalidFilePathException($pathToFile);
        }

        $this->filePath = $pathToFile;
    }

    /** {@inheritdoc} */
    public function operations(): Generator
    {
        // open file
        $file = fopen($this->filePath, 'rb');

        // read file line by line until EOF
        while (($values = fgetcsv($file)) !== false) {
            // throw exception if not enough values in row
            if (count($values) < 6) {
                throw new InvalidRowException(implode(',', $values));
            }

            yield $this->makeNewOperationInstanceFromRawInput($values);
        }

        // close file
        fclose($file);
    }

    /**
     * Makes new instance of Operation data structure from $rawInput (single row columns).
     * Throws exception if at least one of values is invalid.
     *
     * @throws ValidationException
     */
    private function makeNewOperationInstanceFromRawInput(array $rawInput): Operation
    {
        $user = new User($rawInput[1], $rawInput[2]);
        $amountAndCurrency = new AmountAndCurrency($rawInput[4], $rawInput[5]);

        return new Operation($rawInput[0], $rawInput[3], $user, $amountAndCurrency);
    }
}
