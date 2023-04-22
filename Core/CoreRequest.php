<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 13:38,
 * @LastEditTime: 2021/11/01 13:38
 */

namespace Core;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Core\Constants\DBConst;
use Core\Exceptions\ValidateException;
use Core\Library\ArrMacro;

class CoreRequest extends FormRequest
{
    public const NULL_TO_STRING = 1; // 将null转化为字符串
    public const NULL_TO_DELETE = 2; // 将null删除

    /**
     * 场景
     * @var string|null
     */
    protected ?string $scene = null;

    /**
     * Indicates whether validation should stop after the first rule failure.
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * 是否自动验证
     * @var bool
     */
    protected bool $autoValidate = false;

    /**
     * @var array
     */
    protected array $onlyRule = [];

    /**
     * 分页字段规则
     * @var array|string[][]
     */
    protected array $pageRule = [
        'page' => ['bail', 'nullable', 'integer'],
        'size' => ['bail', 'nullable', 'integer'],
        'page_size' => ['bail', 'nullable', 'integer'],
    ];

    // 数据库开启关闭规则
    protected array $dbOnOffRule = [
        DBConst::DISABLE,
        DBConst::ENABLE,
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 统一响应校验失败信息
     * @param Validator $validator
     * @throws ValidateException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidateException($validator->errors()->first());
    }

    /**
     *  覆盖 ValidatesWhenResolvedTrait 下 validateResolved 自动验证
     */
    public function validateResolved()
    {
        if (method_exists($this, 'autoValidate')) {
            $this->autoValidate = $this->container->call([$this, 'autoValidate']);
        }
        if ($this->autoValidate) {
            $this->handleValidate();
        }
    }

    /**
     * 复制 ValidatesWhenResolvedTrait -> validateResolved 自动验证
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws ValidateException
     */
    protected function handleValidate()
    {
        $this->prepareForValidation();

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $instance = $this->getValidatorInstance();

        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }

    /**
     * 定义 getValidatorInstance 下 validator 验证器
     * @param $factory
     * @return mixed
     */
    public function validator($factory)
    {
        return $factory->make($this->validationData(), $this->getRules(), $this->messages(), $this->attributes());
    }

    /**
     * 验证方法（关闭自动验证时控制器调用）
     * @param string $scene 场景名称 或 验证规则
     */
    public function validate($scene = '')
    {
        if (!$this->autoValidate) {
            if (is_array($scene)) {
                $this->onlyRule = $scene;
            } else {
                $this->scene = $scene;
            }
            $this->handleValidate();
        }
    }

    /**
     * 获取 rules
     * @return array
     */
    protected function getRules(): array
    {
        return $this->handleScene($this->container->call([$this, 'rules']));
    }

    /**
     * 场景验证
     * @param array $rule
     * @return array
     */
    protected function handleScene(array $rule): array
    {
        if ($this->onlyRule) {
            return $this->handleRule($this->onlyRule, $rule);
        }
        $sceneName = $this->getSceneName();
        if ($sceneName && method_exists($this, 'scene')) {
            $scene = $this->container->call([$this, 'scene']);
            if (array_key_exists($sceneName, $scene)) {
                return $this->handleRule($scene[$sceneName], $rule);
            }
        }
        return $rule;
    }

    /**
     * 处理Rule
     * @param array $sceneRule
     * @param array $rule
     * @return array
     */
    private function handleRule(array $sceneRule, array $rule): array
    {
        $rules = [];
        foreach ($sceneRule as $key => $value) {
            if (is_numeric($key) && array_key_exists($value, $rule)) {
                $rules[$value] = $rule[$value];
            } else {
                $rules[$key] = $value;
            }
        }
        return $rules;
    }

    /**
     * 获取场景名称
     *
     * @return string
     */
    protected function getSceneName(): ?string
    {
//        return is_null($this->scene) ? $this->route()->getAction('_scene') : $this->scene;
        return $this->scene;
    }

    public function rules(): array
    {
        return [];
    }

    /**
     * 场景规则
     * @return array
     */
    public function scene(): array
    {
        return [];
    }

    /**
     * 获取属性名称
     * @param string $field
     * @return string
     */
    protected function getAttributeName(string $field): string
    {
        return $this->attributes()[$field] ?? $field;
    }

    /**
     * 获取所有类型请求对应的场景合法的参数
     * @param string $sceneName
     * @param bool $onlyPost 是否只获取post数据
     * @param int|null $actionType 操作类型
     * @return array
     */
    public function getSceneValue(string $sceneName, bool $onlyPost = false, ?int $actionType = null): array
    {
        $fields = $this->scene()[$sceneName];
        $fields = $onlyPost
            ? Arr::only($this->post(), $fields)
            : $this->only($fields);

        switch ($actionType) {
            case self::NULL_TO_STRING:
                $fields = ArrMacro::nullToString($fields);
                break;
            case self::NULL_TO_DELETE:
                $fields = array_filter($fields, function ($val) {
                    return !is_null($val);
                });
                break;
        }

        return $fields;
    }
}
