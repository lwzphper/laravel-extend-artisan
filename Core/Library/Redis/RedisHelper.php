<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/12/30 16:07,
 * @LastEditTime: 2022/12/30 16:07
 */
declare(strict_types=1);

namespace Core\Library\Redis;


use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    private static ?Connection $redisConnect = null; // redis 对象

    /**
     * 获取 redis 对象
     * @author lwz
     */
    public static function getRedisConnect(): ?Connection
    {
        if (is_null(self::$redisConnect)) {
            self::$redisConnect = Redis::connection('cache');
        }
        return self::$redisConnect;
    }

    /**
     * 获取/设置 kv json数据
     * @param string $cacheKey 缓存key
     * @param callable $func 获取数据的回调（必须返回数组）
     * @param int|null $keyExpireTime 过期时间
     * @return array
     */
    public static function getSetKvDataWithArrResult(string $cacheKey, callable $func, ?int $keyExpireTime = null): array
    {
        $conn = self::getRedisConnect();
        if ($data = $conn->get($cacheKey)) {
            return json_decode($data, true);
        }
        $data = $func();
        // 空数据不缓存
        if (empty($data)) {
            return [];
        }

        $encodeData = self::encodeJsonData($data);
        $keyExpireTime ? $conn->setEx($cacheKey, $keyExpireTime, $encodeData) : $conn->set($cacheKey, $encodeData);
        return $data;
    }

    /**
     * 获取/设置 kv 字符串数据
     * @param string $cacheKey 缓存key
     * @param callable $func 获取数据的回调（必须返回数组）
     * @param int|null $keyExpireTime 过期时间
     * @return string
     */
    public static function getSetKvDataWithStrResult(string $cacheKey, callable $func, ?int $keyExpireTime = null): string
    {
        $conn = self::getRedisConnect();
        if ($data = $conn->get($cacheKey)) {
            return $data;
        }
        $data = $func();
        if (empty($data)) {
            return '';
        }
        $keyExpireTime ? $conn->setEx($cacheKey, $keyExpireTime, $data) : $conn->set($cacheKey, $data);
        return $data;
    }

    /**
     * 删除key
     * @param array|string $cacheKey
     */
    public static function removeKey($cacheKey)
    {
        $conn = self::getRedisConnect();
        $cacheKeys = Arr::wrap($cacheKey);
        $conn->del(...$cacheKeys);
    }

    /**
     * 编码数据
     * @param $data
     * @return false|string
     */
    private static function encodeJsonData($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
