<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/25 17:57,
 * @LastEditTime: 2021/11/25 17:57
 */

namespace Core\Abstracts;


use App\Integral\Models\KpiSalaryUserMonthStat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Core\Constants\DBConst;
use Core\Exceptions\DBParamValidException;
use Core\Exceptions\ValidateException;

class AbstractRepository
{
    /**
     * 通过ID查找一条记录
     * @param int $id id
     * @param array $fields 获取的字段
     * @param bool $withTrashed 是否显示全部数据
     * @return Model|null
     */
    public static function getOneById(int $id, array $fields = ['*']): ?Model
    {
        return static::$model::query()
            ->select($fields)
            ->find($id);
    }

    /**
     * 通过ID查找一条记录
     * @param int $id id
     * @param array $fields 获取的字段
     * @return Model|null
     */
    public static function getOneByIdWithTrashed(int $id, array $fields = ['*']): ?Model
    {
        return static::$model::query()
            ->withTrashed()
            ->select($fields)
            ->find($id);
    }

    /**
     * 通过where查找一条记录
     * @param array $where 查询条件
     * @param array $fields 获取的字段
     * @return Model|null
     */
    public static function getOneByWhere(array $where, array $fields = ['*']): ?Model
    {
        return static::$model::query()
            ->select($fields)
            ->setWhereQuery($where)
            ->first();
    }

    /**
     * 通过where查找一条记录
     * @param array $where 查询条件
     * @param array $fields 获取的字段
     * @return Model|null
     */
    public static function getOneByWhereWithTrashed(array $where, array $fields = ['*']): ?Model
    {
        return static::$model::query()
            ->withTrashed()
            ->select($fields)
            ->setWhereQuery($where)
            ->first();
    }

    /**
     * 获取数据总数
     * @param array $where 查询条件
     * @return int
     * @author lwz
     */
    public static function getCountByWhere(array $where): int
    {
        return (int)static::$model::query()
            ->setWhereQuery($where)
            ->count();
    }

    /**
     * 新增一条数据
     * @param array $data 新增的数据
     * @return Model
     */
    public static function add(array $data): Model
    {
        return static::$model::query()
            ->create($data);
    }

    /**
     * 批量插入
     * @param array $data
     * @return mixed
     * @author lwz
     */
    public static function insert(array $data)
    {
        return static::$model::query()->insert($data);
    }

    /**
     * 插入返回id
     * @param array $data
     * @return mixed
     * @author lwz
     */
    public static function insertGetId(array $data)
    {
        return static::$model::query()->insertGetId($data);
    }

    /**
     * 根据ID更新一条记录
     * @param int $id id
     * @param array $update 更新的数据
     * @return mixed
     */
    public static function updateById(int $id, array $update)
    {
        return static::$model::query()
            ->whereId($id)
            ->update($update);
    }

    /**
     * 根据ID更新一条记录
     * @param array $ids
     * @param array $update
     * @return mixed
     */
    public static function updateByIds(array $ids, array $update)
    {
        return static::$model::query()
            ->whereIn('id', $ids)
            ->update($update);
    }

    /**
     * 批量更新
     * @param array|null $where 查询条件
     * @param array $updateData 更新的数据（更新单个字段，传一维数组；更新多个字段，传二维数组）
     *      [
     *          'field' => '更新的字段',
     *          'case_data' => [
     *              ['when_field' => '判断字段', 'when_val' => '判断的值', 'update_val' => '更新的值']
     *              ['when_raw' => 'when的完整条件', 'update_val' => '更新的值']
     *          ]
     *      ]
     * @throws ValidateException
     * @author lwz
     */
    public static function updateBatch(array $where, array $updateData)
    {
        // 如果存在索引0，则视为更新多个字段
        if (isset($updateData[0])) {
            $updateArr = [];
            foreach ($updateData as $item) {
                $updateArr = array_merge($updateArr, self::getUpdateBatchStr($item));
            }
        } else {
            $updateArr = self::getUpdateBatchStr($updateData);
        }

        return static::$model::query()
            ->setWhereQuery($where)
            ->update($updateArr);
    }

