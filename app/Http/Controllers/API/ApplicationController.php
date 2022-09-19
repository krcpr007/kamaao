<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\application_remarks;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Storage;
use File;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * GYAANESH WORK STARTS
     */
        /**     CREATE A NEW JOB    **/   //GYAANESH
        public function store(Request $request)
        {
            // First Make Sure This is Unique
            $hasUserApplied = Application::where('user_id',  $request->user_id)->where('job_id',  $request->job_id)->count();
            if($hasUserApplied)
            {
                return response()->json([
                    'status'=>400,
                    'message'=>'Already Applied For This Job'
                ],400);
            }

            /** TIME TO VALIDATE PARAMS */
            $validater = Validator::make($request->all(),[
                'job_id'=>'required|max:19',
                'expiry_date'=>'required|date',
                'user_id'=>'required|max:191',
                'company_id'=>'required|integer'
            ]);


            if($validater->fails())
            {
                return response()->json([
                    'status'=>422,
                    'errors'=>$validater->errors()
                ],422);
            }
            else
            {
                $application = new Application();
                $application->user_id       =   $request->user_id;
                $application->job_id        =   $request->job_id;
                $application->expiry_date   =   $request->expiry_date;
                $application->save();
                $request->request->add(['status' => 'default']);
                $request->request->add(['application_id' =>$application->id]);
                $request->request->add(['company_id' =>$request->company_id]);
                
                $create_defalut_refferance  =   $this->do_reffer($request);
                return response()->json([
                    'status'=>200,
                    'message'=>'Yor Application For This Job Submitted successfully'
            ]);
            }

        }
        
        public function get_application_details($id)
        {
            
            /** 
             * 
             * NEEDS TO CHECK IF USER HAS PERMISSION TO CHECK THIS (PENDING)
             * Restrict un-assignes Employe 
             * permitted (super admin, subadmin, user that is belong to this application)
             * 
             * 
            */

            $user_application   =   DB::table('job_application')
                ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
                ->join('jobs', 'job_application.job_id', '=', 'jobs.id')         
                ->join('job_app_reffered_to', 'job_application.id', '=', 'job_app_reffered_to.application_id')         
                ->select(
                    'job_application.id as application_id','job_application.*', 
                    'user_registration.name','user_registration.gender','user_registration.mobile',
                    'jobs.*', 'jobs.id as job_id',
                    'job_app_reffered_to.id as refferal_id', 'job_app_reffered_to.status as refferal_status','job_app_reffered_to.current_stage as refferal_current_stage',
                    )
                ->where('job_application.id', $id)->get();

                /**     INCLUDE ALL REMARKS ADDED TO THIS APPLICATION */
                $remarks        =  DB::table('application_remarks')
                ->join('users', 'application_remarks.remarked_by', '=', 'users.id')
                ->select(
                    'application_remarks.*', 
                    'users.name as remarked_by',
                    )
                ->where('application_id', $id)->get();
                $reffered_to    =  DB::table('job_app_reffered_to')->where('application_id', $id)->get();

                /**         CREATE A RESPONSE        */

                return response()->json([
                'status'=>200,
                'app'=>$user_application,
                'remarks'=>$remarks
                ]);
        }

        public function do_reffer(Request $request)
        {
             
            $start_time	=date('d-m-Y h:i:s a');
            $end_time=date('Y-m-d', strtotime($start_time. ' +30 days'));// to be changed as per job preset 
            // $chkapp_id = DB::table('application_expired')->where(['application_id'=>$app_id])->get()->count();
            $is_reffered = DB::table('job_app_reffered_to')
            ->where(['application_id'   =>  $request->application_id])
            //->where(['job_id'           =>  $request->job_id])/** if can be refered multiple time to same job */
            ->get()->count();
            if($is_reffered>0){
                return response()->json([
                    'status'=>304,
                    'message'=>' Already referred'
                    ]);
            }else
            {
                $query=DB::table('job_app_reffered_to')
                ->insert(
                    [
                        'application_id' =>$request->application_id,
                        'user_id' =>$request->user_id,
                        'status' =>$request->status,
                        'company_id'=>$request->company_id,
                        'job_id'=> $request->job_id
                    ]);

                    if($query)
                    {
                        /**         Set THE APPLICATION AS REFFERED Count  */
                       
                        DB::table('job_application')->where('id',$request->application_id)
                        ->increment('reffered_count', 1);
                        
                        return response()->json([
                            'status'=>200,
                            'message'=>'Application Referred'
                            ]);
                    }else
                    {
                        return response()->json([
                            'status'=>404,
                            'message'=>'Something Went Wrong'
                            ]);
                    }
            }
        }

        public function application_view(Request $request)
        {

            //temporary fixed as not getting from front end
            if ($request->input('type')) 
            {
                $status   =   $request->input('type');
            }
            else
            {
                $status   =   0;
            }
            // 0 pending , 1 active , 2 closed(failed) , 3 closed (success)

            // temporary Disabled

            // $validater  = Validator::make($request->all(),[
            //     'type'=>'required',
            // ]);
            // if($validater->fails())
            // {
            //     return response()->json([
            //         'status'=>422,
            //         'errors'=>$validater->errors(),
            //         'param'=> $request->all()
            //     ],422);
            // }
            // dd();
            if(auth('sanctum')->user()->user_type == '1' || auth('sanctum')->user()->user_type == '2')//super admin and sub admin
            {
                $application    =   DB::table('job_application')
                ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
                ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
                ->join('companies', 'jobs.company_id', '=', 'companies.id')
                ->select(
                    'job_application.*', 
                    'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile',
                    'jobs.state','jobs.job_title','jobs.job_type','jobs.city','jobs.area','companies.company_legal_name')
                    ->where('job_application.status','=',$status)
                ->orderby('id','desc')->get();
                
            }
            else
            {
                $application=DB::table('job_application')
                ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
                ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
                ->select('job_application.*', 'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name')
                ->where('job_application.assign', auth('sanctum')->user()->id)
                ->where('job_application.status','=',$status)
                ->orderby('id','desc')->get();
                
            }
            if(count($application))
            {
                return ([
                    'status'=>200,
                    'application'=>$application,
                    // 'role'  => auth('sanctum')->user()
                ]);
            }
            else
            {
                return ([
                    'status'=>404,
                    'message'=>"no record found",
                    // 'role'  => auth('sanctum')->user()
                ]);
            }
            
        }

        public function application_remark(Request $request,$id)
        {
            $remarked_by    =   auth('sanctum')->user()->id;
            $validater  = Validator::make($request->all(),[
                'remark'=>'required',
                'remarked_by'=>'required',
            ]);
            if($validater->fails())
            {
                return response()->json([
                    'status'=>422,
                    'errors'=>$validater->errors()
                ],422);
            }
            else
            {
                $remark['remarks'] =  $request->remark;
                $remark['application_id'] = $id;
                $remark['remarked_by'] = $remarked_by;
                $remarkAdded = application_remarks::create($remark);
                if($remarkAdded)
                {
                    return response()->json([
                        'status'=>200,
                        'message'=>'Remark added on Application  '
                        ],200);
                }
                else
                {
                    return response()->json([
                        'status'=>404,
                        'message'=>' Something went Wrong  '
                        ],404);
                }
            }
        }

        public function update_status(Request $request,$id)
        {
            /**
             * Status Preset
             * 0    => pending => default
             * 1    => Active
             * 2    => Closed
             */ 
            $status=$request->status;
            switch ($status) {
                case 'pending':
                    return response()->json([
                        'status'=>422,
                        'message'   => 'Unprocessable Entity '
                        ], 422);
                  break;
                case 'Active':
                  $status = 1;
                  break;
                default:
                return response()->json([
                    'status'    =>  422,
                    'message'   => 'Unprocessable Entity '
                    ], 422);
              }
            
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
        
        public function update_application_stage(Request $request)
        {  
            $validater  = Validator::make($request->all(),[
                'current_stage' =>  'required|integer',
                'new_stage'     =>   'required|integer',
                'application_id'=>   'required|integer',
            ]);
            $update_stage=DB::table('job_application')->where('id',$request->application_id)
                            ->update(['current_stage'=>$request->new_stage]);
                            
            if($update_stage){
                return response()->json([
                    'status'=>200,
                    'message'=>' Application Updated'
                    ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Something went Wrong'
                    ]);
            }

        }
        
    /**
     * ENDS GYAANESH WORK
     */
    public function application_view_refferd()
    {
        if(auth('sanctum')->user()->user_type == '1' || auth('sanctum')->user()->user_type == '2')//super admin and sub admin
        {
            // $application=DB::table('job_application')
            // ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            // ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            // ->select(
            //     'job_application.*', 
            //     'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile',
            //     'jobs.state','jobs.job_title','jobs.job_type','jobs.city','jobs.area','jobs.company_legal_name')
            // ->where('job_application.Is_reffered', 1)
            // ->orderby('id','desc')->get();
            $application=DB::table('job_application')
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')

            // ->join('application_expired', 'job_application.id', '=', 'application_expired.application_id')
            ->select('job_application.*', 
                    'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile',
                    'jobs.state','jobs.city','jobs.area',
                    'companies.company_legal_name',
                    // 'application_expired.start_time','application_expired.end_time'
                    )
            ->orderby('id','desc')->get();
        }
        else
        {
            $application=DB::table('job_application')
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 
                    'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile',
                    'jobs.state','jobs.city','jobs.area','jobs.company_legal_name')
            ->where('job_application.assign', auth('sanctum')->user()->id)
            ->where('job_application.Is_reffered', 1)
            ->orderby('id','desc')->get();
            $myItem = '';
        }            
        return ([
            'status'=>200,
            'application'=>$application,
            'role'  => auth('sanctum')->user()
        ]);
        
    }


    public function application_view_refferd_OLD()
    {
        $application=DB::table('job_application')
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->join('application_expired', 'job_application.id', '=', 'application_expired.application_id')
            ->select('job_application.*', 'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name','application_expired.start_time','application_expired.end_time')
            ->orderby('id','desc')->get();

        return ([
            'status'=>200,
            'application'=>$application
        ]);
    }

    
            /** application_view OLD  */
    public function application_view_OLD(Request $request)
    {
        if(auth('sanctum')->user()->user_type == '1')//super admin and sub admin
        {
            $application=DB::table('job_application')
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name')->whereNotExists(function ($query) {
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
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 'user_registration.name','user_registration.gender','user_registration.profile_pic','user_registration.mobile','jobs.state','jobs.city','jobs.area','jobs.company_legal_name')->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('application_expired')
                      ->whereColumn('application_expired.application_id', 'job_application.id');
            })
            ->where('job_application.assign', auth('sanctum')->user()->id)
            ->orderby('id','desc')->get();
            $myItem = '';
        }            
        return ([
            'status'=>200,
            'application'=>$application,
            'role'  => auth('sanctum')->user()
        ]);
    }
    
    public function Application_assign(Request $request)
    {
        $app_id=$request->application_id;
        $assign_emp=$request->assign;

        for($i=0;   $i<count($app_id);  $i++)
        {
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

    

    public function Application_user_old($id)
    {
        $chkapp_id = DB::table('application_expired')->where(['application_id'=>$id])->get()->count();
        if($chkapp_id>0)
        {
            $app_view=DB::table('job_application')
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->join('application_expired', 'job_application.id', '=', 'application_expired.application_id')
            ->select('job_application.*', 'user_registration.name','user_registration.gender','user_registration.mobile','jobs.state','jobs.job_category','jobs.city','jobs.area','jobs.company_legal_name','application_expired.status')
            ->where('job_application.id', $id)
            ->orderby('id','desc')->get();
        }else{
            $app_view=DB::table('job_application')
            ->join('user_registration', 'job_application.user_id', '=', 'user_registration.id')
            ->join('jobs', 'job_application.job_id', '=', 'jobs.id')
            ->select('job_application.*', 'user_registration.name','user_registration.gender','user_registration.mobile','jobs.state','jobs.job_category','jobs.city','jobs.area','jobs.company_legal_name')
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
    /***
     * Gyaanesh Work Started Here 
     */
        public function application_status(Request $request,$id)
        {
            
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

        public function create_follow_up(Request $request)
        {
             /** TIME TO VALIDATE PARAMS */
             $validater = Validator::make($request->all(),[
                'application_id'    =>  'required|integer',
                'comment'           =>  'required',
                'follow_up_date'   =>  'required|date_format:Y-m-d',
                'follow_up_time'    =>  'required|date_format:H:i',
                'created_by'           =>  'required'
            ]);

            if($validater->fails())
            {
                return response()->json([
                    'status'=>422,
                    'errors'=>$validater->errors()
                ],422);
            }
            else
            { 
            
                $query=DB::table('application_followups')->insert($request->all());
                if($query){
                    return response()->json([
                        'status'=>200,
                        'message'=>'Follow Up added For Current Application  '
                        ]);
                }else{
                    return response()->json([
                        'status'=>404,
                        'message'=>'Something Went Wrong'
                        ]);
                }
            }
        }

        public function close_follow_up(Request $request)
        {
            /** TIME TO VALIDATE PARAMS */
            $validater = Validator::make($request->all(),[
                'id'        =>  'required|integer',
                'user_id'   =>  'required'
            ]);

            if($validater->fails())
            {
                return response()->json([
                    'status'=>422,
                    'errors'=>$validater->errors()
                ],422);
            }
            else
            {
                $do_job    =   DB::table('application_followups')->where('id',$request->id)
                ->update(['has_followed_up'=>1, 'followed_by'=>$request->user_id]);
                
                if($do_job){
                    return response()->json([
                        'status'=>200,
                        'message'=>' Application  status changed'
                        ]);
                }else{
                    return response()->json([
                        'status'=>404,
                        'message'=>'Something Went Wrong'
                        ]);
                } 
            }
        }

        public function get_my_follow_up(Request $request)
        {
            $follow_ups =DB::table('application_followups')
            ->where(['created_by'=>$request->created_by]);

            if($request->type =='current')
            {
                $follow_ups->where('follow_up_date', date('Y-m-d'));

            }elseif ($request->type =='upcoming') 
            {
                $follow_ups->where('follow_up_date','>', date('Y-m-d'));
                
            }
            else
            {
                $follow_ups->where('follow_up_date','<', date('Y-m-d'));
            }
            
            
            $follow_ups->orderBy('follow_up_time', 'ASC');
            $result =   $follow_ups->get();
            if(count($result)){
                return response()->json([
                    'status'=>200,
                    'message'=> $result,
                    'date'=>date('Y-m-d')
                    ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>"no follow up found for Date ".date('Y-m-d')
                    ]);
            }
            
        }
    /***
     * Gyaanesh Work Ends Here 
     */
    
    public function application_remark_old(Request $request,$id)
    {
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

    

    public function applcation_followup_fetch($id)
    {
        $follow=DB::table('application_followup_time')->where(['app_id'=>$id])->get();

        return response()->json([
                'status'=>200,
                'follow'=>$follow
        ]);
    }
    public function referredstatus(Request $request)
    {
        
        $status =$request->status;
        $application_id=$request->application_id;
        $start_time	=date('d-m-Y h:i:s a');
        $end_time=date('Y-m-d', strtotime($start_time. ' +30 days'));// to be changed as per job preset 
        // $chkapp_id = DB::table('application_expired')->where(['application_id'=>$app_id])->get()->count();
        $is_reffered = DB::table('job_app_reffered_to')->where(['application_id'=>$app_id])->get()->count();
        if($is_reffered>0){
            return response()->json([
                'status'=>304,
                'message'=>' Already referred'
                ]);
        }else{
            $query=DB::table('job_app_reffered_to')->insert(['application_id' =>$application_id, 'status' =>$status, 'company_id'=>22]);
                if($query)
                {

                    /**         MARK THE APPLICATION AS REFFERED  */
                    DB::table('job_application')
                    ->where('id', $app_id)
                    ->update(array('Is_reffered' => 1));  // u
                    
                    return response()->json([
                        'status'=>200,
                        'message'=>'Application Referred'
                        ]);
                }else
                {
                    return response()->json([
                        'status'=>404,
                        'message'=>'Something Went Wrong'
                        ]);
                }
        }
    }

    public function applcation_expire($id)
    {
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
        }
        else
        {
            return 'Not referred';
        }
    }
        
}
