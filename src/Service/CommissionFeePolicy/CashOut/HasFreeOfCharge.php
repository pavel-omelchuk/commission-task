<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut;

use PavelOmelchuk\CommissionTask\Contract\Entity\Operation as AbstractOperation;
use PavelOmelchuk\CommissionTask\Contract\Repository\Operation as OperationRepositoryContract;
use PavelOmelchuk\CommissionTask\Contract\Service\Calendar as CalendarServiceContract;
use PavelOmelchuk\CommissionTask\Contract\Service\CommissionFeePolicy as CommissionFeePolicyContract;
use PavelOmelchuk\CommissionTask\Contract\Service\Math as MathContract;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Factory\Repository\Operation as OperationRepositoryFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Calendar as CalendarServiceFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Math as MathServiceFactory;
use PavelOmelchuk\CommissionTask\Model\Operation;

/**
 * HasFreeOfCharge policy handler.
 * --------------------------------
 * Default commission fee - "X"% from cash out amount.
 *
 * "Y (specified currency)" per week (from monday to sunday) is free of charge.
 *
 * If total cash out amount is exceeded - commission is calculated only from exceeded amount
 * (that is, for "Y (specified currency)" there is still no commission fee).
 *
 * This discount is applied only for first "Z" cash out operations per week for each user
 * - for forth and other operations commission is calculated by default rules ("X"%)
 * - rule about "Y (specified currency)" is applied only for first three cash out operations.
 * --------------------------------
 */
class HasFreeOfCharge implements CommissionFeePolicyContract
{
    const POLICY_NAME = 'has_free_of_charge';

    /** @var MathContract */
    private $mathService;

    /** @var CalendarServiceContract */
    private $calendarService;

    /** @var OperationRepositoryContract */
    private $operationRepository;

    /** @var string */
    private $feePercent;

    /** @var string */
    private $freeOfChargeOperationsPerWeekLimitation;

    /** @var AmountAndCurrency */
    private $freeOfChargeLimitation;

    /**
     * HasFreeOfCharge constructor.
     *
     * Sets passed parameters and initializes services.
     */
    public function __construct(
        string $feePercent,
        string $freeOfChargeOperationsPerWeekLimitation,
        AmountAndCurrency $freeOfChargeLimitation
    ) {
        $this->feePercent = $feePercent;
        $this->freeOfChargeOperationsPerWeekLimitation = $freeOfChargeOperationsPerWeekLimitation;
        $this->freeOfChargeLimitation = $freeOfChargeLimitation;

        $this->loadMathService();
        $this->loadCalendarService();
        $this->loadOperationRepository();
    }

    /** {@inheritdoc} */
    public static function makeFromConfigurations(): CommissionFeePolicyContract
    {
        $policyConfigurations = ConfigFactory::getInstance()->get('commission_fee_policies.'.self::POLICY_NAME);

        // commission fee percent
        $feePercent = $policyConfigurations['percent'];
        // commission fee free of charge limitations
        $freeOfChargeOperationsPerWeekLimitation = $policyConfigurations['free_of_charge']['max_operations'];
        $freeOfChargeLimitation = new AmountAndCurrency(
            $policyConfigurations['free_of_charge']['amount'],
            $policyConfigurations['free_of_charge']['currency']
        );

        // create new instance with parameters defined in configurations
        return new static($feePercent, $freeOfChargeOperationsPerWeekLimitation, $freeOfChargeLimitation);
    }

    /** {@inheritdoc} */
    public function getFeeForOperation(Operation $operation): string
    {
        $operationCurrencyCode = $operation->getAmountAndCurrency()->getCurrency();

        // free of charge amount in $operation's currency
        $freeOfChargeBalance = $this->getFreeOfChargeBalanceForOperation($operation);

        // chargeable part of $operation's amount as a difference of $operation's amount and non-chargeable credits
        $chargeableAmount = $this->calculateChargeableAmountForOperation($operation, $freeOfChargeBalance);

        // calculates commission fee for chargeable part of operation's amount with declared percent
        $commissionFee = $this->calculateFeeForChargeableAmount($chargeableAmount);

        // round commission fee with app rounding policy
        return CurrencyServiceFactory::getInstance()->round($commissionFee, $operationCurrencyCode);
    }