    /**
     * 获取更新字段字符串
     * @param array $updateData 更新的数据
     * @return array
     * @throws ValidateException
     * @author lwz
     */
    protected static function getUpdateBatchStr(array $updateData): array
    {
        // case 参数不能为空
        if (empty($updateData['case_data'] ?? null)) {
            throw new ValidateException('case_data error');
        }

        /**
         * 字符串示例：
         * SET `type` = (CASE
         * WHEN  `name` = 1 THEN 999
         * WHEN  `name` = 2 THEN 1000
         * WHEN  `name` = 3 THEN 1024
         * END)
         */

        $whereRaw = 'CASE';
        foreach ($updateData['case_data'] as $item) {
            if (isset($item['when_raw'])) {
                $whereRaw .= sprintf(' WHEN %s then %s', $item['when_raw'], self::getSqlWhereVal($item['update_val']));
            } else {
//                $whereRaw .= " WHEN {$item['when_field']} = '{$item['when_val']}' then '{$item['update_val']}'";
                $whereRaw .= sprintf(' WHEN %s = %s then %s',
                    $item['when_field'],
                    self::getSqlWhereVal($item['when_val']),
                    self::getSqlWhereVal($item['update_val'])
                );
            }
        }
        $whereRaw .= ' ELSE `' . $updateData['field'] . '` END';

        return [$updateData['field'] => DB::raw($whereRaw)];
    }

    /**
     * 获取 sql 查询值
     * @param mixed $value 查询的值
     * @return mixed|string
     * @author lwz
     */
    protected static function getSqlWhereVal($value)
    {
        // 如果是字符串，加上双引号
        return is_string($value) ? '"' . $value . '"' : $value;
    }

    /**
     * 根据where条件更新一条或多条记录
     * @param array $where 查询条件
     * @param array $update 更新的数据
     * @return mixed
     */
    public static function updateByWhere(array $where, array $update)
    {
        return static::$model::query()
            ->setWhereQuery($where)
            ->update($update);
    }

    /**
     * 根据where条件判断记录是否存在
     *
     * @param array $where
     * @return mixed
     * @Date: 2022/1/15 10:22
     * @Author: ikaijian
     */
    public static function existsByWhere(array $where)
    {
        return static::$model::query()
            ->setWhereQuery($where)
            ->exists();
    }

    /**
     * 根据ID删除一条记录
     * @param int $id id
     * @return mixed
     */
    public static function deleteById(int $id)
    {
        $info = static::$model::query()->find($id);
        $info && $info->delete();
        return $info;
    }

    /**
     * 根据where 删除多条记录
     * @param array $where 查询条件
     * @return int 删除的数据条数
     * @throws DBParamValidException
     */
    public static function deleteByWhere(array $where): int
    {
        if (empty($where)) {
            throw new DBParamValidException('[where] 参数错误');
        }
        return (new static::$model)->setWhereQuery($where)->delete();
    }

    /**
     * 根据 where column自增num
     * @param array $where 查询条件
     * @param string $column 字段
     * @param int $num 自增数量
     * @return mixed
     */
    public static function increment(array $where, string $column, int $num = 1)
    {
        return static::$model::query()
            ->setWhereQuery($where)
            ->increment($column, $num);
    }

    /**
     * 根据 where column自减 num
     * @param array $where 查询条件
     * @param string $column 字段
     * @param int $num 自增数量
     * @return mixed
     */
    public static function decrement(array $where, string $column, int $num = 1)
    {
        return static::$model::query()
            ->setWhereQuery($where)
            ->decrement($column, $num);
    }

    /**
     * 更新或添加数据
     * @param array $where 查询条件
     * @param array $data 更新的数据
     * @return mixed
     * @author lwz
     */
    public static function updateOrInsert(array $where, array $data)
    {
        return static::$model::query()
            ->updateOrInsert($where, $data);
    }

    /**
     * 更新或添加数据（ORM）
     * @param array $where 查询条件
     * @param array $data 更新的数据
     * @return mixed
     * @author lwz
     */
    public static function updateOrCreate(array $where, array $data): Model
    {
        return static::$model::query()
            ->updateOrCreate($where, $data);
    }

