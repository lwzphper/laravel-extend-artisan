<?php
/**
 * @Author: renxianyong <876187035@qq.com>
 * @Date: 2022/9/26 14:03
 * @LastEditTime: 2022/9/26 14:03
 * @Copyright: 2021 Core Inc. 保留所有权利。
 */

namespace Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SetWhereMore
{
    /**
     * 批量in查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereIns(Builder $query, $value) :Builder
    {
        foreach ($value as $item) {
            $query->whereIn($item[0], $item[1]);
        }
        return $query;
    }

    /**
     * 批量模糊查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereLikes(Builder $query, $value) :Builder
    {
        foreach ($value as $item) {
            $query->where($item[0], 'like', '%'.$item[1].'%');
        }
        return $query;
    }

    /**
     * 批量Or查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereOrs(Builder $query, $value) :Builder
    {
        foreach ($value as $item) {
            if (!empty($item[2])) {
                $query->orWhere(function ($query) use ($item) {
                    $query->where($item[0], $item[1])->where($item[2], $item[3]);
                });
            } else {
                $query->orWhere($item[0], $item[1]);
            }
        }
        return $query;
    }

    /**
     * 批量whereBetween查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereBetweens(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->whereBetween($item[0], $item[1]);
        }
        return $query;
    }

    /**
     * 批量大于等于查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereMoreEquals(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->where($item[0], '>=',$item[1]);
        }
        return $query;
    }

    /**
     * 批量小于等于查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereLessEquals(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->where($item[0], '<=',$item[1]);
        }
        return $query;
    }

    /**
     * 批量不等于查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereNotEquals(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->where($item[0], '<>',$item[1]);
        }
        return $query;
    }

    /**
     * 批量关联查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereHave(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->whereHas($item[0], function ($query) use ($item) {
                if (count($item) === 3) {
                    $query->where($item[1], $item[2]);
                }
                if (count($item) === 4) {
                    if ($item[2] === 'like') {
                        $query->where($item[1], $item[2], '%'.$item[3].'%');
                    } else {
                        $query->where($item[1], $item[2], $item[3]);
                    }
                }
            });
        }
        return $query;
    }

    /**
     * 批量排序
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereOrderBys(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->orderBy($item[0], $item[1]);
        }
        return $query;
    }

    /**
     * 批量分组
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereGroupBys(Builder $query, $value): Builder
    {
        foreach ($value as $item) {
            $query->groupBy($item);
        }
        return $query;
    }

    /**
     * 原生sql查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereSelectRaw(Builder $query, $value) :Builder
    {
        return $query->selectRaw($value);
    }

    /**
     * 批量模糊or查询
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function scopeWhereOrLikes(Builder $query, $value) :Builder
    {
        $query->where(function ($query) use ($value) {
            foreach ($value as $key => $item) {
                if ($key === 0) {
                    $query->where($item[0], 'like', '%'.$item[1].'%');
                } else {
                    $query->orWhere($item[0], 'like', '%'.$item[1].'%');
                }
            }
        });
        return $query;
    }
}