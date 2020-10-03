<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\SignUpNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthenticationController extends Controller
{
    public function register (Request $request, User $user){
    $validator =  Validator::make($request->all(),[
        'email' => 'bail|email|required|unique:users',
        'password' => 'bail|required|alpha_num',
        'firstName' => 'bail|required',
    ]);
    try {
        if ($validator->fails()){
            $data = [
                'success' => false,
                'error' =>"Kindly ensure all fields are well filled",
            ];
            return response($data,422);
        }

        //register User
        $user->createUser($request);

        //authenticate registered user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            Auth::user()->notify(new SignUpNotification($request->firstName));
            $access_token = Auth::user()->createToken('userToken')->accessToken;
            $data = [
                'user' => Auth::user(),
                'token' => $access_token,
                'success' => true,
                'message' => "Sign and Login Success"
            ];
            return response($data,200);
        }

        // handle unauthenticated user
        $data = [
            'success' => false,
            'error' => 'Invalid Login Details'
        ];
        return response($data,401);
    }
    catch (\Exception $exception){
        $data = [
            'success' => false,
            'error' => 'Action Could not be Performed'
        ];
        return response($data, 500);
    }
}

    public function userLogin(Request $request){
        $validator =  Validator::make($request->all(),[
            'email' => 'bail|required',
            'password' => 'bail|required|min:6|alpha_num',
        ]);
        try {
            if ($validator->fails()){
                $data = [
                    'success' => false,
                    'errors' =>$validator->errors(),
                ];
                return response($data,422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $access_token = Auth::user()->createToken('userToken')->accessToken;
                $data = [
                    'user' => Auth::user(),
                    'token' => $access_token,
                    'success' => true
                ];
                return response($data,200);
            }

            // handle unauthenticated user
            $data = [
                'success' => false,
                'error' => 'Invalid Login Details'
            ];
            return response($data,401);
        }
        catch (\Exception $exception){
            $data = [
                'success' => false,
                'error' => 'Action Could not be Performed'
            ];
            return response($data, 500);
        }
    }

    public function logout(){
        try{
            Auth::user()->token()->revoke();
            $data = [
                'success' => true,
                'message' => "Logout Successfull"
            ];
            return response($data, 200);
        }
        catch (\Exception $exception){
            $data = [
                'success' => false,
                'error' => 'Action Could not be Performed'
            ];
            return response($data, 500);
        }
    }

    public function login(){
        $data = [
            'success' => false,
            'error' => 'Unauthorized Access'
        ];
        return response($data, 200);
    }
}
