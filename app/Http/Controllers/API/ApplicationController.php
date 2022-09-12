<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Storage;
use File;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function application_view_refferd(){
        $application=DB::table('job_application')
            ->join('user_regisrtration', 'job_application.user_id', '=', 'user_regisrtration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->join('application_expired', 'job_application.id', '=', 'application_expired.application_id')
            ->select('job_application.*', 'user_regisrtration.name','user_regisrtration.gender','user_regisrtration.profile_pic','user_regisrtration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name','application_expired.start_time','application_expired.end_time')
            ->orderby('id','desc')->get();

        return ([
            'status'=>200,
            'application'=>$application
        ]);
    }
    public function application_view(Request $request){
        
        if(auth('sanctum')->user()->user_type == '1')
        {
            $application=DB::table('job_application')
            ->join('user_regisrtration', 'job_application.user_id', '=', 'user_regisrtration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 'user_regisrtration.name','user_regisrtration.gender','user_regisrtration.profile_pic','user_regisrtration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name')->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('application_expired')
                      ->whereColumn('application_expired.application_id', 'job_application.id');
            })
            ->orderby('id','desc')->get();
            $myItem = '';
        }
        else
        {
            $application=DB::table('job_application')
            ->join('user_regisrtration', 'job_application.user_id', '=', 'user_regisrtration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 'user_regisrtration.name','user_regisrtration.gender','user_regisrtration.profile_pic','user_regisrtration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name')->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('application_expired')
                      ->whereColumn('application_expired.application_id', 'job_application.id');
            })
            ->where('job_application.assign', auth('sanctum')->user()->id)
            ->orderby('id','desc')->get();
            $myItem = '';
        }

    

            
            // dd(auth('sanctum')->user()->user_type);
            return ([
                'status'=>200,
                'application'=>$application,
                'role'  => auth('sanctum')->user()
            ]);
        }
    

    public function Application_assign(Request $request){
        $app_id=$request->application_id;
         $assign_emp=$request->assign;
        for($i=0;$i<count($app_id);$i++){
           $update= DB::table('job_application')
            ->where('id', $app_id[$i])
            ->update(['assign' =>$assign_emp]);
            if($update){
                return response()->json([
                    'status'=>200,
                    'message'=>'Application Assign to Employee '
                ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>'error'
                ]); 
            }
        }
        
           
            
       

    }

    public function Application_user($id){
        $chkapp_id = DB::table('application_expired')->where(['application_id'=>$id])->get()->count();
        if($chkapp_id>0){
        $app_view=DB::table('job_application')
        ->join('user_regisrtration', 'job_application.user_id', '=', 'user_regisrtration.id')
        ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
        ->join('application_expired', 'job_application.id', '=', 'application_expired.application_id')
        ->select('job_application.*', 'user_regisrtration.name','user_regisrtration.gender','user_regisrtration.mobile','jobs.state','jobs.job_category','jobs.city','jobs.area','jobs.company_legal_name','application_expired.status')
        ->where('job_application.id', $id)
        ->orderby('id','desc')->get();
        }else{
            $app_view=DB::table('job_application')
            ->join('user_regisrtration', 'job_application.user_id', '=', 'user_regisrtration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 'user_regisrtration.name','user_regisrtration.gender','user_regisrtration.mobile','jobs.state','jobs.job_category','jobs.city','jobs.area','jobs.company_legal_name')
            ->where('job_application.id', $id)
            ->orderby('id','desc')->get();
        }
    
        if($app_view){
        return response()->json([
            'status'=>200,
            'app'=>$app_view
            ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Application  Id Not Found'
                    ]);
                
            }
    }

    public function application_status(Request $request,$id){
        $status=$request->input('status');
        $application=DB::table('job_application')->where('id',$id)
                        ->update(['status'=>$status]);
        if($application){
            return response()->json([
                'status'=>200,
                'message'=>' Application  status changed'
                ]);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>' Application  status not changed'
                ]);
        }
    }

        public function application_remark(Request $request,$id){
            $remark=$request->input('remark');
            $application=DB::table('job_application')->where('id',$id)
                            ->update(['remark'=>$remark]);
            if($application){
                return response()->json([
                    'status'=>200,
                    'message'=>'Remark added on Application  '
                    ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Remark not added on Application  '
                    ]);
            }
            }

        public function applcation_followup(Request $request){
            $comment=$request->input('comment');
            $app_id=$request->input('app_id');
            $comment_at	=date('d M,Y');
            $comment_time=date('h:i:s a');
            $query=DB::table('application_followup_time')->insert(['app_id' =>$app_id, 'comment' =>$comment ,'comment_at'=>$comment_at,'comment_time'=>$comment_time]);
            if($query){
                return response()->json([
                    'status'=>200,
                    'message'=>'Comment added on Application  '
                    ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Comment not added on Application  '
                    ]);
            }
        }

        public function applcation_followup_fetch($id){
            $follow=DB::table('application_followup_time')->where(['app_id'=>$id])->get();

            return response()->json([
                    'status'=>200,
                    'follow'=>$follow
            ]);
        }

        public function referredstatus(Request $request){
            $status=$request->input('status');
            $app_id=$request->input('app_id');
            $start_time	=date('d-m-Y h:i:s a');
            $end_time=date('Y-m-d', strtotime($start_time. ' +30 days'));
            $chkapp_id = DB::table('application_expired')->where(['application_id'=>$app_id])->get()->count();
            if($chkapp_id>0){
                return response()->json([
                    'status'=>404,
                    'message'=>' Already referred  '
                    ]);
            }else{
                $query=DB::table('application_expired')->insert(['application_id' =>$app_id, 'status' =>$status,'start_time'=>$start_time ,'end_time'=>$end_time]);
                    if($query){
                        return response()->json([
                            'status'=>200,
                            'message'=>'You are referred'
                            ]);
                    }else{
                        return response()->json([
                            'status'=>404,
                            'message'=>'You are not referred '
                            ]);
                    }
            }
        }

        public function applcation_expire($id){
            $chkapp_id = DB::table('application_expired')->where(['application_id'=>$id])->get()->count();
            
            $follow=DB::table('application_expired')->where(['application_id'=>$id])->select('start_time', 'end_time') ->get();
            if($chkapp_id>0){
            $start_time=$follow[0]->start_time;
             $end_time=$follow[0]->end_time;
             $current_date_time=date('d-m-Y h:i:s a');
             $datetime1 =date_create($current_date_time) ;
$datetime2 = date_create($end_time);
  
// calculates the difference between DateTime objects
$interval = date_diff($datetime1, $datetime2);
  
// printing result in days format
            return $interval->format('%R%a days');
            }else{
                return 'Not referred';
            }
        }
        
}
