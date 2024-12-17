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

        // if (isset($api_response[0]['data'][0]['content'])) {
        //     $raw_table_data = $api_response[0]['data'][0]['content'];
        // }
        if (isset($api_response[0]['data'])) {
            $raw_table_data = $api_response[0]['data'];
        }

        return $raw_table_data;
    }
    public function handleRequest($request, $file_path)
    {
        try {
            $client = new \GuzzleHttp\Client([
                'verify' => false, // Tạm thời bỏ qua xác thực SSL
            ]);
            $promises = [];

            $body = $request->body;
            if (isset($body['bearer_token'])) {
                $token_info = $body['bearer_token'];
                $file_key_name = $body['file_key_name'];
                try {
                    $login_response = $client->request($token_info['token_method'], $token_info['token_url'], [
                        'json' => $token_info['token_body'],
                        'headers' => $token_info['token_header'],
                    ]);
                } catch (\Throwable $th) {
                    throw $th;
                }

                $login_data = json_decode($login_response->getBody());
                $token = 'Bearer ' . $login_data->data->access;

                $file_paths = [
                    $file_path
                ];
                $multipart = [];
                foreach ($file_paths as $path) {
                    $multipart[] = [
                        'name'     => $file_key_name, // Tên của trường trong form-data
                        'contents' => fopen($path, 'r'), // Mở tệp để đọc
                        'filename' => basename($path) // Tên tệp
                    ];
                }

                $promises[] = $client->requestAsync($request->method, $request->url, [
                    'query' => $request->queries,
                    'multipart' => $multipart,
                    'headers' => [
                        'Authorization' => $token
                    ],
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
