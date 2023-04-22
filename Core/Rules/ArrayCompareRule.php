<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

/**
 * 两个数组包含关系校验
 * Class DbOnOffStatus
 * @package Core\Rules
 * @author lwz
 */
class ArrayCompareRule implements Rule
{
    private Request $request;
    private array $haystack;
    private ?int $needMaxNum;
    private string $errMsgExt = '有误';

    /**
     * CheckIdsRule constructor.
     * @param Request|null $request
     * @param array $haystack 数据堆
     * @param int|null $needMaxNum 最大允许的数量
     */
    public function __construct(Request &$request, array $haystack, ?int $needMaxNum = null)
    {
        $this->request = $request;
        $this->haystack = $haystack;
        $this->needMaxNum = $needMaxNum;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = array_unique(is_string($value) ? explode(',', $value) : $value);
        if ($this->needMaxNum && count($value) > $this->needMaxNum) {
            $this->errMsgExt = '超过限制数量';
            return false;
        }

        if (array_diff($value, $this->haystack)) {
            return false;
        }

        // tips： 如果传了 request 参数，会将数据替换为数组
        $this->request && $this->request->merge([
            $attribute => $value,
        ]);
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute ' . $this->errMsgExt;
    }
}
