<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/03 10:14,
 * @LastEditTime: 2021/12/03 10:14
 */

namespace Core\Abstracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Core\Library\Redis\RedisLock;

/**
 * Class AbstractCache
 * @package Core\Abstracts
 * @author lwz
 * 缓存抽象类
 */
abstract class AbstractCache
{
    /**
     * 缓存key值字段
     * @var string
     */
    protected string $keyField = 'key';

    /**
     * 过期时间字段
     * @var string
     */
    protected string $expireTimeField = 'expire';

    /**
     * 空数据的缓存时间
     *   主要为了防止通过id获取详情
     *      1. 查询 id 为 1 的数据，此时数据库中不存在
     *      2. 插入 id 为 1 的数据
     *      3. 由于在 步骤1 中已经对数据进行了缓存，因此无法获取到 id 为 1 的数据
     * @var int
     */
    protected int $emptyExpire = 3;

    /**
     * 获取数据
     * @param string $name 数据名称
     * @param string $keyExt 缓存key扩展名称
     * @param null $default 默认值
     * @return mixed|null
     * @author lwz
     */
    public function get(string $name, string $keyExt = '', $default = null)
    {
        $key = $this->getCacheKey($name, $keyExt);
        return $key ? Cache::get($key, $default) : null;
    }

    /**
     * 加锁等待数据
     * @param string $name 数据名称
     * @param callable $handleGetData 获取数据的函数
     * @param string $keyExt 缓存key扩展名称
     * @param int $lockTimout 锁等待超时时间
     * @param bool $saveEmptyData 空数据时，是否将数据保存到缓存
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author lwz
     */
    public function getWithLock(string $name, callable $handleGetData, string $keyExt = '', int $lockTimout = 5, bool $saveEmptyData = false)
    {
        // 先从缓存中缓存
        $data = $this->get($name, $keyExt);
        if (is_null($data)) {
            // 获取锁的 key
            $config = $this->getCacheConfig($name);
            $lockKey = $this->fillCacheKey($config[$this->keyField], $keyExt);

            return RedisLock::handleByLock($lockKey, function () use ($name, $keyExt, &$handleGetData, $saveEmptyData) {
                $data = $this->get($name, $keyExt) ?: $handleGetData();
                $isEmpty = empty($data);

                // 设置缓存（只有数据不为空，或 数据为空并设置了保存数据 才进行缓存）
                if (!$isEmpty || ($isEmpty && $saveEmptyData)) {
                    $this->set($name, $data, $keyExt);
                }
                return $data;
            }, $lockTimout);
        }
        return $data;
    }

    /**
     * 批量获取数据
     * @param string $name 数据名称
     * @param array $keyExtArr 缓存key扩展名称。如：['xxx', 'xxx']
     */
    public function many(string $name, array $keyExtArr): array
    {
        $keys = []; // 记录需要获取的key
        foreach (array_unique($keyExtArr) as $keyExt) {
            array_push($keys, $this->getCacheKey($name, $keyExt));
        }
        return Cache::many($keys);
    }

    /**
     * 加锁获取多条数据
     * @param string $name 数据名称
     * @param array $keyExtArr 缓存key扩展名称。如：['xxx', 'xxx']
     * @param callable $handleGetData 获取数据的函数
     * @param int $lockTimout 锁等待超时时间
     */
    /*public function manyWithLock(string $name, array $keyExtArr, callable $handleGetData, int $lockTimout = 5): array
    {
        // 先将 key 进行排序处理
        sort($keyExtArr);

        // 先从缓存中获取数据
        $list = $this->many($name, $keyExtArr);
        if ($this->checkManyDataIsEmpty($list)) {
            // 获取加锁的 key
            $lockKey = $this->getCacheConfig($name)[$this->keyField] . md5(json_encode($keyExtArr));
            // 加锁获取数据
            return $this->handleByLock($lockKey, function () use ($name, &$keyExtArr, &$handleGetData) {
                // 从缓存中获取数据
                $list = $this->many($name, $keyExtArr);
                // 如果数据为空，就从数据中获取数据
                $this->checkManyDataIsEmpty($list) && $list = $handleGetData();
                // todo 将数据设置到缓存（如何将数据设置到对应的 key 缓存中？）

                return $list;
            }, $lockTimout);
        }
    }*/

    /**
     * 检查批量获取的数据是否为空
     * @param array $data many 获取的数据
     * @return bool
     */
    /*protected function checkManyDataIsEmpty(array $data): bool
    {
        // 只要数据中存在一个 null 就设为空。
        // 因为如果从数据库中获取不到数据，也会保存空数据到缓存中，
        // 因此数据不会为 null，出现 null 说明缓存已经过期了，需要重新获取
        foreach ($data as $item) {
            if (is_null($item)) {
                return true;
            }
        }
        return false;
    }*/

