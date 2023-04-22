<?php


namespace Core\Library\Redis\BloomFilter;

use Illuminate\Support\Facades\Redis;

/**
 * 使用redis实现的布隆过滤器
 */
abstract class BloomFilterRedis {
	/**
	 * 需要使用一个方法来定义bucket的名字
	 */
	protected string $bucket;

	protected array $hashFunction;

	protected BloomFilterHash $Hash;

	/**
	 * BloomFilterRedis constructor.
	 *
	 * @throws \Exception
	 */
	public function __construct()
	{
		if ( ! $this->bucket || ! $this->hashFunction)
		{
			throw new \Exception("需要定义bucket和hashFunction", 1);
		}
		$this->Hash  = new BloomFilterHash;
	}

	/**
	 * 添加到集合中
	 */
	public function add($string)
	{
        return Redis::pipeline(function ($pipe) use ($string) {
            foreach ($this->hashFunction as $function)
            {
                $hash = $this->Hash->$function($string);
                $pipe->setBit($this->bucket, $hash, 1);
            }
        });
	}

	/**
	 * 批量添加内容到集合中
	 *
	 * @param array $keys
	 *
	 * @return array
	 */
	/*public function multiAdd(array $keys): array
    {
		$result = [];
		if (count($keys) < 1)
		{
			return $result;
		}
		foreach ($keys as $key)
		{
			$result[] = $this->add($key);
		}
		return $result;
	}*/

	/**
	 * 查询是否存在, 存在的一定会存在, 不存在有一定几率会误判
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public function exists(string $string): bool
    {
		$res = Redis::pipeline(function ($pipe) use ($string) {
            $len  = strlen($string);
            foreach ($this->hashFunction as $function)
            {
                $hash = $this->Hash->$function($string, $len);
                $pipe = $pipe->getBit($this->bucket, $hash);
            }
        });

		foreach ($res as $bit)
		{
			if ($bit == 0)
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * 批量检查是否存在
	 *
	 * @param array $keys
	 *
	 * @return array
	 */
	/*public function multiExists(array $keys): array
    {
		$result = [];
		if (count($keys) < 1)
		{
			return $result;
		}
		foreach ($keys as $key)
		{
			$result[] = $this->exists($key);
		}
		return $result;
	}*/

}
