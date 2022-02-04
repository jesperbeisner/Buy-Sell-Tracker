<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateTimeZone;

class DateService
{
    /**
     * @param int $weekNumber
     * @return array<DateTime>
     */
    public function getStartAndEndOfWeekFromWeekNumber(int $weekNumber): array
    {
        $startDate = new DateTime('now', new DateTimeZone('Europe/Berlin'));
        $startDate->setISODate(2022, $weekNumber);
        $startDate->setTime(0, 0, 0);

        $endDate = clone $startDate;
        $endDate->modify('+6 days');
        $endDate->setTime(23, 59, 59);

        return [$startDate, $endDate];
    }
}
