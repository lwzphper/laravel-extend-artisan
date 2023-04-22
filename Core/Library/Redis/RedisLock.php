<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/5/6 0006 17:18,
 * @LastEditTime: 2021/5/6 0006 17:18
 */

namespace Core\Library\Redis;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Class RedisLock
 * @package App\Common\Library
 * @author lwz
 */
class RedisLock
{
    /**
     * 获取锁
     * @param string $lockName 锁名称
     * @param int $acquireTimeout 获取锁的最大等待时间
     * @param int $lockTimout 锁的过期时间
     * @return string|bool
     */
    public static function lockWait(string $lockName, int $acquireTimeout = 5, int $lockTimout = 5)
    {
        $lockTimout = (int)ceil($lockTimout);

        $end = time() + $acquireTimeout;
        while (time() < $end) {
            if ($token = self::lock($lockName, $lockTimout)) {
                return $token;
            }
            usleep(10000);
        }
        // 获取锁失败，记录日志文件
//        Log::error('redis获取锁失败，lockName: ' . $lockName);
        return false;
    }

    /**
     * 获取锁
     * @param string $lockName 锁名称
     * @param int $lockTimout 锁超时秒数
     * @return false|string
     * @author lwz
     */
    public static function lock(string $lockName, int $lockTimout = 10)
    {
        $token = uniqid(mt_rand());
        if (Redis::set($lockName, $token, 'EX', $lockTimout, 'NX')) {
            return $token;
        } elseif (Redis::ttl($lockName) == -1) {
            Redis::expire($lockName, $lockTimout);
        }
        return false;
    }

    /**
     * 加锁处理操作
     * @param string $lockKey 锁的键名
     * @param callable $handleFn 处理的函数
     * @param int $lockTimout 加锁的时长
     * @return mixed
     */
    public static function handleByLock(string $lockKey, callable $handleFn, int $lockTimout = 5)
    {
        while (true) {
            $token = self::lock($lockKey, $lockTimout);
            if ($token) {
                $data = $handleFn();
                // 删除锁
                self::releaseLock($lockKey, $token);
                return $data;
            } else {
                usleep(10000);
            }
        }
    }

    /**
     * 释放锁
     * @param string $lockName 锁名称
     * @param string $token 唯一键
     * @return bool
     */
    public static function releaseLock(string $lockName, string $token): bool
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return Redis::eval($script, 1, $lockName, $token) == 1;
    }

    /**
     * 设置并且获取数据
     * @param string $cacheKey
     * @param callable $fn
     * @param int $cacheTtl
     * @param int $blockTime
     * @return mixed
     * @author lwz
     */
    public static function getAndSaveWhenEmpty(string $cacheKey, callable $fn, int $cacheTtl, int $blockTime = 3)
    {
        if ($result = Cache::get($cacheKey)) {
            return $result;
        }

        return self::handleByLock('lock:' . $cacheKey, function () use ($fn, $cacheKey, $cacheTtl) {
            $result = Cache::get($cacheKey);
            if ($result) {
                return $result;
            }

            $result = $fn();
            Cache::set($cacheKey, $result, $cacheTtl);
            return $result;
        });

        /*return Cache::lock('lock:' . $cacheKey)->block($blockTime, function () use ($fn, $cacheKey, $cacheTtl) {
            $result = Cache::get($cacheKey);
            if ($result) {
                return $result;
            }

            $result = $fn();
            Cache::set($cacheKey, $result, $cacheTtl);
            return $result;
        });*/
    }
}
