<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Service\Calendar;

use DateInterval;
use DatePeriod;
use DateTime;
use PavelOmelchuk\CommissionTask\Contract\Service\Calendar as CalendarServiceContract;

/**
 * Class BuiltInDateTime.
 * Implements Calendar interface with built-in DateTime PHP components.
 */
class BuiltInDateTime implements CalendarServiceContract
{
    const DAY_NUM_MONDAY = 1;

    const DAY_NUM_SUNDAY = 7;

    /**
     * Date format supported by the App.
     *
     * @var string
     */
    protected $supportedDateFormat;

    public function __construct(string $supportedDateFormat)
    {
        $this->supportedDateFormat = $supportedDateFormat;
    }

    public function getSupportedDateFormat(): string
    {
        return $this->supportedDateFormat;
    }

    /** {@inheritdoc} */
    public function isMonday(string $date): bool
    {
        $dateTime = $this->convertToDateTimeInstance($date);
        $dateDayNum = (int) $dateTime->format('N');

        // whether the $date's day num equals to Monday
        return $dateDayNum === static::DAY_NUM_MONDAY;
    }

    /** {@inheritdoc} */
    public function isSunday(string $date): bool
    {
        $dateTime = $this->convertToDateTimeInstance($date);
        $dateDayNum = (int) $dateTime->format('N');

        // whether the $date's day num equals to Sunday
        return $dateDayNum === static::DAY_NUM_SUNDAY;
    }

    /** {@inheritdoc} */
    public function getStartDayOfWeekForDate(string $date): string
    {
        // return the passed $date if it's already a first day of a week.
        if ($this->isMonday($date)) {
            return $date;
        }

        $dateTime = $this->convertToDateTimeInstance($date);

        // return previous monday for passed $date
        return $dateTime->modify('last monday')->format($this->supportedDateFormat);
    }

    /** {@inheritdoc} */
    public function getEndDayOfWeekForDate(string $date): string
    {
        // return the passed $date if it's already a last day of a week.
        if ($this->isSunday($date)) {
            return $date;
        }

        $dateTime = $this->convertToDateTimeInstance($date);

        // return next sunday for passed $date
        return $dateTime->modify('next sunday')->format($this->supportedDateFormat);
    }

    /** {@inheritdoc} */
    public function getDateListBetweenPassedDates(string $dateFrom, string $dateTo): array
    {
        // container
        $datesInPeriod = [];

        // convert passed dates to DateTime instances
        $dateTimeFrom = $this->convertToDateTimeInstance($dateFrom);
        $dateTimeTo = $this->convertToDateTimeInstance($dateTo);
        // add 1 day to include passed $dateTo into period
        $dateTimeTo = $dateTimeTo->add(new DateInterval('P1D'));

        // get dates period between $dateFrom and $dateTo (both included)
        $datePeriod = new DatePeriod($dateTimeFrom, new DateInterval('P1D'), $dateTimeTo);

        // iterate through the period and put date in correct format to the container
        foreach ($datePeriod as $dateTime) {
            $datesInPeriod[] = $dateTime->format($this->supportedDateFormat);
        }

        return $datesInPeriod;
    }

    /** {@inheritdoc} */
    public function getDateListOnTheSameWeekAsDate(string $date): array
    {
        // get current $date's week boundaries
        $startDayOfWeek = $this->getStartDayOfWeekForDate($date);
        $endDayOfWeek = $this->getEndDayOfWeekForDate($date);

        // return array of dates in the week
        return $this->getDateListBetweenPassedDates($startDayOfWeek, $endDayOfWeek);
    }

    /**
     * Converts passed date (string representation) to DateTime instance.
     */
    protected function convertToDateTimeInstance(string $date): DateTime
    {
        return DateTime::createFromFormat($this->supportedDateFormat, $date);
    }
}
