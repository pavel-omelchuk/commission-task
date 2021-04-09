<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\CommissionFeePolicy\CashOut;

use PavelOmelchuk\CommissionTask\Contract\Service\CommissionFeePolicy as CommissionFeePolicyContract;
use PavelOmelchuk\CommissionTask\Contract\Service\Math as MathContract;
use PavelOmelchuk\CommissionTask\DataStructure\AmountAndCurrency;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Currency as CurrencyServiceFactory;
use PavelOmelchuk\CommissionTask\Factory\Service\Math as MathServiceFactory;
use PavelOmelchuk\CommissionTask\Model\Operation;

/**
 * HasMinLimitation policy handler.
 * --------------------------------
 * Commission fee - "X"% from amount, but not less than "Y (declared currency)" for operation.
 * --------------------------------.
 */
class HasMinLimitation implements CommissionFeePolicyContract
{
    const POLICY_NAME = 'has_min_limitation';

    /** @var MathContract */
    private $mathService;

    /** @var string */
    private $feePercent;

    /** @var AmountAndCurrency */
    private $minLimitationAmountAndCurrency;

    /**
     * HasMinLimitation constructor.
     *
     * Sets passed parameters and initializes services.
     */
    public function __construct(string $feePercent, AmountAndCurrency $minLimitationAmountAndCurrency)
    {
        $this->feePercent = $feePercent;
        $this->minLimitationAmountAndCurrency = $minLimitationAmountAndCurrency;

        $this->loadMathService();
    }

    /** {@inheritdoc} */
    public static function makeFromConfigurations(): CommissionFeePolicyContract
    {
        $policyConfigurations = ConfigFactory::getInstance()->get('commission_fee_policies.'.self::POLICY_NAME);

        // commission fee percent
        $feePercent = $policyConfigurations['percent'];

        // commission fee min limitation
        $minLimitationAmountAndCurrency = new AmountAndCurrency(
            $policyConfigurations['min']['amount'],
            $policyConfigurations['min']['currency']
        );

        // create new instance with parameters defined in configurations
        return new static($feePercent, $minLimitationAmountAndCurrency);
    }

    /** {@inheritdoc} */
    public function getFeeForOperation(Operation $operation): string
    {
        $operationAmount = $operation->getAmountAndCurrency()->getAmount();
        $operationCurrencyCode = $operation->getAmountAndCurrency()->getCurrency();

        // calculate fee with declared percent
        $calculatedCommissionFee = $this->calculateFeeForOperationAmount($operationAmount);
        // commission fee min limitation converted to current operation's currency
        $minLimitation = $this->getMinLimitationConvertedToCurrency($operationCurrencyCode);

        // calculated commission fee OR min limitation (in current currency) if declared limit not reached
        $commissionFee = $this->mathService->max($calculatedCommissionFee, $minLimitation);

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
     * Converts declared min limitation for operation into specified currency.
     */
    private function getMinLimitationConvertedToCurrency(string $currencyCode): string
    {
        $minLimitationConverted = $this->minLimitationAmountAndCurrency->toCurrency($currencyCode);

        return $minLimitationConverted->getAmount();
    }

    /**
     * Initializes math service instance into class property.
     */
    private function loadMathService()
    {
        $this->mathService = MathServiceFactory::getInstance();
    }
}
