<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/10 10:40,
 * @LastEditTime: 2021/12/10 10:40
 */

namespace Core\Interfaces;


use Illuminate\Console\Scheduling\Schedule;

interface ScheduleInterface
{
    public static function run(Schedule $schedule);
}
