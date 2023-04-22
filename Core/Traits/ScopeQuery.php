<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 11:41,
 * @LastEditTime: 2021/11/01 11:41
 */

namespace Core\Traits;


use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Core\Exceptions\DBParamValidException;

trait ScopeQuery
{
    /**
     * 设置where查询条件
     * @param Builder|null $query
     * @param array|null $where
     * @param string|null $tableAlias
     * @return Builder
     * @author lwz
     */
    public function scopeSetWhereQuery(?Builder $query, ?array $where, ?string $tableAlias = null): ?Builder
    {
        if (!empty($where)) {
            /**
             * where数组的命名规范：
             *      [
             *          '字段1' => '值'
             *      ]
             *
             * 查询方式：
             *
             */
            foreach ($where as $field => $value) {
                $whereMethod = 'where' . ucfirst(Str::camel($field));
                // 检查对应的scope方法有没有定义，就使用默认的where查询(考虑到有where_in等其他情况，因此使用scope方式调用)
                if (!method_exists($this, 'scope' . ucfirst($whereMethod))) {
                    $query->{is_array($value) ? 'whereIn' : 'where'}($tableAlias ? $tableAlias . '.' . $field : $field, $value);
                    continue;
                }
                $query->{$whereMethod}($value, $tableAlias);
            }
        }
        return $query;
    }

    /**
     * 主键查询
     * @param Builder $query
     * @param $id
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    public function scopeWhereId(Builder $query, $id, ?string $tableAlias = null): Builder
    {
        return $this->setScopeWhere($query, 'id', $id, true, $tableAlias);
    }

    /**
     * 设置主键whereIn查询
     * @param Builder $query
     * @param array $value
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    public function scopeWhereIdIn(Builder $query, array $value, ?string $tableAlias = null): Builder
    {
        return $this->setScopeBasicWhereIn($query, 'id', $value, true, $tableAlias);
    }

    /**
     * 设置主键whereIn查询
     * @param Builder $query
     * @param array $value
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    public function scopeWhereIds(Builder $query, array $value, ?string $tableAlias = null): Builder
    {
        return $this->scopeWhereIdIn($query, $value, $tableAlias);
    }

    /**
     * 设置主键whereIn查询
     * @param Builder $query
     * @param array $value
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    public function scopeWhereIdNotIn(Builder $query, array $value, ?string $tableAlias = null): Builder
    {
        return $this->setScopeBasicWhereNotIn($query, 'id', $value, true, $tableAlias);
    }

    /**
     * 根据查询参数的数量，设置where或whereIn条件
     * @param Builder $query
     * @param string $field
     * @param $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @param string $separator 字符串的分割符
     * @return mixed
     * @author lwz
     */
    protected function setScopeWhereOrWhereIn(Builder $query, string $field, $value, bool $isRequired = true, ?string $tableAlias = null, string $separator = ',')
    {
        $values = is_array($value) ? $value : explode($separator, $value);
        if (count($values) == 1) {
            $funcName = 'setScopeWhere';
        } else {
            $funcName = 'setScopeBasicWhereIn';
            $value = $values;
        }

        return $this->$funcName($query, $field, $value, $isRequired, $tableAlias);
    }

