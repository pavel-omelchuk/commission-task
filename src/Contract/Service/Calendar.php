<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Contract\Service;

/**
 * Interface Calendar
 * Provides a set of useful methods to work with dates.
 */
interface Calendar
{
    /**
     * Returns date format supported by the App.
     */
    public function getSupportedDateFormat(): string;

    /**
     * Determines whether the passed date is a Monday or not.
     */
    public function isMonday(string $date): bool;

    /**
     * Determines whether the passed date is a Sunday or not.
     */
    public function isSunday(string $date): bool;

    /**
     * Returns date of first day of week (Monday) for passed $date.
     */
    public function getStartDayOfWeekForDate(string $date): string;

    /**
     * Returns date of last day of week (Sunday) for passed $date.
     */
    public function getEndDayOfWeekForDate(string $date): string;

    /**
     * Returns array of dates (formatted to string) between 2 passed dates (both included).
     */
    public function getDateListBetweenPassedDates(string $dateFrom, string $dateTo): array;

    /**
     * Returns array of dates (formatted to string) on the same week as passed date.
     */
    public function getDateListOnTheSameWeekAsDate(string $date): array;
}
