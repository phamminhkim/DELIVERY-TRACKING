<?php

namespace App\Services;

use App\Models\Master\Employee;
use App\Models\SocialAccount;
use App\User;
use Exception;
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

    public function createOrGetUserFromZalo($data)
    {
        
        
        if ( $data) {
            $providerId = $data['id']; 
            $avatar = "img\avata-default.png";
            
            if (isset($data['picture'])) {
                $avatar =  $data['picture']['data']['url']; 
                $name = $data['name']; 
            }
            
            $providerName = 'ZaloProvider';
          
           
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
        return null;
    }

    public function createOrGetUserFromOnetl($data)
    {
       try {
        $avatar =  $data['avatar'] ?? ''; 
        $name = $data['name']; 
        $username = $data['username']; 
        $phone_number = $data['phone_number'] ?? ''; 
        $email = $data['email'] ?? ''; 
        $company_id = $data['company_id'] ?? ''; 
        $address = $data['address'] ?? ''; 
        
        $user = User::where('username',$username )->first();
        
        if ($user) {
            return $user;
        } else {
            //validate trùng email
            $user = User::where('email',$email )->first(); 
            if($user){
                throw new Exception ('Không thể tạo tài khoản. Email đã tồn tại trên hệ thống');
            }
           
            $user = User::create([
                'name' => $name,
                'avatar' => $avatar,
                'email' => $email,
                'username' => $username,
                'phone_number' => $phone_number
            ]);

            Employee::create([
                'user_id' =>  $user->id,
                'company_code' => $company_id,
                'code' => $username,
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone_number,
                'address' => $address,
            ]);
            return $user;
        }
        
       } catch (\Throwable $th) {
           
           throw $th;
       }
        
    }
    
}
