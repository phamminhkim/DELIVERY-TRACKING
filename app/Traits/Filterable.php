<?php

namespace App\Traits;

use Illuminate\Support\Str;


trait Filterable
{
    public function scopeFilter($query, $request, $exclude_filter_fields = [])
    {
        $params = $request->all();
        foreach ($params as $field => $value) {
            $method = 'filter' . Str::studly($field);
            if (method_exists($this, $method) && !in_array($field, $exclude_filter_fields)) {
                $this->{$method}($query, $request);
            }
        }

        return $query;
    }
}
