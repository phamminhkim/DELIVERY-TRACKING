<?php

namespace App\Enums\Abs;

abstract class Enum
{
    static public function getAllConstants()
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
