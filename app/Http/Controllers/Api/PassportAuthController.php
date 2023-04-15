<?php

namespace App\Http\Controllers\Api;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
 
class PassportAuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',

        ]);
   
        if($validator->fails()){
            
            $response = [
                'success' => false,
                'message' => 'Validation Error.',
            ];
    
            $code = '404';
            
            $response['data'] = $validator->errors();
            
            return response()->json($response, $code);

        }
        else{

           
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
      
            $token = $user->createToken('Laravel8PassportAuth')->accessToken;
      
            return response()->json(['token' => $token], 200);

        }
  
       
    }
  
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
  
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
 
    public function userInfo() 
    {
 
     $user = auth()->user();
      
     return response()->json(['user' => $user], 200);
 
    }
}