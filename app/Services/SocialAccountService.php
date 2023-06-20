<?php

namespace App\Services;

 
use App\Models\SocialAccount;
use App\User;
use Laravel\Socialite\Contracts\Provider;
class SocialAccountService
{
    public function createOrGetUser(Provider $provider)
    {
       
        $providerUser = $provider->user(); 
      
        $providerName = class_basename($provider);
       
        $account = SocialAccount::whereProvider($providerName)
            ->whereProviderUserId($providerUser->getId())
            ->first();
          
        if ($account) {
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $providerName
            ]);
            
            $user = User::whereEmail($providerUser->getEmail())->first();
          
            if (!$user) {
                $avatar = $providerUser->getAvatar();
                
                if(!$avatar){
                  
                }
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'avatar' =>$avatar
                ]);
                
            }
           
            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }

    public function getOrCreateUserFromZalo($data)
    {
       
        $providerId = $data['id']; 
        $avatar =  $data['picture']['data']['url']; 
        $providerName = 'ZaloProvider';
        $name = $data['name']; 
        $account = SocialAccount::whereProvider($providerName)
            ->whereProviderUserId($providerId)
            ->first();
        
        if ($account) {
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $providerId,
                'provider' => $providerName
            ]);
            
            $user = User::create([
                'name' => $name,
                'avatar' =>$avatar
            ]);
           
            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }
    
}
