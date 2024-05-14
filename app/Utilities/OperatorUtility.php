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
    public static function regexMatch($raw_data, $regex)
    {
        preg_match($regex, $raw_data, $matches);
        return count($matches) > 0 ? $matches[0] : null;
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


}
