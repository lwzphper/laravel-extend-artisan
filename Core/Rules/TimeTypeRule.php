<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Core\Library\TimeHelper;

/**
 * Class TimeTypeRule
 * @package Core\Rules
 * @author lwz
 * 时间类型验证
 */
class TimeTypeRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, TimeHelper::$timeType);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '时间类型有误';
    }
}
