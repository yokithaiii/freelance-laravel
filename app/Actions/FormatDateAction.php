<?php

namespace App\Actions;

use Carbon\Carbon;

class FormatDateAction
{

    public function setLocale($locale): void
    {
        Carbon::setLocale($locale);
    }

    public function formatDateForHumans($date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }

    public function formatDate($date, $format): string
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format($format);
    }

}