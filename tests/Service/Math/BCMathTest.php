<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Service\Math;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Contract\Service\Math as MathServiceContract;
use PavelOmelchuk\CommissionTask\Service\Math\BCMath as BCMathService;

class BCMathTest extends TestCase
{
    /**
     * @var MathServiceContract
     */
    private $math;

    public function setUp()
    {
        $this->math = new BCMathService(2);
    }

    /**
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->add($leftOperand, $rightOperand)
        );
    }

    /**
     * @dataProvider dataProviderForSubTesting
     */
    public function testSub(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->sub($leftOperand, $rightOperand)
        );
    }

    /**
     * @dataProvider dataProviderForMulTesting
     */
    public function testMul(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->mul($leftOperand, $rightOperand)
        );
    }

    /**
     * @dataProvider dataProviderForDivTesting
     */
    public function testDiv(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->div($leftOperand, $rightOperand)
        );
    }

    /**
     * @dataProvider dataProviderForMinTesting
     */
    public function testMin(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->min($leftOperand, $rightOperand)
        );
    }

    /**
     * @dataProvider dataProviderForMaxTesting
     */
    public function testMax(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->max($leftOperand, $rightOperand)
        );
    }

    /**
     * @dataProvider dataProviderForCompTesting
     */
    public function testComp(string $leftOperand, string $rightOperand, int $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->comp($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers' => ['1', '2', '3'],
            'add negative number to a positive' => ['-1', '2', '1'],
            'add natural number to a float' => ['1', '1.05123', '2.05'],
        ];
    }

    public function dataProviderForSubTesting(): array
    {
        return [
            'sub natural number from another' => ['2', '1', '1'],
            'sub greater number from less' => ['1', '2', '-1'],
            'sub float number from a natural' => ['3', '1.05', '1.95'],
        ];
    }

    public function dataProviderForMulTesting(): array
    {
        return [
            'multiply natural number by another' => ['5', '2', '10'],
            'multiply positive number by negative' => ['2', '-1', '-2'],
            'multiply float by float' => ['1.5', '1.1', '1.65'],
        ];
    }

    public function dataProviderForDivTesting(): array
    {
        return [
            'divide natural number by another' => ['10', '2', '5'],
            'divide natural number by another with remainder' => ['5', '2', '2.5'],
            'divide natural number by another with period remainder' => ['1', '3', '0.33'],
            'divide greater number be less' => ['1', '0.5', '2'],
            'divide greater number by float' => ['10', '2.5', '4'],
        ];
    }

    public function dataProviderForMinTesting(): array
    {
        return [
            'compare 2 natural numbers' => ['2', '1', '1'],
            'compare positive vs negative' => ['-2', '0', '-2'],
            'compare 2 equal numbers' => ['1', '1', '1'],
            'compare 2 float numbers' => ['1.05', '1.06', '1.05'],
        ];
    }

    public function dataProviderForMaxTesting(): array
    {
        return [
            'compare 2 natural numbers' => ['1', '2', '2'],
            'compare positive vs negative' => ['1', '-5', '1'],
            'compare 2 equal numbers' => ['1', '1', '1'],
            'compare 2 float numbers' => ['1.05', '1.06', '1.06'],
        ];
    }

    public function dataProviderForCompTesting(): array
    {
        return [
            'compare 2 equal floats' => ['1.05', '1.05', MathServiceContract::COMP_EQUAL],
            'compare greater float vs less' => ['1.05', '1.03', MathServiceContract::COMP_GREATER_LEFT],
            'compare less float vs less' => ['1.03', '1.05', MathServiceContract::COMP_GREATER_RIGHT],
            'compare 2 equal naturals' => ['1', '1', MathServiceContract::COMP_EQUAL],
            'compare greater natural vs less' => ['11', '1', MathServiceContract::COMP_GREATER_LEFT],
            'compare less natural vs less' => ['1', '12', MathServiceContract::COMP_GREATER_RIGHT],
        ];
    }
}