    /**
     * 获取列表数据
     * @param array $where where查询条件
     * @param mixed $fields 查询的字段
     * @param string|null $orderField 排序字段
     * @param string|null $orderType 排序类型。DBConst::ORDER_ASC, DBConst::ORDER_DESC
     * @param int|null $maxSize 最大数据量。传 null 或 0 不限制
     * @return Collection
     * @author lwz
     */
    public static function getList(array $where, $fields = ['*'], ?string $orderField = null, ?string $orderType = null, ?int $maxSize = DBConst::RESULT_MAX_SIZE): Collection
    {
        return static::$model::setWhereQuery($where)
            ->when(is_string($fields), function ($query) use ($fields) {
                return empty($fields) ? $query : $query->selectRaw($fields);
            }, function ($query) use ($fields) {
                return empty($fields) ? $query : $query->select($fields);
            })
            ->when($orderField, function ($query, $orderFiled) use ($orderType) {
                $orderType = in_array($orderType, [DBConst::ORDER_ASC, DBConst::ORDER_DESC]) ? $orderType : DBConst::ORDER_DESC;
                $query->orderBy($orderFiled, $orderType);
            })
            ->when($maxSize > 0, function ($query) use ($maxSize) {
                $query->limit($maxSize);
            })
            ->get();
    }

    /**
     * 获取统计列表
     * @param array $where 查询条件
     * @param mixed $fields 统计字段
     * @param null $groupFields groupBy字段
     * @return Collection
     * @author lwz
     */
    public static function getStatList(array $where, $fields = ['*'], $groupFields = null): Collection
    {
        return static::$model::setWhereQuery($where)
            ->when(is_string($fields), function ($query) use ($fields) {
                return empty($fields) ? $query : $query->selectRaw($fields);
            }, function ($query) use ($fields) {
                return empty($fields) ? $query : $query->select($fields);
            })
            ->when($groupFields, function ($query, $groupFields) {
                return $query->groupBy($groupFields);
            })
            ->get();
    }

    /**
     * 获取统计行数
     * @param array $where where查询条件
     * @param string|array $fields 查询的字段
     * @param string $groupField 分组字段
     * @return Collection
     * @author lwz
     */
    public static function getGroupByCount(array $where, $fields, string $groupField): Collection
    {
        return static::$model::setWhereQuery($where)
            ->when(is_string($fields), function ($query) use ($fields) {
                return $query->selectRaw($fields);
            }, function ($query) use ($fields) {
                return $query->select($fields);
            })
            ->groupByRaw($groupField)
            ->get();
    }

    /**
     * 获取分页列表
     * @param int|null $page 页码
     * @param int|null $size 分页大小
     * @param array $where 查询条件
     * @param array|string $fields 获取的字段
     * @param string|null $orderField 排序字段
     * @param string|null $orderType 排序类型。DBConst::ORDER_ASC, DBConst::ORDER_DESC
     * @param bool $getTotal 是否获取数据总数
     * @return array
     * @author lwz
     */
    public static function getPageList(
        ?int $page, ?int $size, array $where, $fields = ['*'],
        ?string $orderField = null, ?string $orderType = null, bool $getTotal = false
    ): array
    {
        return static::$model::query()
            ->when(is_string($fields), function ($query) use ($fields) {
                return empty($fields) ? $query : $query->selectRaw($fields);
            }, function ($query) use ($fields) {
                return empty($fields) ? $query : $query->select($fields);
            })
            ->when($orderField, function ($query, $orderFiled) use ($orderType) {
                $orderType = in_array($orderType, [DBConst::ORDER_ASC, DBConst::ORDER_DESC]) ? $orderType : DBConst::ORDER_DESC;
                $query->orderBy($orderFiled, $orderType);
            })
            ->pageList($page, $size, $where, $getTotal);
    }

    /**
     * 强制删除全部数据（包括软删除数据）
     * @param array $where 查询条件
     * @return bool|mixed|null
     * @throws DBParamValidException
     * @author lwz
     */
    public static function forceDeleteAllData(array $where)
    {
        if (empty($where)) {
            throw new DBParamValidException('缺少查询参数');
        }
        return static::$model::withTrashed()->setWhereQuery($where)->forceDelete();
    }
}
