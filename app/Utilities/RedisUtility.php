<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Redis;

class RedisUtility
{
    public static function saveWithExpire($key, $value, $expirationInSeconds)
    {
        Redis::setex($key, $value, $expirationInSeconds);
    }
    public static function save($key, $value)
    {
        Redis::set($key, $value);
    }
    public static function saveWithCategoryExpire($category, $key, $value, $expirationInSeconds)
    {
        Redis::hset($category, $key, $value);
        Redis::expire($category, $expirationInSeconds);
    }
    public static function saveWithCategory($category, $key, $value)
    {
        Redis::hset($category, $key, $value);
    }
    public static function getByCategory($category, $key, $json_decode)
    {
        if (Redis::ping() == 'PONG' && Redis::hexists($category, $key)) {
            $value = Redis::hget($category, $key);
            return $json_decode ? json_decode($value) : $value;
        }
        return null;
    }
    public static function deleteByCategory($category)
    {
        Redis::del($category);
    }
    public static function deleteByKey($key)
    {
        Redis::del($key);
    }
    public static function deleteByCategoryAndKey($category, $key)
    {
        Redis::hdel($category, $key);
    }
}
