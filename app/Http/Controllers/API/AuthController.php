<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function google_login(Request $request)
    {
        $validater = Validator::make($request->all(),[
            'email'=>'email|max:191|unique:users,email',
        ]);

        if($validater->fails())
        {       
            $user = User::where('email', $request->email)->first();
 
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                        'status'=>401,
                        'message'=>'Invalid Credentials',
                ]);

            }else{
                $token = $user->createToken($user->email.'_Token')->plainTextToken;
                return response()->json([
                    'status'=>202,
                    'username'=>$user->name,
                    'token'=>$token,
                    'message'=>'Logged In Successfully'
                ]);
            }


                // $token = $user->createToken($request->email.'_Token')->plainTextToken;
                // return response()->json([
                //     'status'=>202,
                //     'username'=>$user->name,
                //     'token'=>$token,
                //     'message'=>'Logged In Successfully',
                //     'validation_errors'=>$validater->messages(),
                //     ]);
        }else{
            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ]);

            $token = $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=>200,
                'username'=>$user->name,
                'token'=>$token,
                'message'=>'Login Successfully'
            ]);
        }
    }


    public function register(Request $request)
    {
        $validater = Validator::make($request->all(),[
            'name'=>'required|max:191',
            'email'=>'required|email|max:191|unique:users,email',
            'password'=>'required|min:8'


        ]);

        if($validater->fails())
        {
                return response()->json([
                        'validation_errors'=>$validater->messages(),
                ]);
        }else{
            $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ]);

            $token = $user->createToken($user->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=>200,
                'username'=>$user->name,
                'token'=>$token,
                'message'=>'Registered Successfully'
        ]);
        }
    }

    public function login(Request $request)
    {
        $validater = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
                
            ]);

        if($validater->fails())
        {
            return response()->json([
                'validation_errors'=>$validater->messages(),
            ]);
        }
        else
        {
             
            $user = User::where('email', $request->email)->first();
            if (! $user || ! Hash::check($request->password, $user->password)) 
            {
                return response()->json([
                            'status'=>401,
                            'message'=>'Invalid Credentials',
                    ]);

            }else
            {
                $token = $user->createToken($user->email.'_Token')->plainTextToken;
                return response()->json([
                    'status'=>200,
                    'username'=>$user->name,
                    'token'=>$token,
                    'message'=>'Logged In Successfully'
                ]);
            }
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=>200,
            'message'=>'Logged Out Successfully'
    ]);
    }
}
