<?php

if (!function_exists('getRatio')) {
    /**
     * 获取比率
     * @param int|float $dividend 被除数
     * @param int|float $divisor 除数
     * @param bool $addPercentSign 是否添加 % 符号
     * @author lwz
     */
    function getRatio($dividend, $divisor, bool $addPercentSign = true)
    {
        $result = $divisor > 0 ? round($dividend / $divisor, 4) : 0;
        return $addPercentSign ? $result * 100 . '%' : $result;
    }
}

if (!function_exists('checkIsReleaseEnv')) {
    /**
     * 判断是否正式环境
     * @author lwz
     */
    function checkIsReleaseEnv(): bool
    {
        return config('app.env') == 'release';
    }
}

if (!function_exists('getFirstCharter')) {
    //获取中文字符拼音首字母（可以用于php处理联系人顺序的电话本）
    function getFirstCharter($str): string
    {
        if (empty($str)) {
            return '';
        }
        $fchar = ord($str[0]);
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str[0]);
        }
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return '#';
    }
}

if (!function_exists('encryptPhone')) {
    /**
     * 手机加密
     * @param string $phone
     * @return string
     */
    function encryptPhone(string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $length = strlen($phone); // 计算手机号码的长度
        if ($length < 6) {
            return $phone;
        }
        $middleNum = ceil($length / 2) - 1; // 计算中间位置
        $encryptNum = min($length - $middleNum, 4);
        $startNum = $middleNum - floor($encryptNum / 2);
        return substr($phone, 0, $startNum) . str_repeat('*', $encryptNum) . substr($phone, $startNum + $encryptNum);
    }
}


if (!function_exists('toChineseNum')) {
    /**
     * 阿拉伯数字转中文
     * @param int $num
     * @return string
     */
    function toChineseNum(int $num): string
    {
        //节权位的位置
        $unitSectionPos = 0;
        $chnStr = '';
        //单个数字转换用的数组
        $chnNumChar = ["零", "一", "二", "三", "四", "五", "六", "七", "八", "九"];
        //节权位转换用的数组
        $chnUnitSection = ["", "万", "亿", "万亿", "亿亿"];
        //节内权位换算的数组
        $chnUnitChar = ["", "十", "百", "千"];
        $needZero = false;

        if ($num === 0) {
            return $chnNumChar[0];
        }

        //节内换算的闭包
        $sectionToChinese = function ($section) use ($chnNumChar, $chnUnitChar) {
            $chnStr = '';
            //节内的位置
            $unitPos = 0;
            $zero = true;

            while ($section > 0) {
                $v = $section % 10;
                if ($v === 0) {
                    if (!$zero) {
                        $zero = true;
                        $chnStr = $chnNumChar[$v] . $chnStr;
                    }
                } else {
                    $zero = false;
                    $strIns = $chnNumChar[$v];
                    $strIns .= $chnUnitChar[$unitPos];
                    $chnStr = $strIns . $chnStr;
                }
                $unitPos++;
                $section = floor($section / 10);
            }
            return $chnStr;
        };

        while ($num > 0) {
            $section = $num % 10000;
            if ($needZero) {
                $chnStr = $chnNumChar[0] . $chnStr;
            }
            $strIns = $sectionToChinese($section);
            $strIns .= ($section !== 0) ? $chnUnitSection[$unitSectionPos] : $chnUnitSection[0];
            $chnStr = $strIns . $chnStr;
            $needZero = ($section < 1000) && ($section > 0);
            $num = floor($num / 10000);
            $unitSectionPos++;
        }

        $search = '一十';
        $replacement = '十';
        //处理含一十开头的（这个可根据需求处理）
        if (mb_strpos($chnStr, $search) === 0) {
            $position = strpos($chnStr, $search);
            $chnStr = substr_replace($chnStr, $replacement, $position, strlen($search));
        }

        return $chnStr;
    }
}


