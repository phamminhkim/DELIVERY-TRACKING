<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Zalo\Zalo;
use Zalo\ZaloEndPoint;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
       
    
        return Socialite::driver($provider)->redirect();
        
    }

    public function handleProviderCallback(SocialAccountService $service, $provider)
    {
        $user = null;
        if($provider == 'zalo'){

            $codeVerifier = session()->get('codeVerifier');
        //  dd( $codeVerifier );
          $config = array(
              'app_id' => '270349389225145785',
              'app_secret' => 'PtCJyx1eNY4Pr6W467Xu'
          );
          $zalo = new Zalo($config);
          $helper = $zalo -> getRedirectLoginHelper();
          $zaloToken = $helper->getZaloToken($codeVerifier); // get zalo token
     
          $accessToken = $zaloToken->getAccessToken();
  
          
    
          $params = ['fields' => 'id,name,picture'];
          $response = $zalo->get(ZaloEndPoint::API_GRAPH_ME, $accessToken, $params);
          $result = $response->getDecodedBody(); // result
          //Tạo lấy user hoặc tạo user từ zalo
          //dd($result);
        }else{
            $user = $service->createOrGetUser(Socialite::driver($provider)); 
        }
      
        Auth::login($user);

        return redirect()->to('/');
    }
}
