<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/09/16 10:34,
 * @LastEditTime: 2022/09/16 10:34
 */
declare(strict_types=1);

namespace Core\Library;

/**
 * 文件辅助类
 * Class FileHelper
 * @package Core\Library
 * @author lwz
 */
class FileHelper
{
    protected static int $kBit = 1024;
    protected static int $kbBit = 1048576; // 1024 * 1024
    protected static int $mbBit = 1073741824;  // 1024 * 1024 * 1024
    protected static int $gbBit = 1099511627776; // 1024 * 1024 * 1024 * 1024
    protected static int $tbBit = 1125899906842624; // 1024 * 1024 * 1024 * 1024 * 1024

    /**
     * 获取文件大小
     * @param int $size 文件大小（字节数）
     * @return string
     * @author lwz
     */
    public static function getFilesize(int $size): string
    {
        $p = 0;
        $format = 'bytes';
        if ($size > 0 && $size < self::$kBit) {
            return number_format($size) . ' ' . $format;
        } elseif ($size >= 1024 && $size < self::$kbBit) {
            $p = 1;
            $format = 'KB';
        } elseif ($size >= self::$kbBit && $size < self::$mbBit) {
            $p = 2;
            $format = 'MB';
        } elseif ($size >= self::$mbBit && $size < self::$gbBit) {
            $p = 3;
            $format = 'GB';
        } elseif ($size >= self::$gbBit && $size < self::$tbBit) {
            $p = 3;
            $format = 'TB';
        }
        $size /= pow(1024, $p);

        return number_format($size, 2) . $format;
    }

    /**
     * 获取文件后缀
     * @param string $path 文件路径
     * @return string
     * @author lwz
     */
    public static function getPathExt(string $path): string
    {
        return substr($path, strrpos($path, '.') + 1) ?: '';
    }

    /**
     * 将文件路径格式化成json格式化
     * @param array $filePaths 文件路径
     * @param bool $emptyToNull 是否将空字符串转化为 null
     * @return string|null
     * @author lwz
     */
    public static function encodeJsonPaths(array $filePaths, bool $emptyToNull = false): ?string
    {
        return $filePaths ? json_encode($filePaths, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ($emptyToNull ? null : '');
    }

    /**
     * 将json格式文件路径，格式化数组
     * @param string|null $filePaths
     * @return array
     * @author lwz
     */
    public static function decodeJsonPaths(?string $filePaths): array
    {
        return $filePaths ? (json_decode($filePaths, true) ?? []) : [];
    }
}
