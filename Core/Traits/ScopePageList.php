<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 11:41,
 * @LastEditTime: 2021/11/01 11:41
 */

namespace Core\Traits;


use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Core\Library\FormatHelper;

trait ScopePageList
{
    // 分页相关列表设置
    protected int $defaultPageNo = 1; // 默认的页码
    protected int $defaultPageSizeNo = 10; // 默认分页的数据量
    protected int $maxPageSizeNum = 100; // 设置最大的页面数据量（分页列表）
    protected bool $pageDefaultQueryRequired = false; // 分页查询，设置字段是否必传
    protected ?string $pageQueryTableAlias = null; // 分页查询语句表别名

    /**
     * 设置分页
     * @param Builder $query
     * @param string $aliasTableName
     * @return Builder
     * @author lwz
     */
    public function scopeSetTableAlias(Builder $query, string $aliasTableName): Builder
    {
        $this->pageQueryTableAlias = $aliasTableName;
        return $query;
    }

    /**
     * 获取分页列表
     * @param Builder|null $query
     * @param int|null $pageNo 页码
     * @param int|null $pageSize 分页大小
     * @param array $where 查询条件
     * @param bool $getTotal 是否获取数据总数
     * @param int|null $maxPageSize 最大分页数
     * @return array
     * @author lwz
     */
    public function scopePageList(
        Builder $query, ?int $pageNo = null, ?int $pageSize = null,
        array $where = [], bool $getTotal = false, ?int $maxPageSize = null
    ): array
    {
        $maxPageSize && $this->maxPageSizeNum = $maxPageSize;
        $pageNo = $this->getPageNumber($pageNo);
        $pageSize = $this->getPageSizeNumber($pageSize);

        // 获取查询对象
        $query = $query->setWhereQuery($where, $this->pageQueryTableAlias, $this->pageDefaultQueryRequired);

        $total = null;
        // 获取总页数的情况
        if ($getTotal) {
            // 获取数据总数
            $total = $this->handleGetPageCountNumber($query);
            // 获取不到数据直接返回
            if (!$total) {
                return FormatHelper::getPageListResponse(new \Illuminate\Database\Eloquent\Collection, $pageNo, $pageSize, $total);
            }
        }

        // 获取数据列表
        $list = $query->offset($this->getPageOffsetNumber($pageNo, $pageSize))
            ->limit($pageSize)
            ->get();
        return FormatHelper::getPageListResponse($list, $pageNo, $pageSize, $total);
    }

    /**
     * 获取统计数量
     * @param \Illuminate\Database\Eloquent\Builder|static $query
     * @param array $columns
     * @return int
     * @author lwz
     */
    protected function handleGetPageCountNumber($query): int
    {
        return $query->toBase()->getCountForPagination();
    }

    /**
     * 获取分页页码
     * @param int|null $pageNo
     * @return int
     * @author lwz
     */
    protected function getPageNumber(?int $pageNo): int
    {
        return is_numeric($pageNo) && $pageNo > 0 ? $pageNo : $this->defaultPageNo;
    }

    /**
     * 获取分页页码数据量
     * @param int|null $pageSize
     * @return int
     * @author lwz
     */
    protected function getPageSizeNumber(?int $pageSize): int
    {
        return is_numeric($pageSize) && $pageSize > 0 ?
            ($pageSize > $this->maxPageSizeNum ? $this->maxPageSizeNum : $pageSize) :
            $this->defaultPageSizeNo;
    }

    /**
     * 获取分页时，数据的偏移量
     * @param int $pageNo
     * @param int $pageSize
     * @return int
     * @author lwz
     */
    protected function getPageOffsetNumber(int $pageNo, int $pageSize): int
    {
        return ($pageNo - 1) * $pageSize;
    }
}