    /**
     * 设置数据
     * @param string $name 数据名称
     * @param mixed $value 保存的值
     * @param string $keyExt 缓存key扩展名称
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author lwz
     */
    public function set(string $name, $value, string $keyExt = ''): bool
    {
        $config = $this->getCacheConfig($name);
        // 将集合转化为数组
        $value instanceof Collection && $value = $value->toArray();
        $expireTime = empty($value) ? $this->emptyExpire : $config[$this->expireTimeField];
        return Cache::set(
            $this->fillCacheKey($config[$this->keyField], $keyExt),
            $value,
            $expireTime
        );
    }

    /**
     * 原生命令
     * @param string $method 方法名
     * @param array $parameters 参数
     * @return mixed
     */
    public function command(string $method, array $parameters = [])
    {
        return $this->getOriRedisObj()->command($method, $parameters);
    }

    /**
     * 获取原生 redis 的缓存对象
     * @return \Illuminate\Redis\Connections\Connection
     */
    protected function getOriRedisObj(): \Illuminate\Redis\Connections\Connection
    {
        return Redis::connection('cache');
    }

    /**
     * 删除数据
     * @param string $name 数据名称
     * @param string $keyExt 缓存key扩展名称
     * @author lwz
     */
    public function forget(string $name, string $keyExt = ''): bool
    {
        return Cache::forget($this->getCacheKey($name, $keyExt));
    }

    /**
     * 批量删除数据
     * @param string $name 数据名称
     * @param array $keyExt 缓存key扩展名称
     * @return int 成功删除的个数
     * @author lwz
     */
    public function forgetMultiple(string $name, array $keyExt): int
    {
        // 如果扩展数据为空，直接返回
        if (empty($keyExt)) {
            return 0;
        }

        // 获取 redis 的key
        $keys = array_map(function ($item) use ($name) {
            return $this->getOriRedisCacheKey($name, $item);
        }, $keyExt);
        if (empty($keyExt)) {
            return 0;
        }

        // 删除 key（返回成功删除的个数）
        return $this->getOriRedisObj()->del(...$keys);
    }

    /**
     * 通过前缀，删除数据
     * @param string $name 数据名称
     * @param string $prefix 数据前缀
     */
    public function forgetByPrefix(string $name, string $prefix)
    {
        // 匹配符合条件的key
        $redisObj = $this->getOriRedisObj();
        $keys = $redisObj->keys($this->getOriRedisCacheKey($name, $prefix . '*'));
        if (empty($keys)) {
            return;
        }

        // 将匹配到的结果，删除系统Redis的前缀（通过keys获取的是完整的key，但通过Redis进行操作时默认会加上系统前缀）
        $keys = $this->removeRedisPrefixKey($keys);

        // 删除匹配到的key
        !empty($keys) && $redisObj->del(...$keys);
    }


    /**
     * 获取原始的缓存 key
     * @param string $name 数据名称
     * @param string $keyExt 数据扩展
     * @return string
     * @author lwz
     */
    public function getOriRedisCacheKey(string $name, string $keyExt = ''): string
    {
        /**
         * 注意：这里有两个 key 前缀（Redis默认的前缀 和 cache 的前缀）
         * 由于Redis默认的前缀，使用 Redis Facade 时会自动加上，因此匹配时只需要加上 Cache 的前缀即可
         */
        return Cache::getPrefix() . $this->getCacheKey($name) . $keyExt;
    }

    /**
     * 对数据进行编码
     * @param $data
     * @return string
     */
    public function encodeData($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 对数据进行解码
     * @param $data
     * @return mixed
     */
    public function decodeData($data)
    {
        return json_decode($data, true);
    }

    /**
     * 移除 key 中，系统默认的 redis 前缀
     * @param array $keys redis key
     * @return array
     * @author lwz
     */
    protected function removeRedisPrefixKey(array $keys): array
    {
        $dbPrefix = config('database.redis.options.prefix'); // 数据库前缀
        return array_map(function ($item) use ($dbPrefix) {
            return preg_replace('/^' . $dbPrefix . '/', '', $item);
        }, $keys);
    }

    /**
     * 获取缓存key
     * @param string $name 缓存名称
     * @param string $keyExt 缓存key扩展名称
     * @return string|null
     * @author lwz
     */
    protected function getCacheKey(string $name, string $keyExt = ''): ?string
    {
        return $this->fillCacheKey($this->getCacheConfig($name)[$this->keyField] ?? null, $keyExt);
    }

    /**
     * 补全缓存key
     * @param string|null $key 缓存key
     * @param string $keyExt 缓存key扩展名称
     * @return string
     * @author lwz
     */
    protected function fillCacheKey(?string $key, string $keyExt = ''): ?string
    {
        return empty($key) ? null : $key . $keyExt;
    }

    /**
     * 获取缓存配置
     * @param string $name 数据名称
     * @return array
     * @author lwz
     */
    abstract protected function getCacheConfig(string $name): array;
}
