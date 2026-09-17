<?php

use Carbon\Carbon;

if (!function_exists('getBusinessTimeRange')) {

    function getBusinessTimeRange($startHour = 6)
    {
        $now = Carbon::now();

        if ($now->hour < $startHour) {
            // Before business start hour → still previous business day
            $start = Carbon::yesterday()->setHour($startHour)->setMinute(0)->setSecond(0);
            $end   = Carbon::today()->setHour($startHour)->setMinute(0)->setSecond(0);
        } else {
            // Current business day
            $start = Carbon::today()->setHour($startHour)->setMinute(0)->setSecond(0);
            $end   = Carbon::tomorrow()->setHour($startHour)->setMinute(0)->setSecond(0);
        }

        return [$start, $end];
    }

}