<?php

declare(strict_types=1);

namespace PavelOmelchuk\CommissionTask\Factory\Service;

use PavelOmelchuk\CommissionTask\Contract\Service\Calendar as CalendarServiceContract;
use PavelOmelchuk\CommissionTask\Factory\Service\Config as ConfigFactory;
use PavelOmelchuk\CommissionTask\Service\Calendar\BuiltInDateTime as BuiltInDateTimeCalendarService;

/**
 * Calendar factory.
 */
class Calendar
{
    /**
     * Provides with a Calendar Service instance.
     */
    public static function getInstance(): CalendarServiceContract
    {
        $supportedDateFormat = ConfigFactory::getInstance()->get('app.format.date');

        return new BuiltInDateTimeCalendarService($supportedDateFormat);
    }
}
