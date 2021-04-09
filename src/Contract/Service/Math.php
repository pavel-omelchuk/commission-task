<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Service;

/**
 * Interface Math
 * Describes a math component's interface.
 */
interface Math
{
    const COMP_GREATER_LEFT = 1;

    const COMP_EQUAL = 0;

    const COMP_GREATER_RIGHT = -1;

    /**
     * Compares 2 numbers.
     */
    public function comp(string $leftOperand, string $rightOperand): int;

    /**
     * Returns minimum value of the two passed.
     */
    public function min(string $leftOperand, string $rightOperand): string;

    /**
     * Returns maximum value of the two passed.
     */
    public function max(string $leftOperand, string $rightOperand): string;

    /**
     * Sums up the 2 numbers.
     */
    public function add(string $leftOperand, string $rightOperand): string;

    /**
     * Subtracts a $rightOperand from a $leftOperand.
     */
    public function sub(string $leftOperand, string $rightOperand): string;

    /**
     * Multiplies 2 numbers.
     */
    public function mul(string $leftOperand, string $rightOperand): string;

    /**
     * Divides a $leftOperand by $rightOperand.
     */
    public function div(string $leftOperand, string $rightOperand): string;
}
