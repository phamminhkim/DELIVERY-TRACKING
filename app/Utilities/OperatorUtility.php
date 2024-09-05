<?php

namespace App\Utilities;

use Exception;

class OperatorUtility
{
    public static function add($a, $b) {
        return $a + $b;
    }
    public static function subtract($a, $b) {
        return $a - $b;
    }
    public static function multiply($a, $b) {
        return $a * $b;
      }
    public static function divide($a, $b) {
        if ($b != 0) {
            return $a / $b;
        } else {
            throw new Exception("Cannot divide by zero");
        }
    }
    public static function getOperator($operator) {
        switch ($operator) {
            case '+':
                return 'add';
            case '-':
                return 'subtract';
            case '*':
                return 'multiply';
            case '/':
                return 'divide';
            default:
                return 'add';
        }
    }
    public static function replaceSeparator($string, $decimal_separator = '.', $thousand_separator = ',') {
        $string = str_replace($thousand_separator, '', $string);
        $string = str_replace($decimal_separator, '.', $string);
        return $string;
    }
    public static function regexMatch($raw_data, $regex, $output_index = 0)
    {
        preg_match($regex, $raw_data, $matches);
        return count($matches) > $output_index ? trim($matches[$output_index]) : null;
    }
    public static function getValueWithCondition($array, $condition)
    {
        $result = "";
        $index = $condition->index;
        $value = $condition->value;
        $action = $condition->action;
        $new_index = $action->new_index;
        $regex_match = $action->regex_match;

        if ($array[$index] ==  $value) {
            preg_match($regex_match, $array[$new_index], $matches);
            $result = count($matches) > 0 ? $matches[0] : null;
        } else {
            $result = $array[$index];
        }
        return $result;
    }

    public static function mergeValue($array, $index_array) {
        $result = "";
        foreach ($index_array as $index) {
            $result .= $array[$index];
        }
        return $result;
    }
    // Hàm replace value
    public static function replaceValue($value, $replace_value) {
        $result = $value;
        foreach ($replace_value as $replace) {
            $result = str_replace($replace['old_value'], $replace['new_value'], $result);
        }
        return $result;
    }

    // Hàm chuyển mã TCVN3 sang Unicode
    public static function convertTCVN3ToUnicode($vnstr)
    {
        try {
            $tcvn3_to_unicode_map = array(
                'a' => 'a', '¸' => 'á', 'µ' => 'à', '¶' => 'ả', '·' => 'ã', '¹' => 'ạ', '¨' => 'ă', '¾' => 'ắ', '»' => 'ằ',
                '¼' => 'ẳ', '½' => 'ẵ', 'Æ' => 'ặ', '©' => 'â', 'Ê' => 'ấ', 'Ç' => 'ầ', 'È' => 'ẩ', 'É' => 'ẫ', 'Ë' => 'ậ',
                'e' => 'e', 'Ð' => 'é', 'Ì' => 'è', 'Î' => 'ẻ', 'Ï' => 'ẽ', 'Ñ' => 'ẹ', 'ª' => 'ê', 'Õ' => 'ế', 'Ò' => 'ề',
                'Ó' => 'ể', 'Ô' => 'ễ', 'Ö' => 'ệ', 'o' => 'o', 'ã' => 'ó', 'ß' => 'ò', 'á' => 'ỏ', 'â' => 'õ', 'ä' => 'ọ',
                '«' => 'ô', 'è' => 'ố', 'å' => 'ồ', 'æ' => 'ổ', 'ç' => 'ỗ', 'é' => 'ộ', '¬' => 'ơ', 'í' => 'ớ', 'ê' => 'ờ',
                'ë' => 'ở', 'ì' => 'ỡ', 'î' => 'ợ', 'i' => 'i', 'Ý' => 'í', '×' => 'ì', 'Ø' => 'ỉ', 'Ü' => 'ĩ', 'Þ' => 'ị',
                'u' => 'u', 'ó' => 'ú', 'ï' => 'ù', 'ñ' => 'ủ', 'ò' => 'ũ', 'ô' => 'ụ', '­' => 'ư', 'ø' => 'ứ', 'õ' => 'ừ',
                'ö' => 'ử', '÷' => 'ữ', 'ù' => 'ự', 'y' => 'y', 'ý' => 'ý', 'ú' => 'ỳ', 'û' => 'ỷ', 'ü' => 'ỹ', 'þ' => 'ỵ',
                '®' => 'đ', 'A' => 'A', '¡' => 'Ă', '¢' => 'Â', 'E' => 'E', '£' => 'Ê', 'O' => 'O', '¤' => 'Ô', '¥' => 'Ơ',
                'I' => 'I', 'U' => 'U', '¦' => 'Ư', 'Y' => 'Y', '§' => 'Đ'
            );

            $output = '';
            $length = mb_strlen($vnstr);
            for ($i = 0; $i < $length; $i++) {
                $c = mb_substr($vnstr, $i,1);
                if (array_key_exists($c, $tcvn3_to_unicode_map)) {
                    $output .= $tcvn3_to_unicode_map[$c];
                } else {
                    $output .= $c;
                }
            }
            return $output;
        } catch (\Throwable $th) {
            return $vnstr;
        }
    }
}
