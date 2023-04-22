<?php

namespace Core\Library\Redis\BloomFilter;

/**
 * 主要用于当前待检查的内容是否已存在
 *
 * 重复内容过滤器
 * 该布隆过滤器总位数为2^32位, 判断条数为2^30条. hash函数最优为3个.(能够容忍最多的hash函数个数)
 * 使用的三个hash函数为
 * BKDR, SDBM, JSHash
 *
 * 注意, 在存储的数据量到2^30条时候, 误判率会急剧增加, 因此需要定时判断过滤器中的位为1的的数量是否超过50%, 超过则需要清空.
 *
 *
 * Class Client
 * @package Mu\BloomFilter
 */
class BloomFilter extends BloomFilterRedis
{

	/**
	 * 表示判断重复内容的过滤器
	 * @var string
	 */
	protected string $bucket = 'bloomFilter';

	protected array $hashFunction = array('BKDRHash', 'SDBMHash', 'JSHash');


	/**
	 * @param string $bucket
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function setBucket(string $bucket=''): BloomFilter
    {
		if (empty($bucket)){
			throw new \Exception('必须设置bucket');
		}
		$this->bucket = $bucket;
		return $this;
	}

	/**
	 * @param array $hash
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function setHashFunction(array $hash): BloomFilter
    {
		if (count($hash) >= 3){
			throw new \Exception('hash函数不得小于3个，不然误报率太高');
		}
		$this->hashFunction = $hash;
		return $this;
	}

}
