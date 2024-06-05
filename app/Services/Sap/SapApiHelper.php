<?php
namespace App\Services\Sap;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request  ;
use Illuminate\Support\Facades\Log;

class SapApiHelper{


    public static function fetchData( $body){
        try {
            $res= SapApiHelper::send('GET',$body);
        } catch (\Throwable $th) {
            throw $th;
        }
        $response = (string) $res->getBody();
        $json = json_decode($response);
        return $json;
    }
    public static function postData( $body){

        try {
            $re = [];
            $re['data'] = "";
            $re['error'] =  "";
            $re['success'] = true;
            $res= SapApiHelper::send('POST',$body);
            $response = (string) $res->getBody();
            $json = json_decode($response);
            $re['data'] = $json?$json:"";
            return $re;
         } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $re['success'] = false;
            $re['error'] =  $th->getMessage();

        }
        return $re;

    }
    public static function send($type,$body){

        $url = config("api_site.connections.saphana.url");// env('SAP_URL');
        $tokenData = SapApiHelper::getToken();
        $client = new Client();
            $headers = [
            'x-csrf-Token' =>  $tokenData['x-csrf-token'],
            'Content-Type' => 'application/json',
            'Cookie' => $tokenData['cookie']
            ];
        try {
            $client = new Client(    ["verify"=>false] );
            $httpRequest = new Request($type, $url,$headers, $body);
            $res = $client->sendAsync($httpRequest)->wait();
        } catch (\Throwable $th) {
            Log::error($th->getMessage()) ;
            throw $th;
        }
         return $res;
       }
       public static function getToken( ){

        try {
            $username = config("api_site.connections.saphana.username");// env('SAP_USER');
            $password = config("api_site.connections.saphana.password");// env('SAP_PASS');
            $url      = config("api_site.connections.saphana.url");// env('SAP_URL');
            $headers = [
                'Authorization' => 'Basic '.base64_encode($username.':'.$password),
                'Content-Type' => 'application/json',
                'x-csrf-token' => 'fetch',
                'Set-Cookie' => 'fetch',
                ];

            $body = [

                ];

            $client = new Client(
                ["verify"=>false]
            );
            $httpRequest = new Request('GET', $url, $headers,json_encode($body) );
            $res = $client->sendAsync($httpRequest)->wait();

        } catch (\Throwable $th) {
            Log::error($th->getMessage()) ;
            throw $th;
        }
        $cookie = $res->getHeader('set-cookie')[1];
        $token = $res->getHeader('x-csrf-token')[0];
        $data = [
            'x-csrf-token' => $token,
            'cookie' => $cookie,
        ];
         return  $data;
       }
       public static function TestConnectByUrl($url,$timeout = 2.0,$showError=false){
        try {

            $client = new Client([
                'timeout'  => $timeout,
                "verify"=>false
            ]);
            $response = $client->get($url);

        } catch (\Throwable $th) {
            if ($th instanceof  ConnectException) {
                $errors = $th->getHandlerContext();
               return $errors['error'];
            }elseif ($th instanceof  ClientException ) {
                $res = $th->getResponse() ;
                $err = $res->getStatusCode() . ":" . $res->getReasonPhrase();
                return $err;
            }
            Log::error($th->getMessage()) ;
            if($showError){
                return $th->getMessage();
            }else{
                return "Connectin error";
            }
        }
         return  "Kết nối thành công";
       }
}