    /**
     * Determines remaining free of charge balance on the $operation's week by $operation user's history.
     * Returns free of charge balance as an AmountAndCurrency instance converted to the $operation's currency.
     */
    private function getFreeOfChargeBalanceForOperation(Operation $operation): AmountAndCurrency
    {
        $operationCurrencyCode = $operation->getAmountAndCurrency()->getCurrency();
        // list of operations performed by the user in the week of the current $operation
        $userCashOutOperationHistory = $this->getCashOutOperationsByTheSameUserAndWeek($operation);

        // Does user has free of charge operations for this week
        if (!$this->isFreeOfChargeOperationsPerWeekLimitationExceeded($userCashOutOperationHistory)) {
            // AmountAndCurrency instance implies the SUM of all previous operations amount
            // converted to the $operation's currency
            $previousAmount = $this->getOperationsTotalAmount($userCashOutOperationHistory, $operationCurrencyCode);
            $freeOfChargeLimitationConverted = $this->freeOfChargeLimitation->toCurrency($operationCurrencyCode);

            // Is Free Of Charge total amount limit not exceeded on this week
            if (!$previousAmount->isAmountGreaterThen($freeOfChargeLimitationConverted)) {
                return $freeOfChargeLimitationConverted->sub($previousAmount);
            }
        }

        // empty free of charge balance
        return new AmountAndCurrency('0', $operationCurrencyCode);
    }

    /**
     * Returns chargeable part of $operation's amount based on free of charge balance.
     * Returns zero if free of charge balance is greater then operation's amount.
     */
    private function calculateChargeableAmountForOperation(
        Operation $operation,
        AmountAndCurrency $freeOfChargeBalance
    ): AmountAndCurrency {
        // operation's amount and currency
        $operationAmount = $operation->getAmountAndCurrency();
        // calculate chargeable part
        return $operationAmount->sub($freeOfChargeBalance);
    }

    /**
     * Multiplies $chargeableAmount by declared commission fee percent.
     */
    private function calculateFeeForChargeableAmount(AmountAndCurrency $chargeableAmount): string
    {
        return $this->mathService->mul($chargeableAmount->getAmount(), $this->feePercent);
    }

    /**
     * Returns array of all cash out operations performed by the $operation's user
     * on the same week as $operations's date.
     *
     * @return Operation[]
     */
    private function getCashOutOperationsByTheSameUserAndWeek(Operation $operation): array
    {
        // operation user
        $user = $operation->getUser();
        // operation date's string representation
        $date = $operation->getDate()->format($this->calendarService->getSupportedDateFormat());

        // operation date's week boundaries
        $weekStart = $this->calendarService->getStartDayOfWeekForDate($date);
        $weekEnd = $this->calendarService->getEndDayOfWeekForDate($date);
        // we should factor in only operations before the current
        $weekEnd = min($weekEnd, $operation->getDate());

        // list of all operations performed by the user on the same week as current operation
        $userOperationHistory = $this->operationRepository->getAllByUserBetweenDates($user, $weekStart, $weekEnd);

        // all operations with CASH_OUT type in the operation history
        return array_filter($userOperationHistory, static function (Operation $operation) {
            return $operation->getType() === AbstractOperation::TYPE_CASH_OUT;
        });
    }

    /**
     * Determines whether the free of charge operations per week limitation exceeded or not.
     */
    private function isFreeOfChargeOperationsPerWeekLimitationExceeded(array $weeklyCashOutOperationHistory): bool
    {
        $cashOutOperationsCount = count($weeklyCashOutOperationHistory);

        // whether count of operations this week more or equals to the declared limit
        return $cashOutOperationsCount >= ((int) $this->freeOfChargeOperationsPerWeekLimitation);
    }

    /**
     * Summarizes amounts of all passed operations.
     * Returns result as an AmountAndCurrency instance.
     * Result will be converted to the $targetCurrency.
     */
    private function getOperationsTotalAmount(array $operations, string $targetCurrency): AmountAndCurrency
    {
        // initialize container in target currency
        $totalAmount = new AmountAndCurrency('0', $targetCurrency);

        foreach ($operations as $operation) {
            $totalAmount = $totalAmount->add($operation->getAmountAndCurrency());
        }

        return $totalAmount;
    }

    /**
     * Initializes operation repository instance into class property.
     */
    private function loadOperationRepository()
    {
        $this->operationRepository = OperationRepositoryFactory::getInstance();
    }

    /**
     * Initializes calendar service instance into class property.
     */
    private function loadCalendarService()
    {
        $this->calendarService = CalendarServiceFactory::getInstance();
    }

    /**
     * Initializes math service instance into class property.
     */
    private function loadMathService()
    {
        $this->mathService = MathServiceFactory::getInstance();
    }
}
