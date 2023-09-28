<?php

namespace App\Utilities;

use App\Enums\ApiRequestMethod;

class ApiRequest
{
    public $url;
    public $queries;
    public $body;
    public $method;

    public function __construct($url, $queries = [], $body = [], $method = ApiRequestMethod::GET)
    {
        $this->url = $url;
        $this->queries = $queries;
        $this->body = $body;
        $this->method = $method;
    }
}
