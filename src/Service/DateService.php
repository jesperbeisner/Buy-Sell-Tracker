<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;

class DateService
{
    /**
     * @return array<DateTime>
     */
    public function getStartAndEndOfWeekFromWeekNumber(int $weekNumber): array
    {
        $startDate = new DateTime('now');
        $startDate->setISODate(2022, $weekNumber);
        $startDate->setTime(0, 0, 0);

        $endDate = clone $startDate;
        $endDate->modify('+6 days');
        $endDate->setTime(23, 59, 59);

        return [$startDate, $endDate];
    }
}
