<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Http;
use App\Utilities\ApiRequest;
use App\Enums\ApiRequestMethod;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\Utils;

class ApiHandler
{
    public static function get($url, $queries = [])
    {
        $response = Http::get($url, $queries);
        return $response->throw()->json();
    }

    public static function post($url, $queries = [], $body = [])
    {
        $query_string = http_build_query($queries);
        $response = Http::post($url . "?" . $query_string, $body);
        return $response->throw()->json();
    }

    public static function put($url, $queries = [], $body = [])
    {
        $query_string = http_build_query($queries);
        $response = Http::put($url . "?" . $query_string, $body);
        return $response->throw()->json();
    }

    public static function delete($url, $queries = [])
    {
        $query_string = http_build_query($queries);
        $response = Http::delete($url . "?" . $query_string);
        return $response->throw()->json();
    }

    public static function handleMultipleRequests($requests)
    {
        try {
            //use guzzle
            $client = new \GuzzleHttp\Client();
            $promises = [];
            foreach ($requests as $request) {
                $body = $request->body;
                if (isset($body['bearer_token'])) {
                    $token_info = $body['bearer_token'];
                    $login_response = $client->request($token_info['token_method'], $token_info['token_url'], [
                        'form_params' => $token_info['token_body'],
                        'headers' => $token_info['token_header'],
                    ]);
                    $login_data = json_decode($login_response->getBody());
                    $token = 'Bearer ' . $login_data->access_token;
                    $promises[] = $client->requestAsync($request->method, $request->url, [
                        'query' => $request->queries,
                        'json' => $request->body,
                        'headers' => [
                            'Authorization' => $token
                        ]
                    ]);
                } else {
                    $promises[] = $client->requestAsync($request->method, $request->url, [
                        'query' => $request->queries,
                        'json' => $request->body
                    ]);
                }
            }
            $results = Utils::all($promises)->wait();
            $responses = [];
            foreach ($results as $result) {
                $responses[] = json_decode($result->getBody()->getContents(), true);
            }
            return $responses;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
