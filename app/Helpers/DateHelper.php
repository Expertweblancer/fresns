<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DateHelper
{
    private static $langTagFormat = ['en' => ['diffyear' => 'm/d/Y h:m', 'sameyear' => 'm/d h:m'], 'zh-Hans' => ['diffyear' => 'Y-m-d h:m', 'sameyear' => 'm-d h:m'], 'zh-Hant' => ['diffyear' => 'Y-m-d h:m', 'sameyear' => 'm-d h:m']];

    /**
     * Get database time zone.
     *
     * @return string
     */
    public static function fresnsSqlTimezone()
    {
        return DB::select('show VARIABLES like \'system_time_zone\';')[0]->Value;
    }

    /**
     * Get the current database time.
     *
     * @return string
     */
    public static function fresnsSqlCurrentTimestamp()
    {
        return get_object_vars(DB::select('SELECT NOW()')[0])['NOW()'];
    }

    /**
     * The conversion time is the current database time.
     *
     * @param $datetime
     * @param $timezone
     * @return string
     *
     * @throws \Exception
     */
    public static function fresnsConversionToSqlDatetime($datetime, $timezone)
    {
        $datetime = new \DateTime($datetime);
        $datetime = $datetime->setTimezone(new \DateTimeZone($timezone));
        $result = $datetime->setTimezone(new \DateTimeZone(self::fresnsSqlTimezone()));

        return $result->format('Y-m-d H:i:s');
    }

    /**
     * Output time values by time zone.
     *
     * @param $datetime
     * @param  string  $timezone
     * @return \DateTime|string
     *
     * @throws \Exception
     */
    public static function fresnsOutputTimeToTimezone($datetime, $timezone = '')
    {
        $mysqlZone = self::fresnsSqlTimezone();
        if ($mysqlZone == $timezone) {
            return $datetime;
        }
        if (empty($timezone)) {
            $timezone = request()->header('timezone');
        }
        $datetime = (new \DateTime($datetime))->setTimezone(new \DateTimeZone($mysqlZone));   //传参的时间timezone转成数据库的
        $time = $datetime->setTimezone(new \DateTimeZone($timezone));

        return $time->format('Y-m-d H:i:s');
    }

    /**
     * Formatted time output by time zone and language tag.
     *
     * @param  string  $datetime
     * @param  string  $timezone
     * @param  string  $langTag
     * @return string
     */
    public static function fresnsOutputFormattingTime($datetime, $timezone, $langTag = '')
    {
        $datetime = self::fresnsOutputTimeToTimezone($datetime, $timezone);
        $datetime = Carbon::parse($datetime);
        $mysqlTime = Carbon::parse(DateHelper::fresnsSqlCurrentTimestamp());
        $diff = $datetime->diffInDays($mysqlTime);
        if ($diff == 0) {
            return Carbon::parse($datetime)->format('h:m');
        }
        $year = $datetime->diffInYears($mysqlTime);
        $sign = $year != 0 ? 'sameyear' : 'diffyear';
        $langTag = $langTag ?: $langTag = ConfigHelper::fresnsConfigByItemKey('default_language');
        $reslutFormat = self::$langTagFormat[$langTag][$sign];

        return Carbon::parse($datetime)->format($reslutFormat);
    }

    /**
     * Processing output by language humanization time.
     *
     * @param $dateTime
     * @param  string  $langTag
     * @return string
     */
    public static function fresnsOutputHumanizationTime($dateTime, $langTag = '')
    {
        $langTag = $langTag ?: $langTag = ConfigHelper::fresnsConfigByItemKey('default_language');
        $currentTime = DateHelper::fresnsSqlCurrentTimestamp();
        $jet = Carbon::parse($dateTime);
        $diff = Carbon::parse($currentTime)->diffInMinutes($jet);
        $symbol = 'timeFormatMinute';
        if ($diff > 60) {
            $diff = Carbon::parse($currentTime)->diffInHours($jet);
            $symbol = 'timeFormatHour';
            if ($diff > 24) {
                $diff = Carbon::parse($currentTime)->diffInDays($jet);
                $symbol = 'timeFormatDay';
                if ($diff > 30) {
                    $diff = Carbon::parse($currentTime)->diffInMonths($jet);
                    $symbol = 'timeFormatMonth';
                }
            }
        }
        $timeFormat = ConfigHelper::fresnsConfigByItemKey('language_menus');
        foreach ($timeFormat as $item) {
            if ($item['langTag'] == $langTag) {
                $timeFormat = $item[$symbol];
                $timeFormat = mb_substr($timeFormat, '4');
            }
        }

        return $diff.$timeFormat;
    }
}