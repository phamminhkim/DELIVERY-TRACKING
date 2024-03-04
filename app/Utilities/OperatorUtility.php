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
}
