<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Tests\Service\Calendar;

use PHPUnit\Framework\TestCase;
use PavelOmelchuk\CommissionTask\Contract\Service\Calendar as CalendarServiceContract;
use PavelOmelchuk\CommissionTask\Service\Calendar\BuiltInDateTime as BuiltInDateTimeCalendarService;

class BuiltInDateTimeTest extends TestCase
{
    /**
     * @var CalendarServiceContract
     */
    private $calendar;

    public function setUp()
    {
        $this->calendar = new BuiltInDateTimeCalendarService('Y-m-d');
    }

    /**
     * @dataProvider dataProviderForGetSupportedDateFormatTest
     */
    public function testGetSupportedDateFormat(string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->getSupportedDateFormat()
        );
    }

    /**
     * @dataProvider dataProviderForIsMondayTest
     */
    public function testIsMonday(string $date, bool $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->isMonday($date)
        );
    }

    /**
     * @dataProvider dataProviderForIsSundayTest
     */
    public function testIsSunday(string $date, bool $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->isSunday($date)
        );
    }

    /**
     * @dataProvider dataProviderForGetStartDayOfWeekForDateTest
     */
    public function testGetStartDayOfWeekForDate(string $date, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->getStartDayOfWeekForDate($date)
        );
    }

    /**
     * @dataProvider dataProviderForGetEndDayOfWeekForDateTest
     */
    public function testGetEndDayOfWeekForDate(string $date, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->getEndDayOfWeekForDate($date)
        );
    }

    /**
     * @dataProvider dataProviderForGetDateListBetweenPassedDatesTest
     */
    public function testGetDateListBetweenPassedDates(string $dateFrom, string $dateTo, array $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->getDateListBetweenPassedDates($dateFrom, $dateTo)
        );
    }

    /**
     * @dataProvider dataProviderForGetDateListOnTheSameWeekAsDateTest
     */
    public function testGetDateListOnTheSameWeekAsDate(string $date, array $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->calendar->getDateListOnTheSameWeekAsDate($date)
        );
    }

    public function dataProviderForGetSupportedDateFormatTest(): array
    {
        return [
            'get supported date format returns initial parameter' => ['Y-m-d']
        ];
    }

    public function dataProviderForIsMondayTest(): array
    {
        return [
            'correct Monday' => ['2021-04-05', true],
            'incorrect Monday' => ['2021-04-06', false],
        ];
    }

    public function dataProviderForIsSundayTest(): array
    {
        return [
            'correct Sunday' => ['2021-04-11', true],
            'incorrect Sunday' => ['2021-04-05', false],
        ];
    }

    public function dataProviderForGetStartDayOfWeekForDateTest(): array
    {
        return [
            'start day of standard week' => ['2021-04-08', '2021-04-05'],
            'start day of week between months' => ['2021-04-01', '2021-03-29'],
            'start day of week between years' => ['2021-01-01', '2020-12-28'],
        ];
    }

    public function dataProviderForGetEndDayOfWeekForDateTest(): array
    {
        return [
            'end day of standard week' => ['2021-04-08', '2021-04-11'],
            'end day of week between months' => ['2021-03-29', '2021-04-04'],
            'end day of week between years' => ['2020-12-28', '2021-01-03'],
        ];
    }

    public function dataProviderForGetDateListBetweenPassedDatesTest(): array
    {
        return [
            'date list of standard period' => [
                '2021-04-08',
                '2021-04-11',
                ['2021-04-08', '2021-04-09', '2021-04-10', '2021-04-11']
            ],
            'date list of period between months' => [
                '2021-03-31',
                '2021-04-04',
                ['2021-03-31', '2021-04-01', '2021-04-02', '2021-04-03', '2021-04-04']
            ],
            'date list of period between years' => [
                '2020-12-31',
                '2021-01-02',
                ['2020-12-31', '2021-01-01', '2021-01-02']
            ],
        ];
    }

    public function dataProviderForGetDateListOnTheSameWeekAsDateTest(): array
    {
        return [
            'standard week' => [
                '2021-04-08',
                ['2021-04-05', '2021-04-06', '2021-04-07', '2021-04-08', '2021-04-09', '2021-04-10', '2021-04-11'],
            ],
            'week in 2 months' => [
                '2021-04-01',
                ['2021-03-29', '2021-03-30', '2021-03-31', '2021-04-01', '2021-04-02', '2021-04-03', '2021-04-04'],
            ],
            'week in 2 years' => [
                '2020-12-30',
                ['2020-12-28', '2020-12-29', '2020-12-30', '2020-12-31', '2021-01-01', '2021-01-02', '2021-01-03'],
            ],
        ];
    }
}