<?php

namespace App\Services\Implementations\Extractors;

use App\Services\Interfaces\DataExtractorInterface;
use App\Utilities\ApiRequest;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Exception\RequestException;

class AiExtractorService implements DataExtractorInterface
{
    public function extract($file_path, $options)
    {
        $raw_table_data = [];
        $table_area_info = $options['table_area_info'];
        $api_extract_info = $table_area_info->api_extract_info;

        $api_url = $api_extract_info->url;
        $api_method = $api_extract_info->method;
        $body_data = json_decode(json_encode($api_extract_info->body_data), true);
        $api_request = new ApiRequest($api_url, [], $body_data, $api_method);
        $api_response = $this->handleRequest($api_request, $file_path);

        return $raw_table_data;
    }
    public function handleRequest($request, $file_path)
    {
        try {
            //use guzzle
            // $client = new \GuzzleHttp\Client();
            $client = new \GuzzleHttp\Client([
                'verify' => false, // Tạm thời bỏ qua xác thực SSL trên local
            ]);
            $promises = [];

            $body = $request->body;
            if (isset($body['bearer_token'])) {
                $token_info = $body['bearer_token'];
                $file_key_name = $body['file_key_name'];
                $login_response = $client->request($token_info['token_method'], $token_info['token_url'], [
                    'json' => $token_info['token_body'],
                    'headers' => $token_info['token_header'],
                    // 'verify' => false,
                ]);

                $login_data = json_decode($login_response->getBody());
                $token = 'Bearer ' . $login_data->access;

                $promises[] = $client->requestAsync($request->method, $request->url, [
                    'query' => $request->queries,
                    // 'json' => $request->body,
                    'multipart' => [
                        [
                            'name'     => $file_key_name, // Tên của trường trong form-data
                            'contents' => fopen($file_path, 'r'), // Mở tệp để đọc
                            'filename' => basename($file_path) // Tên tệp
                        ]
                    ],
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
