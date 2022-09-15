<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function google_login(Request $request){
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
                if($user->user_type== 1){//1 =admin
                    $token = $user->createToken($user->email.'_AdminToken', ['server:admin'])->plainTextToken;
                }else{

                    $token = $user->createToken($user->email.'_Token',[''])->plainTextToken;
                }
        return response()->json([
            'status'=>202,
            'username'=>$user->name,
            'token'=>$token,
            'message'=>'Logged In Successfully'
    ]);
            }


          
        }else{
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            
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
                        'validation_errors'=>$validater->errors(),
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
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
                $user = Auth::user(); 
                $token = $user->createToken($user->email.'_Token')->plainTextToken;
                return response()->json([
                    'status'=>200,
                    'username'=>$user->name,
                    'user_email'=>$user->email.'_Token',
                    'token'=>$token,
                    'message'=>'Logged In Successfully'
                ]);
            } 
            else{ 
                return response()->json([
                                    'status'=>401,
                                    'message'=>'Invalid Credentials',
                        ]);
            } 

            // $user = User::where('email', $request->email)->first();
            // if (! $user || ! Hash::check($request->password, $user->password)) 
            // {
            //     return response()->json([
            //                 'status'=>401,
            //                 'message'=>'Invalid Credentials',
            //     ]);
            // }
            // else
            // {
            //     $token = $user->createToken($user->email.'_Token')->plainTextToken;
            //     return response()->json([
            //         'status'=>200,
            //         'username'=>$user->name,
            //         'user_email'=>$user->email.'_Token',
            //         'token'=>$token,
            //         'message'=>'Logged In Successfully'
            //     ]);
            // }
        }
    }

    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();
        // $request->user()->currentAccessToken()->delete();
        // $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status'=>200,
            'message'=>'Logged Out Successfully'
    ]);
    }

    public function add_employee(Request $request)
    {
        $employee = new User;
        $employee->name=$request->input('employee_name');
        $employee->email=$request->input('employee_email');
        $employee->password=Hash::make($request->input('employee_pass'));
        $employee->mobile=$request->input('mobile');
        $employee->enc_pass=$request->input('employee_pass');
        $employee->team_leader=$request->input('team_leader');
        $employee->user_type=$request->input('employee_designation');
        $employee->status='enable';
        if($request->hasfile('profile_pic'))
        {
            $image=$request->file('profile_pic');
            $ext=$image->extension();
            $image_name=time().'.'.$ext;
            $image->move(public_path('Profile_pic'),$image_name);
            $employee->profile_pic=$image_name;
        }
        $employee->save();
            // $token = $employee->createToken($employee->email.'_Token')->plainTextToken;
            return response()->json([
                'status'=>200,
                'message'=>'Employee Details Inserted Successfully'
        ]);
    }

    public function view_employee()
    {
        # code...
        $employee=DB::table('users')->where('user_type','!=',1)->get();

        return response()->json([
                'status'=>200,
                'employee'=>$employee
        ]);
    }

    public function get_team(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'user_id'=>'required|integer',
        ]);
        if($validated->fails())
        {
                return response()->json([
                    'status'=>422,
                    'validation_errors'=>$validated->errors(),
                ],422);
        }
        else
        {
            $team   =   DB::table('users')->where('team_leader','=',$request->user_id)
            ->select('id', 'name', 'email', 'user_type', 'team_leader')->get();
            if(count($team))
            {
                return response()->json([
                        'status'=>200,
                        'employee'=>$team
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>204,
                    'message'=> "No Record found"
            ],200);
            }
        }
    }
    public function edit($id){
        $employee = User::find($id);
        if($employee){
        return response()->json([
            'status'=>200,
            'employee'=>$employee
            ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Employee Id Not Found'
                    ]);
                
            }
    }
    public function update_employee(Request $request, $id){
        $employee=User::find($id);
        if($employee){
            $employee->name=$request->input('employee_name');
            $employee->email=$request->input('employee_email');
            $employee->password=Hash::make($request->input('employee_pass'));
            $employee ->enc_pass=$request->input('employee_pass');
            $employee->mobile=$request->input('mobile');

            $employee->user_type=$request->input('employee_designation');
            $employee->status='enable';
            if($request->hasfile('profile_pic')){
                $Image=DB::table('users')->where(['id'=>$id])->get();
                $file=public_path('Profile_pic/'.$Image[0]->profile_pic);
                if(File::exists($file)){
                    File::delete($file);
                }
                $image=$request->file('profile_pic');
                $ext=$image->extension();
                $image_name=time().'.'.$ext;
                $image->move(public_path('Profile_pic'),$image_name);
                $employee->profile_pic=$image_name;
            }
            $employee->save();
            return response()->json([
                'status'=>200,
                'message'=>'Employee Update Successfully'
            ]);
        }
    }
    public function enable_employee(Request $request, $id)
    {
        # code...
        $employee=User::find($id);
        if($employee){
            $employee->status='enable';
            $employee->save();
            return response()->json([
                'status'=>200,
                'message'=>'Employee status enable'
            ]);
        }
       
        

    }

    public function disable_employee(Request $request, $id)
    {
        # code...
        
        $employee=User::find($id);
        if($employee){
            $employee->status='disable';
            $employee->save();
            return response()->json([
                'status'=>200,
                'message'=>'Employee status disable'
            ]);
        }
       
        
        
       

    }

    public function employe_delete(Request $request, $id)
    {
        # code...
        $employee = User::find($id);
        
        
        $employee->delete();
        return response()->json([
            'status'=>204,
            'message'=>'Employee Details  Deleted'
        ]);
        

    }
    public function check_user($id){
        $check_user=DB::table('personal_access_tokens')->where('name',$id)->get();

        return response()->json([
                'status'=>200,
                'check_user'=>$check_user
        ]);
    }
}
