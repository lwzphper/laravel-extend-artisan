<?php

namespace Core\Listeners;

use Illuminate\Support\Facades\Log;

class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (config('app.debug') /*&& request()->header('Dump-Sql') request()->input('dump_sql') == 1*/) {
            $sql = str_replace("%", "#_#", $event->sql); // 将 % 替换为 #_# 防止sql冲突，如：格式化日期 %d
            $sql = str_replace("?", "'%s'", $sql);
            $log = vsprintf($sql, $event->bindings);
            $log = str_replace("#_#", "%", $log); // 将 #_# 转换为 %
            Log::debug($log);
        }
    }
}
