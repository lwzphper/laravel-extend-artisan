<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/01/06 11:01,
 * @LastEditTime: 2022/01/06 11:01
 */

namespace Core\Library;


use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TimeHelper
{
    public const YEAR_LENGTH = 4; // 年份长度
    public const MONTH_LENGTH = 2; // 月份长度
    public const MIN_MONTH = 1; // 最小的月份
    public const MAX_MONTH = 12; // 最大的月份

    // 返回日期格式
    public const DATE_TYPE_DATETIME = 1; // YYYY-MM-DD HH:ii:ss 格式
    public const DATE_TYPE_DATE = 2; // 年月日 格式
    public const DATE_TYPE_YEAR_MONTH_NUM = 3; // 年月数字。如：202201

    // 时间筛选选项
    public const TIME_TYPE_TODAY = 1; // 今天
    public const TIME_TYPE_YESTERDAY = 2; // 昨天
    public const TIME_TYPE_THIS_WEEK = 3; // 本周
    public const TIME_TYPE_LAST_WEEK = 4; // 上周
    public const TIME_TYPE_THIS_MONTH = 5; // 本月
    public const TIME_TYPE_LAST_MONTH = 6; // 上月
    public const TIME_TYPE_THIS_QUARTER = 7; // 本季度
    public const TIME_TYPE_LAST_QUARTER = 8; // 上一季度
    public const TIME_TYPE_THIS_YEAR = 9; // 本年度
    public const TIME_TYPE_LAST_30_DAY = 10; // 最近30天
    public const TIME_TYPE_LAST_7_DAY = 11; // 最近7天
    public const TIME_TYPE_LAST_YEAR = 12; // 上一年度
    public static array $timeType = [
        self::TIME_TYPE_TODAY,
        self::TIME_TYPE_YESTERDAY,
        self::TIME_TYPE_THIS_WEEK,
        self::TIME_TYPE_LAST_WEEK,
        self::TIME_TYPE_THIS_MONTH,
        self::TIME_TYPE_LAST_MONTH,
        self::TIME_TYPE_THIS_QUARTER,
        self::TIME_TYPE_LAST_QUARTER,
        self::TIME_TYPE_THIS_YEAR,
        self::TIME_TYPE_LAST_30_DAY,
        self::TIME_TYPE_LAST_7_DAY,
        self::TIME_TYPE_LAST_YEAR,
    ];

    /**
     * 获取时间范围
     * @param int|null $timeType 时间类型
     * @param array|null $timeRange 时间范围
     * @param int $dateType 日期类型
     * @return array
     */
    public static function getTimeRange(?int $timeType = null, ?array $timeRange = null, int $dateType = self::DATE_TYPE_DATETIME): array
    {
        // 如果时间类型 和 时间范围都为空，直接返回空数组
        if (is_null($timeType) && is_null($timeRange)) {
            return [];
        }
        // 优先使用自定义时间范围查询
        if (!empty($timeRange)) {
            return $timeRange;
        }

        switch ($dateType) {
            case self::DATE_TYPE_DATE:
                $dateFormat = 'Y-m-d';
                break;
            case self::DATE_TYPE_YEAR_MONTH_NUM:
                $dateFormat = 'Ym';
                break;
            default:
                $dateFormat = 'Y-m-d H:i:s';
        }

        // 根据类型返回对应的时间数据
        switch ($timeType) {
            case self::TIME_TYPE_TODAY: // 今天
                $carbonObj = Carbon::today();
                return [
                    $carbonObj->startOfDay()->format($dateFormat),
                    $carbonObj->endOfDay()->format($dateFormat),
                ];
            case self::TIME_TYPE_YESTERDAY: // 昨天
                $carbonObj = Carbon::yesterday();
                return [
                    $carbonObj->startOfDay()->format($dateFormat),
                    $carbonObj->endOfDay()->format($dateFormat),
                ];
            case self::TIME_TYPE_THIS_WEEK: // 本周
                $carbonObj = Carbon::now();
                return [
                    $carbonObj->startOfWeek()->format($dateFormat),
                    $carbonObj->endOfWeek()->format($dateFormat),
                ];
            case self::TIME_TYPE_LAST_WEEK: // 上周
                $carbonObj = Carbon::now()->subWeek();
                return [
                    $carbonObj->startOfWeek()->format($dateFormat),
                    $carbonObj->endOfWeek()->format($dateFormat),
                ];
            case self::TIME_TYPE_THIS_MONTH: // 本月
                $carbonObj = Carbon::now();
                return [
                    $carbonObj->startOfMonth()->format($dateFormat),
                    $carbonObj->endOfMonth()->format($dateFormat),
                ];
            case self::TIME_TYPE_LAST_MONTH: // 上月
                $carbonObj = Carbon::now()->subMonth();
                return [
                    $carbonObj->startOfMonth()->format($dateFormat),
                    $carbonObj->endOfMonth()->format($dateFormat),
                ];
            case self::TIME_TYPE_THIS_QUARTER: // 本季度
                $carbonObj = Carbon::now();
                return [
                    $carbonObj->firstOfQuarter()->format($dateFormat),
                    $carbonObj->lastOfQuarter()->endOfDay()->format($dateFormat),
                ];
            case self::TIME_TYPE_LAST_QUARTER: // 上一季度
                $carbonObj = Carbon::parse('-3 months');
                return [
                    $carbonObj->firstOfQuarter()->format($dateFormat),
                    $carbonObj->lastOfQuarter()->endOfDay()->format($dateFormat),
                ];
            case self::TIME_TYPE_THIS_YEAR: // 本年度
                $carbonObj = Carbon::now();
                return [
                    $carbonObj->startOfYear()->format($dateFormat),
                    $carbonObj->endOfYear()->format($dateFormat),
                ];
            case self::TIME_TYPE_LAST_30_DAY: // 最近30天
                return [
                    Carbon::now()->subDays(30)->startOfDay()->format($dateFormat),
                    Carbon::now()->format($dateFormat),
                ];
            case self::TIME_TYPE_LAST_7_DAY: // 最近7天
                return [
                    Carbon::now()->subDays(7)->startOfDay()->format($dateFormat),
                    Carbon::now()->format($dateFormat),
                ];
            case self::TIME_TYPE_LAST_YEAR: // 上一年度
                $carbonObj = Carbon::now()->subYear();
                return [
                    $carbonObj->startOfYear()->format($dateFormat),
                    $carbonObj->endOfYear()->format($dateFormat),
                ];
        }
        return [];
    }

    /**
     * 获取按天的时间范围
     * @param int $timeType
     * @return array
     */
    public static function getDayRange(int $timeType): array
    {
        $datetime = self::getTimeRange($timeType);
        return $datetime ? [
            explode(" ", $datetime[0])[0],
            explode(" ", $datetime[1])[0]
        ] : [];
    }

    /**
     * 获取两个时间戳之间相差的天数
     * @param int $t1 时间戳1
     * @param int $t2 时间戳2
     * @return int
     * @author lwz
     */
    public static function getTimestampDiffDays(int $t1, int $t2): int
    {
        return floor(abs(($t1 - $t2) / 86400));
    }

    /**
     * 获取指定日期段内每一天的日期
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @param string $dateFormat 返回的日期格式。默认：YYYY-mm-dd
     * @return array
     */
    public static function getDateFromRange(string $startDate, string $endDate, string $dateFormat = 'Y-m-d'): array
    {
        $sTimestamp = strtotime($startDate);
        $eTimestamp = strtotime($endDate);

        // 计算日期段内有多少天
        $days = ($eTimestamp - $sTimestamp) / 86400 + 1;

        // 保存每天日期
        $date = array();

        for ($i = 0; $i < $days; $i++) {
            $date[] = date($dateFormat, $sTimestamp + (86400 * $i));
        }

        return $date;
    }

    /**
     * 获取周一和周日的日期（基于当前时间）
     * @param int $dateType 日期类型
     * @return array
     * @author lwz
     */
    public static function getMondayAndSunday(int $dateType = self::DATE_TYPE_DATE): array
    {
        return [
            date($dateType == self::DATE_TYPE_DATE ? 'Y-m-d' : 'Y-m-d 00:00:00', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
            date($dateType == self::DATE_TYPE_DATE ? 'Y-m-d' : 'Y-m-d 23:59:59', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600))
        ];
    }

    /**
     * 获取毫秒的时间戳
     * @return string
     */
    public static function getCurrentMilis(): string
    {
        $mill_time = microtime();
        $timeInfo = explode(' ', $mill_time);
        return sprintf('%d%03d', $timeInfo[1], $timeInfo[0] * 1000);
    }

    /**
     * 获取两个时间段的交集区间
     * @param $startDate1
     * @param $endDate1
     * @param $startDate2
     * @param $endDate2
     * @return array
     */
    public static function getDatesIntersection($startDate1, $endDate1, $startDate2, $endDate2): array
    {
        if ($startDate1 > $endDate2 || $endDate1 < $startDate2) {
            return [];
        }
        // 获取开始区间
        if ($startDate1 > $startDate2) {
            $startDate = $startDate1;
        } else {
            $startDate = $startDate2;
        }
        // 获取结束区间
        if ($endDate1 > $endDate2) {
            $endDate = $endDate2;
        } else {
            $endDate = $endDate1;
        }
        return [$startDate, $endDate];
    }

    /**
     * 获取数字格式月份之间的全部月份
     * @param int|array $timeRange 时间范围。如：202201
     * @return array
     * @author lwz
     */
    public static function getAllNumberMonthByRange($timeRange): array
    {
        if (is_numeric($timeRange) || count($timeRange) == 1) {
            return Arr::wrap($timeRange);
        }

        if ($timeRange[0] == $timeRange[1]) {
            return Arr::wrap($timeRange[0]);
        }

        // 将日期从小到大排序
        sort($timeRange);

        $startYear = (int)substr((string)$timeRange[0], 0, self::YEAR_LENGTH);
        $startMonth = (int)substr((string)$timeRange[0], self::YEAR_LENGTH, self::MONTH_LENGTH);
        $endYear = (int)substr((string)$timeRange[1], 0, self::YEAR_LENGTH);
        $endMonth = (int)substr((string)$timeRange[1], self::YEAR_LENGTH, self::MONTH_LENGTH);

        $monthRet = [];
        while (true) {
            if ($startYear > $endYear) { // 防止参数乱传，死循环
                break;
            }
            array_push($monthRet, $startYear . str_pad($startMonth, 2, '0', STR_PAD_LEFT));
            if ($startYear == $endYear && $startMonth == $endMonth) {
                break;
            }
            if ($startMonth >= self::MAX_MONTH) {
                $startYear++;
                $startMonth = self::MIN_MONTH;
            } else {
                $startMonth++;
            }
        }
        return $monthRet;
    }
}
