<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\post_job;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exports\JobsExport;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use File;

class JobsController extends Controller
{
    public function index( Request $request, $id=null)
    {
        if($id)//getting single job by id
        {
            $job = Job::find($id);
        }
        else
        {
            // get all job
            $job=DB::table('jobs')
            ->join('companies', 'companies.id', '=', 'jobs.company_id')
            ->select('jobs.*', 'companies.company_logo')
            ->where('is_expired','0')->orderby('id','desc')->get();
        }
        
        return response()->json([
            'status'=>200,
            'job'=>$job
        ]);
    }

    public function edit($id)
    {
        $job = Job::find($id);
        if($job)
        {
            return response()->json([
            'status'=>200,
            'job'=>$job
            ]);
        }else
        {
            return response()->json([
                'status'=>404,
                'message'=>' Job Id Not Found'
                ]);
                
        }
    }
            /**
             * GYAANESH WORK STARTS
             */
    /**
     * POST A NEW JOB
     */
    public function store(post_job $request)
    {
    
        $Job = new Job;
        $Job->call_action=$request->input('call_action');
        $Job->company_id=$request->input('company_id');
        $Job->call_action1=$request->input('call_action1');
        $Job->job_title=$request->input('job_title');
        $Job->sub_title=$request->input('sub_title');
        $Job->job_type=$request->input('job_type');
        $Job->job_category=$request->input('job_category');
        $Job->expiry_date=$request->input('expiry_date');
        $Job->total_openings=$request->input('total_openings');
        $Job->salary_min=$request->input('salary_min');
        $Job->salary_max=$request->input('salary_max');
        $Job->state=$request->input('state');
        $Job->city=$request->input('city');
        $Job->area=$request->input('area');
        $Job->roles_responsibilities=$request->input('roles_responsibilities');
        $Job->status='enable';
        $Job->mini_edu_req=$request->input('mini_edu_req');
        $Job->year_req=$request->input('year_req');
        $Job->month_req=$request->input('month_req');
        $Job->skill_req=$request->input('skill_req');
        $Job->doc_req=$request->input('doc_req');
        $Job->add_req=$request->input('add_req'); 
            

        if($request->hasfile('descri_video'))
        {
            $video=$request->file('descri_video');
            $ext=$video->extension();
            $video_name=time().'.'.$ext;
            $video->move(public_path('video'),$video_name);
            $Job->descri_video=$video_name;
        }
        
        $Job->save();
        return response()->json([
            'status'=>200,
            'message'=>'Job Details Inserted Successfully',
            'id'    =>$Job->id
        ]);
    }


    
    public function update(Request $request)
    {    
        $validater = Validator::make($request->all(),
        [
            'job_id'=> 'required|integer',
            'job_title'=> 'required',
            'sub_title'=> 'required',
            'job_type'=> 'required',
            'job_category'=> 'required',
            'expiry_date'=> 'required|date',
            'total_openings'=> 'required|integer',
            'salary_min'=> 'required|integer',
            'salary_max'=> 'required|integer',
            'state'=> 'required',
            'city'=> 'required',
            'area'=> 'required',
            'roles_responsibilities'=> 'required',
            'mini_edu_req'=> 'required',
            'year_req'=> 'required',
            'month_req'=> 'required',
            'skill_req'=> 'required',
            'doc_req'=> 'required',
            'company_id'=> 'required',
            'add_req'=> 'string|nullable',
        ]);

        if($validater->fails())
        {
            return response()->json([
                'status'=>422,
                'validation_errors'=>$validater->errors(),
            ]);
        }else
        {
            $job =  Job::find($request->job_id);
           
            if($job)
            {
                
                // $Job = new Job;
                $job->call_action=$request->input('call_action');
                $job->company_id=$request->input('company_id');
                $job->call_action1=$request->input('call_action1');
                $job->job_title=$request->input('job_title');
                $job->sub_title=$request->input('sub_title');
                $job->job_type=$request->input('job_type');
                $job->job_category=$request->input('job_category');
                $job->expiry_date=$request->input('expiry_date');
                $job->total_openings=$request->input('total_openings');
                $job->salary_min=$request->input('salary_min');
                $job->salary_max=$request->input('salary_max');
                $job->state=$request->input('state');
                $job->city=$request->input('city');
                $job->area=$request->input('area');
                $job->roles_responsibilities=$request->input('roles_responsibilities');
                $job->status='enable';
                $job->mini_edu_req=$request->input('mini_edu_req');
                $job->year_req=$request->input('year_req');
                $job->month_req=$request->input('month_req');
                $job->skill_req=$request->input('skill_req');
                $job->doc_req=$request->input('doc_req');
                $job->add_req=$request->input('add_req'); 
               
                if($request->hasfile('descri_video'))
                {
                   
                    if(File::exists(public_path('video/'.$job->descri_video)))
                    {
                        File::delete(public_path('video/'.$job->descri_video));
                    }

                    
                    $video      =   $request->file('descri_video');
                    $ext        =   $video->extension();
                    $video_name =   time().'.'.$ext;
                    
                    $video->move(public_path('video'),$video_name);
                    $job->descri_video=$video_name;
                    
                }

                $job->save();
                return response()->json([
                    'status'=>200,
                    'message'=>'Job Details updated'

                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'Something Went Wrong'
                ]);
            }
        }
    }

    public function do_like(Request $request)
    {
        $table_name = $request->type; // Either jobs OR Product can be sent
        // print_r($request->all());
        if($table_name =='jobs')
        {
            // Make Sure its unique
            $table_row =  DB::table('jobs_liked_by_user')
            ->where('job_id', $request->id)
            ->where('user_id', $request->user_id)->get();
            
            if($table_row->count())
            {
                return response()->json([
                    'status'=>409,
                    'message'=>  'Already Liked',
                ],409);
            }
            else
            {
                $update     = DB::table($table_name)->where('id', $request->id)->update(['like_count'=> DB::raw('like_count + 1'),]);
                if($update){
                    // Create User Like Log    
                    DB::table('jobs_liked_by_user')->insert(
                        [
                            'job_id' => $request->id,
                            'user_id' => $request->user_id
                        ]);
                    return response()->json([
                        'status'=>200,
                        'message'=>  rtrim($table_name, "s"). ' Liked',
                    ]);
                }
                
            }
        }
        
    }


    public function export_jobs(Request $request)
    {
        $export_as = $request->input('export_as');
        $export_as = 'xlsx'; //csv,xlsx 
        return Excel::download(new JobsExport, 'jobs.'.$export_as);
    }
    
    /**
     * GYAANESH WORK ENDS
     */
    
     

    public function delete(Request $request, $id)
    {
        # code...
        $Job = Job::find($id);
        
        
        $Job->delete();
        return response()->json([
            'status'=>204,
            'message'=>'Job Details  Deleted'
        ]);
        

    }
    
    public function disable_status(Request $request, $id)
    {
        # code..
           $update= DB::table('jobs')
        ->where('id', $id)
        ->update([
            'status'     => 'disable'
        ]);
        if($update){
            return response()->json([
                'status'=>200,
                'message'=>'jobs status disable'
            ]);
        }
    }
    
    public function enable_status(Request $request, $id)
    {
        # code..
           $update= DB::table('jobs')
        ->where('id', $id)
        ->update([
            'status'     => 'enable'
        ]);
        if($update){
            return response()->json([
                'status'=>200,
                'message'=>'jobs status enable'
            ]);
        }
    }

    
    
}
