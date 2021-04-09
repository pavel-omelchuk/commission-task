<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\Math;

use PavelOmelchuk\CommissionTask\Contract\Service\Math as MathContract;

/**
 * Class BCMath.
 * Math component implements all necessary math functionality with BC Math PHP extension.
 */
class BCMath implements MathContract
{
    /**
     * @var array
     */
    private static $instances = [];

    /**
     * Accuracy (number of decimal places) for all Math operations.
     *
     * @var int
     */
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    /** {@inheritdoc} */
    public function comp(string $leftOperand, string $rightOperand): int
    {
        return bccomp($leftOperand, $rightOperand, $this->scale);
    }

    /** {@inheritdoc} */
    public function min(string $leftOperand, string $rightOperand): string
    {
        $comparisonResult = $this->comp($leftOperand, $rightOperand);

        // return $leftOperand if $rightOperand is greater
        if ($comparisonResult === static::COMP_GREATER_RIGHT) {
            return $leftOperand;
        }

        // return $rightOperand if $leftOperand is greater OR operands are equal
        return $rightOperand;
    }

    /** {@inheritdoc} */
    public function max(string $leftOperand, string $rightOperand): string
    {
        $comparisonResult = $this->comp($leftOperand, $rightOperand);

        // return $leftOperand if $leftOperand is greater
        if ($comparisonResult === static::COMP_GREATER_LEFT) {
            return $leftOperand;
        }

        // return $rightOperand if $rightOperand is greater OR operands are equal
        return $rightOperand;
    }

    /** {@inheritdoc} */
    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    /** {@inheritdoc} */
    public function sub(string $leftOperand, string $rightOperand): string
    {
        return bcsub($leftOperand, $rightOperand, $this->scale);
    }

    /** {@inheritdoc} */
    public function mul(string $leftOperand, string $rightOperand): string
    {
        return bcmul($leftOperand, $rightOperand, $this->scale);
    }

    /** {@inheritdoc} */
    public function div(string $leftOperand, string $rightOperand): string
    {
        return bcdiv($leftOperand, $rightOperand, $this->scale);
    }
}
