<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/24 15:32,
 * @LastEditTime: 2021/11/24 15:32
 */

namespace Core;


use Illuminate\Database\Eloquent\Model;
use Core\Exceptions\DBParamValidException;
use Core\Traits\ScopePageList;
use Core\Traits\ScopeQuery;

/**
 * Class BaseModel
 * @package Core
 *
 * @method static \Illuminate\Database\Query\Builder|CoreModel setWhereQuery(array $where, ?string $tableAlias = null, bool $defaultRequired = false)
 * @method static array pageList(?int $pageNo = null, ?int $pageSize = null, array $where = [], bool $getTotal = false)
 */
class CoreModel extends Model
{
    use ScopePageList, ScopeQuery;

    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'int'
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function encodeJson($data, bool $emptyToNull = false): ?string
    {
        if (empty($data) || $data == 'null') {
            return $emptyToNull ? null : '';
        }

        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($data === false) {
            throw new DBParamValidException('json转化失败');
        }
        return $data;
    }

    protected function decodeJson(?string $data): array
    {
        return $data ? json_decode($data, true) : [];
    }
}
