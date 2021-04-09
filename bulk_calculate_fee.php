<?php

namespace PavelOmelchuk\CommissionTask;

require_once 'vendor/autoload.php';

use PavelOmelchuk\CommissionTask\Factory\Repository\Operation as OperationRepositoryFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Input\Parser as InputParserFactory;
use PavelOmelchuk\CommissionTask\Exception\Validation\Input\InvalidArgumentCount as InvalidArgumentCountException;
use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;

try {
    // check for input data existence
    if (empty($argv[1])) {
        throw new InvalidArgumentCountException('You should provide path to a file with input data.');
    }

    // build full path to the file
    $inputFilePath = realpath($argv[1]);

    // get file parser instance
    $fileParser = InputParserFactory::getInstanceByInput($inputFilePath);
    // get operation repository instance
    $operationRepository = OperationRepositoryFactory::getInstance();

    // iterate all operations in file
    foreach ($fileParser->operations() as $operation) {
        echo $operation->getCommissionFee() . PHP_EOL;
        // add processed operation to the history
        $operationRepository->save($operation);
    }

} catch (ValidationException $validationException) {
    $validationException->log();
}
