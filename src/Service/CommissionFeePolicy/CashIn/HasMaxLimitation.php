<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashIn;

use PavelOmelchuk\CommissionTask\Contract\Service\CommissionFeePolicy as CommissionFeePolicyContract;
use PavelOmelchuk\CommissionTask\Contract\Service\Math as MathContract;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Math as MathServiceFactory;
use PavelOmelchuk\CommissionTask\Model\Operation;

/**
 * HasMaxLimitation policy handler.
 * --------------------------------
 * Commission fee - "X"% from total amount, but no more than "Y (declared currency)".
 * --------------------------------.
 */
class HasMaxLimitation implements CommissionFeePolicyContract
{
    const POLICY_NAME = 'has_max_limitation';

    /** @var MathContract */
    private $mathService;

    /** @var string */
    private $feePercent;

    /** @var AmountAndCurrency */
    private $maxLimitationAmountAndCurrency;

    /**
     * HasMaxLimitation constructor.
     *
     * Sets passed parameters and initializes services.
     */
    public function __construct(string $feePercent, AmountAndCurrency $maxLimitationAmountAndCurrency)
    {
        $this->feePercent = $feePercent;
        $this->maxLimitationAmountAndCurrency = $maxLimitationAmountAndCurrency;

        $this->loadMathService();
    }

    /** {@inheritdoc} */
    public static function makeFromConfigurations(): CommissionFeePolicyContract
    {
        $policyConfigurations = ConfigFactory::getInstance()->get('commission_fee_policies.'.self::POLICY_NAME);

        // commission fee percent
        $feePercent = $policyConfigurations['percent'];

        // commission fee max limitation
        $maxLimitationAmountAndCurrency = new AmountAndCurrency(
            $policyConfigurations['max']['amount'],
            $policyConfigurations['max']['currency']
        );

        // create new instance with parameters defined in configurations
        return new static($feePercent, $maxLimitationAmountAndCurrency);
    }

    /** {@inheritdoc} */
    public function getFeeForOperation(Operation $operation): string
    {
        $operationAmount = $operation->getAmountAndCurrency()->getAmount();
        $operationCurrencyCode = $operation->getAmountAndCurrency()->getCurrency();

        // calculate fee with declared percent
        $calculatedCommissionFee = $this->calculateFeeForOperationAmount($operationAmount);
        // commission fee max limitation converted to current operation's currency
        $maxLimitation = $this->getMaxLimitationConvertedToCurrency($operationCurrencyCode);

        // calculated commission fee OR limitation (in current currency) if declared limit exceeded
        $commissionFee = $this->mathService->min($calculatedCommissionFee, $maxLimitation);

        // round commission fee with app rounding policy
        return CurrencyServiceFactory::getInstance()->round($commissionFee, $operationCurrencyCode);
    }

    /**
     * Multiplies the operation amount by the Commission Fee percent.
     */
    private function calculateFeeForOperationAmount(string $operationAmount): string
    {
        return $this->mathService->mul($operationAmount, $this->feePercent);
    }

    /**
     * Converts declared max limitation for operation into specified currency.
     */
    private function getMaxLimitationConvertedToCurrency(string $currencyCode): string
    {
        $maxLimitationConverted = $this->maxLimitationAmountAndCurrency->toCurrency($currencyCode);

        return $maxLimitationConverted->getAmount();
    }

    /**
     * Initializes math service instance into class property.
     */
    private function loadMathService()
    {
        $this->mathService = MathServiceFactory::getInstance();
    }
}
