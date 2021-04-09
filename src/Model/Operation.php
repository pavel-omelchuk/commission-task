<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Model;

use DateTime;
use Exception;
use PavelOmelchuk\CommissionTask\Contract\Entity\Operation as OperationEntityContract;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Exception\Validation\Operation\InvalidDate as InvalidOperationDateException;
use PavelOmelchuk\CommissionTask\Exception\Validation\Operation\InvalidType as InvalidOperationTypeException;
use PavelOmelchuk\CommissionTask\Exception\Validation\ValidationException;
use PavelOmelchuk\CommissionTask\Factory\Service\CommissionFeePolicy as CommissionFeePolicyFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Factory\Validator\Operation as OperationValidatorFactory;

/**
 * Describes Operation entity.
 */
class Operation implements OperationEntityContract
{
    /** @var DateTime */
    protected $date;

    /** @var string */
    protected $type;

    /** @var User */
    protected $user;

    /** @var AmountAndCurrency */
    protected $amountAndCurrency;

    /**
     * Operation constructor.
     * Builds new Operation instance from raw string data.
     *
     * @throws ValidationException|Exception
     */
    public function __construct(
        string $dateStr,
        string $operationType,
        User $user,
        AmountAndCurrency $amountAndCurrency
    ) {
        $this->setDate($dateStr);
        $this->setType($operationType);
        $this->setUser($user);
        $this->setAmountAndCurrency($amountAndCurrency);
    }

    /**
     * Defines Commission Fee Policy for the operation based on app configurations.
     * Returns commission fee calculated by defined policy.
     */
    public function getCommissionFee(): string
    {
        // get commission fee policy name from configuration for the operation
        $policyName = $this->getCommissionFeePolicyName();
        // get commission fee policy instance
        $commissionFeePolicy = CommissionFeePolicyFactory::getInstanceByName($policyName);

        // calculate commission fee for the operation
        return $commissionFeePolicy->getFeeForOperation($this);
    }

    /**
     * Converts passed string $date to DateTime stores it to the class property.
     * Throws an ValidationException in case of invalid $date.
     *
     * @throws InvalidOperationDateException|Exception
     */
    public function setDate(string $date)
    {
        if (!OperationValidatorFactory::getInstance()->isDateValid($date)) {
            throw new InvalidOperationDateException($date);
        }

        $this->date = new DateTime($date);
    }

    /**
     * Sets operation type or throws an exception if passed $type is not valid.
     *
     * @throws InvalidOperationTypeException
     */
    public function setType(string $type)
    {
        if (!OperationValidatorFactory::getInstance()->isOperationTypeValid($type)) {
            throw new InvalidOperationTypeException($type);
        }

        $this->type = $type;
    }

    /**
     * Sets operation's user.
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Sets operation's amount and currency code.
     */
    public function setAmountAndCurrency(AmountAndCurrency $amountAndCurrency)
    {
        $this->amountAndCurrency = $amountAndCurrency;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAmountAndCurrency(): AmountAndCurrency
    {
        return $this->amountAndCurrency;
    }

    /**
     * Returns name of a commission fee policy defined for such operation in configurations.
     */
    protected function getCommissionFeePolicyName(): string
    {
        $operationType = $this->getType();
        $operationUserType = $this->user->getType();

        return ConfigFactory::getInstance()
            ->get("commission_fee_operations.operation_type.{$operationType}.user_type.{$operationUserType}.policy");
    }
}