    /**
     * 设置where条件
     * @param Builder $query
     * @param string $field
     * @param $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @param string $operator
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function setScopeWhere(Builder $query, string $field, $value, bool $isRequired = true, ?string $tableAlias = null, string $operator = '='): Builder
    {
        return $this->scopeBasicQuery($query, $field, $value, $isRequired, $tableAlias, 'where', $operator);
    }

    /**
     * like查询
     * @param Builder $query
     * @param string $field
     * @param $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function setScopeWhereLike(Builder $query, string $field, $value, bool $isRequired = true, ?string $tableAlias = null): Builder
    {
        // 判断值是否必传
        $this->checkScopeValueEmptyOrFail($field, $value, $isRequired);
        // 空值直接返回
        if ($this->checkIsEmptyValue($value)) {
            return $query;
        }
        return $this->setScopeWhere($query, $field, '%' . $value . '%', $isRequired, $tableAlias, 'like');
    }

    /**
     * 设置whereBetween条件
     * @param Builder $query
     * @param string $field
     * @param array $value
     * @param bool $required
     * @param null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function setScopeWhereBetween(Builder $query, string $field, array $value, bool $required = true, $tableAlias = null): Builder
    {
        return $this->scopeBasicQuery($query, $field, $value, $required, $tableAlias, 'whereBetween');
    }

    /**
     * 全文索引查询条件
     * @param Builder $query
     * @param string $field
     * @param $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function setScopeFullIndexWhere(Builder $query, string $field, $value, bool $isRequired = true, ?string $tableAlias = null): Builder
    {
        // 检查参数是否必填
        $this->checkScopeValueEmptyOrFail($field, $value, $isRequired);
        // 设置字段名
        $fieldName = $this->getScopeField($field, $tableAlias);
        // 返回
        return !is_numeric($value) && empty($value) ? $query : $query->whereRaw('MATCH(' . $fieldName . ') AGAINST(?)', [$value]);
    }

    /**
     * 设置wherein条件
     * @param Builder $query
     * @param string $field
     * @param array $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function setScopeBasicWhereIn(Builder $query, string $field, array $value, bool $isRequired = true, ?string $tableAlias = null): Builder
    {
        $value = $this->arrayUnique($value);
        $whereName = 'whereIn';
        if (count($value) == 1) {
            $whereName = 'where';
            $value = $value[0];
        }
        return $this->scopeBasicQuery($query, $field, $value, $isRequired, $tableAlias, $whereName);
    }

    /**
     * 设置whereNotIn条件
     * @param Builder $query
     * @param string $field
     * @param array $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function setScopeBasicWhereNotIn(Builder $query, string $field, array $value, bool $isRequired = true, ?string $tableAlias = null): Builder
    {
        return $this->scopeBasicQuery($query, $field, $this->arrayUnique($value), $isRequired, $tableAlias, 'whereNotIn');
    }

    /**
     * 数组去重
     * @param array $value
     * @return array
     * @author lwz
     */
    protected function arrayUnique(array $value): array
    {
        return array_values(array_unique($value));
    }

    /**
     * 通用的查询条件
     * @param Builder $query
     * @param string $field
     * @param $value
     * @param bool $isRequired
     * @param string|null $tableAlias
     * @param string $where
     * @param string|null $operator
     * @return Builder
     * @throws DBParamValidException
     * @author lwz
     */
    protected function scopeBasicQuery(Builder $query, string $field, $value, bool $isRequired = true, ?string $tableAlias = null, string $where = 'where', string $operator = null): Builder
    {
        // 检查参数是否必填
        $this->checkScopeValueEmptyOrFail($field, $value, $isRequired);

        // 生成查询方法的参数
        $queryParams = [];
        $queryParams[] = $this->getScopeField($field, $tableAlias);
        !is_null($operator) && $queryParams[] = $operator;
        $queryParams[] = $value;

        return !is_numeric($value) && empty($value) ? $query : $query->$where(...$queryParams);
    }

    /**
     * 检查where语句的值是否为空
     * @param string $field
     * @param $value
     * @param bool $isRequired
     * @throws DBParamValidException
     * @author lwz
     */
    protected function checkScopeValueEmptyOrFail(string $field, $value, bool $isRequired)
    {
        // !is_numeric() 是为了防止 数字 0 视为空的问题
        if ($isRequired && $this->checkIsEmptyValue($value)) {
            throw new DBParamValidException('[scope error]' . $field . ' empty');
        }
    }

    /**
     * 检查是否空值
     * @param $value
     * @return bool
     */
    protected function checkIsEmptyValue($value): bool
    {
        return !is_numeric($value) && empty($value);
    }

    /**
     * 获取字段名
     * @param string $field
     * @param string|null $tableAlias
     * @return string
     * @author lwz
     */
    protected function getScopeField(string $field, ?string $tableAlias = null): string
    {
        return $tableAlias ? $tableAlias . '.' . $field : $field;
    }
}
