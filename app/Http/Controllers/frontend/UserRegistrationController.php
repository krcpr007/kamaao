<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;

use App\Models\frontend\User_registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use File;


class UserRegistrationController extends Controller
{
   
    public function index()
    {
        //
    }

    
    public function create()
    {
        //
    }

    
    public function phone_number(Request $request)
    {
        $otp=rand(33333,99999);
        $user_registration = User_registration::where('phone', $request->phone)->first();              
        
        if( $user_registration)
        {
            $update= DB::table('user_registrations')
            ->where('phone', $request->phone)
            ->update(['otp'  => $otp]);
            return response()->json([
                'status'=>200,
                'username'=>$user_registration->name,
                'user_id'=>$user_registration->id,
                'message'=>'Otp Send Successfully',
                'msg'=>'Your One Time Password Is'.' '.$otp
            ]);
        }               
        else
        {
            $user_registration= User_registration::create([
            'phone'=>$request->phone,
            'otp'=>$otp,
            ]);

            return response()->json([
                'status'=>200,
                'username'=>$user_registration->name,
                'user_id'=>$user_registration->id,
                'message'=>'Otp Send Successfully',
                'msg'=>'Your One Time Password Is'.' '.$otp
            ]);
        }
    }


    public function verify_number(Request $request){
        $verify_number=User_registration::where(['phone'=>$request->phone,'otp'=>$request->otp])->first();
        if($verify_number){
            return response()->json([
                'status'=>200,
                'username'=>$verify_number->name,
                'user_id'=>$verify_number->id,
                'msg'=>$verify_number
                
    
        ]);  
        }else{
            return response()->json([
                'status'=>404,
                'msg'=>'Your Otp and Phone Number Does Not Match'
            ]); 
        }
    }
   
    public function show(User_registration $user_registration)
    {
        //
    }

   
    public function edit(User_registration $user_registration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\frontend\User_registration  $user_registration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User_registration $user_registration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\frontend\User_registration  $user_registration
     * @return \Illuminate\Http\Response
     */
    public function destroy(User_registration $user_registration)
    {
        //
    }
}
