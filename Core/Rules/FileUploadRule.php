<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Core\Library\FileHelper;

/**
 * Class DbOnOffStatus
 * @package Core\Rules
 * @author lwz
 * 数据库开关状态验证
 */
class FileUploadRule implements Rule
{
    public const MAX_FILE_NUM = 9; // 最大文件数

    // 文件类型
    public const FILE_TYPE_IMAGE = 1; // 图片
    public const FILE_TYPE_DOC = 2; // 文档文件
    public static array $fileTypeExt = [
        self::FILE_TYPE_IMAGE => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
        self::FILE_TYPE_DOC => ['pdf', 'doc', 'docx'],
    ];
    public static array $fileTypeDesc = [
        self::FILE_TYPE_IMAGE => '图片',
        self::FILE_TYPE_DOC => '文档',
    ];

    protected ?Request $request;

    /**
     * 最大文件数
     * @var int
     */
    protected int $maxSize;

    /**
     * 错误信息
     * @var string
     */
    protected string $errMsg = '文件参数有误';

    /**
     * 文件名称
     * @var bool
     */
    protected bool $withFileName;

    /**
     * 文件类型
     * @var int|null
     */
    protected ?int $fileType;

    /**
     * FileUploadRule constructor.
     * @param Request|null $request
     * @param int|null $maxSize
     * @param bool $withFileName 是否需要文件名称。name：文件名；path：文件路径；size：文件大小字节数
     * @param int|null $fileType 文件类型
     */
    public function __construct(?Request &$request = null, ?int $maxSize = null, bool $withFileName = false, ?int $fileType = null)
    {
        $this->request = $request;
        $this->maxSize = $maxSize ?? self::MAX_FILE_NUM;
        $this->withFileName = $withFileName;
        $this->fileType = $fileType;
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
        if (!is_array($value)) {
            $this->errMsg = '文件参数必须为数组';
            return false;
        }

        if (count($value) > $this->maxSize) {
            $this->errMsg = '最多允许上传' . $this->maxSize . '个文件';
            return false;
        }

        // 文件类型
        $fileExt = self::$fileTypeExt[$this->fileType] ?? null;
        $fileExtDesc = self::$fileTypeDesc[$this->fileType] ?? '';

        if ($this->withFileName) {
            foreach ($value as $key => $item) {
                $value[$key] = $item = is_array($item) ? $item : json_decode($item, true);
                if (!is_array($item)) {
                    return false;
                }

                $filePath = $item['path'] ?? null;
                $fileSize = $item['size'] ?? null;

                // 校验格式
                if (empty($item['name'] ?? null)) {
                    $this->errMsg = '请求上传文件名';
                    return false;
                }
                if ($this->checkFilePath($filePath) === false) {
                    $this->errMsg = '文件路径有误';
                    return false;
                }
                if (empty($fileSize)) {
                    $this->errMsg = '文件大小有误';
                    return false;
                }

                // 校验文件类型
                if ($this->checkFileExtAndSetErrMsg($filePath, $fileExt, $fileExtDesc) === false) {
                    return false;
                }

                // 将文件字节数转化为KB、MB等格式
                $value[$key]['size'] = is_numeric($fileSize) ? FileHelper::getFilesize($fileSize) : $fileSize;
            }
            $this->request->merge([$attribute => $value]);
        } else { // 图片格式避免传的非二维数组
            foreach ($value as $filePath) {
                // 校验文件路径
                if ($this->checkFilePath($filePath) === false) {
                    $this->errMsg = '文件格式有误';
                    return false;
                }

                // 校验文件类型
                if ($this->checkFileExtAndSetErrMsg($filePath, $fileExt, $fileExtDesc) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 检查文件大小格式
     * @param $size
     * @return bool
     * @author lwz
     */
    private function checkFileSize($size): bool
    {
        return is_numeric($size);
    }

    /**
     * 检查路径后缀
     * @param string $filePath 文件类型
     * @param array|null $extArr 文件后缀数组
     * @param string|null $extDesc 后缀描述
     * @return bool
     * @author lwz
     */
    private function checkFileExtAndSetErrMsg(string $filePath, ?array $extArr = null, ?string $extDesc = null): bool
    {
        if (empty($extArr)) {
            return true;
        }
        if (in_array(strtolower(FileHelper::getPathExt($filePath)), $extArr)) {
            return true;
        }

        // 错误的情况
        $this->errMsg = $extDesc ? '只支持' . $extDesc . '类型文件' : '不支持所选文件类型';
        return false;
    }

    /**
     * 检查文件路径
     * @param $filePath
     * @return bool
     * @author lwz
     */
    private function checkFilePath($filePath): bool
    {
        return !empty($filePath) && is_string($filePath);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errMsg;
    }
}
