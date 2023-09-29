<?php

namespace App\Utilities;

class ApiUtility
{
    public static function getPropertyByPath($object, $path, $delimiter)
    {
        if (!is_array($object) || empty($path)) {
            return $object;
        }
        $parts = explode($delimiter, $path);
        $current = $object;

        foreach ($parts as $part) {
            $current = $current[$part];
        }

        return $current;
    }
}
