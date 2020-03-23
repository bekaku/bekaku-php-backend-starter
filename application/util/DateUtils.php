<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 25/12/2015
 * Time: 2:11 PM
 */

namespace application\util;

use application\util\MessageUtils as MessageUtil;
use DateTime;
use DateInterval;
use DatePeriod;

class DateUtils
{
    const DATE_FOTMAT_FULL = "Y-m-d H:i:s a";
    const DATE_TIME_FORMAT = "Y-m-d H:m:s";
    const DATE_TIME_FORMAT_UNI = "Y-m-d H:i:s";

    const DATE_FORMAT = "Y-m-d";
    const TIME_FORMAT = "H:m:s";

    public static function dateNow(): DateTime
    {
        $date = null;
        try {
            $date = new DateTime();
        } catch (\Exception $e) {
            ControllerUtil::displayError($e->getMessage());
        }

        return $date;
    }

    public static function createDateFromString(string $d): DateTime
    {
        $date = null;
        if ($d) {
            try {
                $date = new DateTime($d);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }

        return $date;
    }

    public static function plusDateByDay(DateTime $dt, int $day): DateTime
    {
        $date = null;
        if ($dt) {
            try {
//                $date = $dt->modify( '+'.$day.' day' );
                $interval = new DateInterval('P' . $day . 'D');//D=day, M=month, Y=year
                $date = $dt->add($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function plusDateByYear(DateTime $dt, int $year): DateTime
    {
        $date = null;
        if ($dt) {
            try {
//                $date = $dt->modify( '+'.$day.' day' );
                $interval = new DateInterval('P' . $year . 'Y');//D=day, M=month, Y=year
                $date = $dt->add($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function minusDateByDay(DateTime $dt, int $day): DateTime
    {
        $date = null;
        if ($dt) {
            try {
//                $date = $dt->modify( '-'.$day.' day' );
                $interval = new DateInterval('P' . $day . 'D');//D=day, M=month, Y=year
                $date = $dt->sub($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function plusDateByHour(DateTime $dt, int $h): DateTime
    {
        $date = null;
        if ($dt) {
            try {
                $interval = new DateInterval('PT' . $h . 'H');
                $date = $dt->add($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function minusDateByHour(DateTime $dt, int $h): DateTime
    {
        $date = null;
        if ($dt) {
            try {
                $interval = new DateInterval('PT' . $h . 'H');
                $date = $dt->sub($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function plusDateByMinute(DateTime $dt, int $m): DateTime
    {
        $date = null;
        if ($dt) {
            try {
                $interval = new DateInterval('PT' . $m . 'M');
                $date = $dt->add($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function minusDateByMinute(DateTime $dt, int $m): DateTime
    {
        $date = null;
        if ($dt) {
            try {
                $interval = new DateInterval('PT' . $m . 'M');
                $date = $dt->sub($interval);
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $date;
    }

    public static function getDatePeriodList(DateTime $start, DateTime $end): DatePeriod
    {
        $period = null;
        if ($start && $end) {
            try {
                $period = new DatePeriod(
                    $start,
                    new DateInterval('P1D'),
                    $end
                );
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $period;
    }

    public static function getDateByDateFormat(DateTime $d, string $format = self::DATE_TIME_FORMAT): string
    {

        $dt = null;
        if ($d) {
            try {
                $dt = $d->format($format);
//                $datatimeForQuery->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                ControllerUtil::displayError($e->getMessage());
            }
        }
        return $dt;
    }

    //
    public static function getDateNow($includeTime = true)
    {
        if ($includeTime == false) {
            return @date("Y-m-d");
        } else {
            return @date("Y-m-d H:i:s");
        }
    }

    public static function getDateNowByFormat($format)
    {//Y-m-d H:i:s.u
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = null;
        try {
            $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        } catch (\Exception $e) {
        }
        $dat = $d->format($format);
        return substr($dat, 0, -3);//return millisec 3 digits
    }

    public static function getTimeNow()
    {//time stamp

        return @time();

    }

    public static function getTimeFromDate($date, $includeSecond = false)
    {


        $dateSplit = explode(" ", $date);
        $time = "00:00:00";
        if (count($dateSplit) > 0) {
            $time = $dateSplit[1];
            if (!$includeSecond) {
                $timeSplit = explode(":", $time);
                $time = $timeSplit[0] . ":" . $timeSplit[1];
            }
        }
        return $time;
    }

    public static function convertSqlSrvToMysql($date)
    {

        if (!empty($date)) {
            $dateSplit = explode(".", $date);
            $dateReturn = null;
            if (count($dateSplit) > 0) {
                $dateReturn = $dateSplit[0];
            }
            return $dateReturn;
        }
        return null;
    }

    public static function getDayOfWeekStringByDateTime($currentDateTime, $upperCase = false)
    {

        if ($upperCase) {
            return strtoupper(date('D', strtotime($currentDateTime)));
        } else {
            return date('D', strtotime($currentDateTime));
        }
    }

    public static function isDateInRange($compareDate, $fromDate, $toDate)
    {//yyyy-mm-dd, yyyy-mm-dd, yyyy-mm-dd

        $isBetween = false;
        $compareTime = strtotime($compareDate);

        //make from time to datetime
        $from = strtotime($fromDate);

        //make to time to datetime
        $to = strtotime($toDate);

        if ($to >= $from) {
            $isBetween = $to > $from && $compareTime >= $from && $compareTime <= $to || $to < $from && ($compareTime >= $from || $compareTime <= $to);
        }

        return $isBetween;
    }

    public static function isTimeInRange($compareDate, $fromTime, $toTime)
    {//yyyy-mm-dd HH:mm:ss, HH:mm, HH:mm

        $isBetween = false;
        $compareTime = strtotime($compareDate);
        $dt = null;
        try {
            $dt = new DateTime($compareDate);
        } catch (\Exception $e) {
        }
        //get date from dateString
        $dateString = $dt->format('Y-m-d');

        //make from time to datetime
        $fromDate = $dateString . " " . $fromTime . ":00";
        $from = strtotime($fromDate);

        //make to time to datetime
        $toDate = $dateString . " " . $toTime . ":00";
        $to = strtotime($toDate);

        if ($to >= $from) {
            $isBetween = $to > $from && $compareTime >= $from && $compareTime <= $to || $to < $from && ($compareTime >= $from || $compareTime <= $to);
        }

        return $isBetween;
    }


    public static function getYearNow($isThaiYear = FALSE)
    {//DateUtils::getYearNow(TRUE);

        if ($isThaiYear) {
            return @date("Y") + 543;
        } else {
            return @date("Y");
        }
    }

    public static function getNumberMonthNow($isIntType = TRUE)
    {//DateUtils::getNumberMonthNow(TRUE);
        if ($isIntType) {
            return number_format(@date('m'));
        } else {
            return @date('m');
        }
    }

    public static function getNumberMonthFromDate($date)
    {//2015-07-06 19:51:37

        $date = date($date);

        return number_format($date('m'));
    }

    public static function getYearFromDate($date, $isThaiYear = FALSE)
    {

        $dateSplit = explode("-", $date);
        if ($isThaiYear) {
            return $dateSplit[0] + 543;
        } else {
            return $dateSplit[0];
        }

    }

    public static function getYearShortFromDate($date, $isThaiYear = FALSE)
    {//2015-07-06 19:51:37

        $time = strtotime($date);
        $yearVal = null;
        if ($isThaiYear) {
            $yearVal = date("y", $time) + 43;
        } else {
            $yearVal = date("y", $time);
        }

        return $yearVal;

    }

    public static function getYearAndMonthFromDate($date)
    {//2015-07-06 19:51:37

        $time = strtotime($date);
        $month = date("m", $time);
        $year = date("Y", $time);

        return $year . $month;

    }

    public static function isPreviousOrCurrentDate($param)
    {//2015-07-06
        $today = date("Y-m-d");
        $today_time = strtotime($today);
        $param_time = strtotime($param);

        if ($param_time > $today_time) {
            return false;
        } else {
            return true;
        }
    }


    public static function getPlusDateFromNow($dayAmount)
    {//paramiter int
        $date = strtotime("+$dayAmount day");
        return date('Y-m-d H:i:s', $date);
    }

    public static function getReduceDateFromNow($dayAmount)
    {//paramiter int
        $date = strtotime("-$dayAmount day");
        return date('Y-m-d H:i:s', $date);
    }

    public static function getMonthFromDate($date)
    {

        $dateSplit = explode("-", $date);
        return $dateSplit[1];

    }

    public static function getDayFromDate($date)
    {

        $dateSplit = explode("-", $date);
        return $dateSplit[2];

    }

    public static function DateConvert($strDate, $type)
    {
        $new_date = null;
        if ($strDate != "") {

            if ($type == "th2en") {

                $new_date = substr($strDate, -4) . '-' . substr($strDate, -7, 2) . '-' . substr($strDate, -10, 2);//แปลง เอาปีขึ้นหน้า

            }
            if ($type == "en2th") {

                $new_date = substr($strDate, -2) . '-' . substr($strDate, -5, 2) . '-' . substr($strDate, -10, 4);    //	แปลงเป็น วัน/เดือน/ปี

            }

        }
        return $new_date;
    }

    public static function convertStrToDateTime($strTime)
    {//param 19:30

        $time = @strtotime($strTime);
        $myDate = @date('H:i:s', $time);
        return $myDate;
    }

    public static function convertDateStrToDateFormat($strDate, $fromThaiYear = FALSE)
    {//param 'dd/mm/yyyy'27/10/2012 format to 2012-10-27 yyyy-mm-dd

        list($d, $m, $y) = explode('/', $strDate);

        if ($fromThaiYear) {
            $mk = mktime(0, 0, 0, $m, $d, $y - 543);
        } else {
            $mk = mktime(0, 0, 0, $m, $d, $y);
        }
        $newDate = strftime('%Y-%m-%d', $mk);

        return $newDate;
    }


    public static function convertToShortThai($strDate)
    {//param 2012-10-22 to 33/10/2555
        $yearInt = null;
        $yearPlus = 543;
        if ($strDate) {
            $dateStr = substr($strDate, -2);
            $monthStr = substr($strDate, -5, 2);
            $yearStr = substr($strDate, -10, 4);
            $yearInt = (int)$yearStr;
            $yearPlus = $yearInt + $yearPlus;

            $new_date = $dateStr . '/' . $monthStr . '/' . $yearPlus;    //	แปลงเป็น วัน/เดือน/ปี
            return $new_date;
        } else {
            return false;
        }
    }

    public static function getThaiDateFull($strDate, $getTime = FALSE)
    {//$strDate format date("Y-m-d H:i:s")
        $time = null;
        if ($strDate) {
            $time = strtotime($strDate);
        }

        //$thai_day_arr = array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
        $thai_day_arr = array(
            MessageUtil::getMessage("date_days_1"),
            MessageUtil::getMessage("date_days_2"),
            MessageUtil::getMessage("date_days_3"),
            MessageUtil::getMessage("date_days_4"),
            MessageUtil::getMessage("date_days_5"),
            MessageUtil::getMessage("date_days_6"),
            MessageUtil::getMessage("date_days_7")
        );
        $thai_month_arrs = array(
            "0" => "",
            "1" => MessageUtil::getMessage("date_month_1"),
            "2" => MessageUtil::getMessage("date_month_2"),
            "3" => MessageUtil::getMessage("date_month_3"),
            "4" => MessageUtil::getMessage("date_month_4"),
            "5" => MessageUtil::getMessage("date_month_5"),
            "6" => MessageUtil::getMessage("date_month_6"),
            "7" => MessageUtil::getMessage("date_month_7"),
            "8" => MessageUtil::getMessage("date_month_8"),
            "9" => MessageUtil::getMessage("date_month_9"),
            "10" => MessageUtil::getMessage("date_month_10"),
            "11" => MessageUtil::getMessage("date_month_11"),
            "12" => MessageUtil::getMessage("date_month_12")
        );

        //global $thai_day_arr,$thai_month_arr;
        $thai_date_return = MessageUtil::getMessage("date_date_day") . " " . $thai_day_arr[date("w", $time)];
        $thai_date_return .= " " . MessageUtil::getMessage("date_date_date") . " " . date("j", $time);
        $thai_date_return .= " " . MessageUtil::getMessage("date_date_month") . " " . $thai_month_arrs[date("n", $time)];
        $thai_date_return .= " " . MessageUtil::getMessage("date_date_year") . " " . (date("Y", $time) + 543);

        if ($getTime) {
            $thai_date_return .= "  " . date("H:i", $time) . " " . MessageUtil::getMessage("date_date_minute");
        }

        return $thai_date_return;

    }

    public static function getThaiDate($strDate, $i_time = false, $short_mont = false)//$strDate format date("Y-m-d H:i:s")
    {
        $thai_month_arr = array(
            "0" => "",
            "1" => MessageUtil::getMessage("date_month_1"),
            "2" => MessageUtil::getMessage("date_month_2"),
            "3" => MessageUtil::getMessage("date_month_3"),
            "4" => MessageUtil::getMessage("date_month_4"),
            "5" => MessageUtil::getMessage("date_month_5"),
            "6" => MessageUtil::getMessage("date_month_6"),
            "7" => MessageUtil::getMessage("date_month_7"),
            "8" => MessageUtil::getMessage("date_month_8"),
            "9" => MessageUtil::getMessage("date_month_9"),
            "10" => MessageUtil::getMessage("date_month_10"),
            "11" => MessageUtil::getMessage("date_month_11"),
            "12" => MessageUtil::getMessage("date_month_12")
        );
        $thai_short_month_arr = array("",
            MessageUtil::getMessage("date_month_short_1"),
            MessageUtil::getMessage("date_month_short_2"),
            MessageUtil::getMessage("date_month_short_3"),
            MessageUtil::getMessage("date_month_short_4"),
            MessageUtil::getMessage("date_month_short_5"),
            MessageUtil::getMessage("date_month_short_6"),
            MessageUtil::getMessage("date_month_short_7"),
            MessageUtil::getMessage("date_month_short_8"),
            MessageUtil::getMessage("date_month_short_9"),
            MessageUtil::getMessage("date_month_short_10"),
            MessageUtil::getMessage("date_month_short_11"),
            MessageUtil::getMessage("date_month_short_12")
        );


        $strYear = @date("Y", strtotime($strDate)) + 543;
        $strMonth = @date("n", strtotime($strDate));
        $strDay = @date("j", strtotime($strDate));
        $strHour = @date("H", strtotime($strDate));
        $strMinute = @date("i", strtotime($strDate));
//		$strSeconds= @date("s",strtotime($strDate));


        if ($short_mont) {
            $strMonthCut = $thai_short_month_arr;
        } else {

            $strMonthCut = $thai_month_arr;//Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        }

        $strMonthThai = $strMonthCut[$strMonth];
        if ($i_time) {
            return "$strDay $strMonthThai $strYear $strHour:$strMinute" . " " . MessageUtil::getMessage("date_date_minute");
        } else {
            return "$strDay $strMonthThai $strYear";
        }

        //return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
    }

    public static function getThaiFullDate($strDate, $i_time = FALSE)//$strDate format date("Y-m-d H:i:s")
    {
        $thai_month_arr = array(
            "0" => "",
            "1" => MessageUtil::getMessage("date_month_1"),
            "2" => MessageUtil::getMessage("date_month_2"),
            "3" => MessageUtil::getMessage("date_month_3"),
            "4" => MessageUtil::getMessage("date_month_4"),
            "5" => MessageUtil::getMessage("date_month_5"),
            "6" => MessageUtil::getMessage("date_month_6"),
            "7" => MessageUtil::getMessage("date_month_7"),
            "8" => MessageUtil::getMessage("date_month_8"),
            "9" => MessageUtil::getMessage("date_month_9"),
            "10" => MessageUtil::getMessage("date_month_10"),
            "11" => MessageUtil::getMessage("date_month_11"),
            "12" => MessageUtil::getMessage("date_month_12")
        );
        $strYear = @date("Y", strtotime($strDate)) + 543;
        $strMonth = @date("n", strtotime($strDate));
        $strDay = @date("j", strtotime($strDate));
        $strHour = @date("H", strtotime($strDate));
        $strMinute = @date("i", strtotime($strDate));
//		$strSeconds= @date("s",strtotime($strDate));


        $strMonthCut = $thai_month_arr;
        $strMonthThai = $strMonthCut[$strMonth];
        if ($i_time) {
            return "$strDay $strMonthThai $strYear $strHour:$strMinute" . " " . MessageUtil::getMessage("date_date_minute");
        } else {
            return "$strDay $strMonthThai $strYear";
        }

        //return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
    }

    public static function getEngShortDate($strDate)//$strDate format date("Y-m-d H:i:s")
    {
        $strYear = @date("Y", strtotime($strDate));
        $strMonth = @date("n", strtotime($strDate));
        $strDay = @date("j", strtotime($strDate));

        $short_month_arr = array("",
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'June',
            'July',
            'Aug',
            'Sept',
            'Oct',
            'Nov',
            'Dec',
        );

        $strMonthCut = $short_month_arr;
        $strMonth = $strMonthCut[$strMonth];
        return $strDay . ' ' . $strMonth . ' ' . $strYear;

    }

    public static function getThaiShortDate($strDate)//$strDate format date("Y-m-d")
    {
        $thai_short_month_arr = array("",
            MessageUtil::getMessage("date_month_short_1"),
            MessageUtil::getMessage("date_month_short_2"),
            MessageUtil::getMessage("date_month_short_3"),
            MessageUtil::getMessage("date_month_short_4"),
            MessageUtil::getMessage("date_month_short_5"),
            MessageUtil::getMessage("date_month_short_6"),
            MessageUtil::getMessage("date_month_short_7"),
            MessageUtil::getMessage("date_month_short_8"),
            MessageUtil::getMessage("date_month_short_9"),
            MessageUtil::getMessage("date_month_short_10"),
            MessageUtil::getMessage("date_month_short_11"),
            MessageUtil::getMessage("date_month_short_12")
        );

        $strYear = @date("Y", strtotime($strDate)) + 543;
        $strMonth = @date("n", strtotime($strDate));
        $strDay = @date("j", strtotime($strDate));
//		$strHour= @date("H",strtotime($strDate));
//		$strMinute= @date("i",strtotime($strDate));
//		$strSeconds= @date("s",strtotime($strDate));

        $strMonthCut = $thai_short_month_arr;
        $strMonthThai = $strMonthCut[$strMonth];
        return $strDay . ' ' . $strMonthThai . ' ' . $strYear;

    }

    public static function convertDateToTimeStamp($strDate)//parameter yyyy-mm-dd h:m:s
    {
        if ($strDate) {
            return @strtotime($strDate);
        }
        return false;
    }

    public static function minToHr($minutes)
    {// Total
        if ($minutes <= 0) {
            return '00:00';
        } else {
            return sprintf("%02d", floor($minutes / 60)) . ':' . sprintf("%02d", str_pad(($minutes % 60), 2, "0", STR_PAD_LEFT));
        }
    }

    public static function getTimeStampStr($session_time)//DateUtils::getTimeStampStr(convertDateToTimeStamp(2015-07-12 20:06:34));
    {
        if ($session_time) {

            $time_difference = time() - $session_time;
            $seconds = $time_difference;
            $minutes = round($time_difference / 60);
            $hours = round($time_difference / 3600);
            $days = round($time_difference / 86400);
            $weeks = round($time_difference / 604800);
            $months = round($time_difference / 2419200);
            $years = round($time_difference / 29030400);
            if ($seconds <= 60) {
                return "$seconds วินาที ที่แล้ว"; //seconds ago
            } else if ($minutes <= 60) {
                if ($minutes == 1) {
                    return "1 นาที ที่แล้ว"; //one minute ago
                } else {
                    return "$minutes นาที ที่แล้ว"; //minutes ago
                }
            } else if ($hours <= 24) {
                if ($hours == 1) {
                    return "1 ชั่วโมง ที่แล้ว";//one hour ago
                } else {
                    return "$hours ชั่วโมง ที่แล้ว";//hours ago
                }
            } else if ($days <= 7) {
                if ($days == 1) {
                    return "1 วัน ที่แล้ว";//one day ago
                } else {
                    return "$days วัน ที่แล้ว";//days ago
                }
            } else if ($weeks <= 4) {
                if ($weeks == 1) {
                    return "1 สัปดาห์ ที่แล้ว";//one week ago
                } else {
                    return "$weeks สัปดาห์ ที่แล้ว";//weeks ago
                }
            } else if ($months <= 12) {
                if ($months == 1) {
                    return "1 เดือน ที่แล้ว";//one month ago
                } else {
                    return "$months เดือน ที่แล้ว";//months ago
                }


            } else {
                if ($years == 1) {
                    return "1 ปี ที่แล้ว";//one year ago
                } else {
                    return "$years ปี ที่แล้ว";//years ago
                }


            }
        } else {
            return '-';
        }


    }

    public static function time_ago($date, $granularity = 2)
    {
        $date = strtotime($date);
        $difference = time() - $date;
        $periods = array(
            MessageUtil::getMessage("date_date_decade") => 315360000,//decade
            MessageUtil::getMessage("date_date_year_call") => 31536000,//year
            MessageUtil::getMessage("date_date_month") => 2628000,//month
            MessageUtil::getMessage("date_date_week") => 604800, //week
            MessageUtil::getMessage("date_date_day") => 86400,//day
            MessageUtil::getMessage("date_date_hour") => 3600,//hour
            MessageUtil::getMessage("date_date_minute_call") => 60,//minute
            MessageUtil::getMessage("date_date_second") => 1);//second
        $retval = '';
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference / $value);
                $difference %= $value;
                $retval .= ($retval ? ' ' : '') . $time . ' ';

                if (MessageUtils::getConfig('locale') == 'en') {
                    $retval .= (($time > 1) ? $key . 's' : $key);
                } else {
                    $retval .= (($time > 1) ? $key : $key);
                }

                $granularity--;
            }
            if ($granularity == '0') {
                break;
            }
        }
        return '' . $retval . ' ' . MessageUtil::getMessage("date_date_ago");
    }

    //returns the exact age of a user
    public static function age($month, $day, $year)
    {
        //(checkdate($month, $day, $year) == 0) ? die("no such date.") : "";
        $y = gmstrftime("%Y");
        $m = gmstrftime("%m");
        $d = gmstrftime("%d");
        $age = $y - $year;
        if ($m <= $month) {
            if ($m == $month) {
                if ($d < $day) $age = $age - 1;
            } else $age = $age - 1;
        }
        return ($age);
    }

    public static function calculateDiffDateTime($dateLess, $dateGreater)
    {

        $date = null;
        $current = null;
        try {
            $date = new \DateTime($dateLess);
        } catch (\Exception $e) {
        }

        try {
            $current = new \DateTime($dateGreater);
        } catch (\Exception $e) {
        }
        // The diff-methods returns a new DateInterval-object...
        $diff = $current->diff($date);
//        $diffTime = $diff->format('%a Day and %h hours');

        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);

        return $hours;
    }
}