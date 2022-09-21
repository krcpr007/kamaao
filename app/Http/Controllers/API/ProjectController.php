<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Add_project;
use App\Http\Traits\is_enabledTrait;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\project_application;
use App\Models\task;
use App\Models\task_steps;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Storage;
use File;

class ProjectController extends Controller
{
    use is_enabledTrait;

    /**
     * Gyaanesh Work Starts
     */

    public function index()
    {
        $projects=Project::with('task')->orderby('id','desc')->get();
        foreach ($projects as  $project) 
        {
            foreach ($project->task as $t) {
                $t['steps'] = task_steps::where('task_id', $t->id)->get();
            }
        }
        
        return response()->json([
                'status'=>200,
                'projects'=>$projects
        ]);
    }

    
    public function store(Add_project $request)
    { 
        $Project = new Project;
        $Project->project_title     =   $request->input('project_title');
        $Project->pr_sub_title      =   $request->input('pr_sub_title');
        $Project->project_type      =   $request->input('project_type');
        $Project->start_date        =   $request->input('start_date');
        $Project->end_date          =   $request->input('end_date');
        $Project->total_openings    =   $request->input('total_openings');
        $Project->amount            =   $request->input('amount');
        $Project->state             =   $request->input('state');
        $Project->city              =   $request->input('city');
        $Project->area              =   $request->input('area');
        $Project->term_condition    =   $request->input('term_condition');
        $Project->status            =   1;
        $Project->mini_edu_req      =   $request->input('mini_edu_req');
        $Project->experience_req    =   $request->input('experience_req');
        $Project->skill_req         =   $request->input('skill_req');
        $Project->doc_req           =   $request->input('doc_req');
        $Project->add_req           =   $request->input('add_req');
        $Project->opening_left      =   $request->total_openings;
        if($request->hasfile('descri_video')){
            $video=$request->file('descri_video');
            $ext=$video->extension();
            $video_name=time().'.'.$ext;
            $video->move(public_path('video'),$video_name);
            $Project->descri_video=$video_name;
        }
        $Project->save();

        foreach ($request->tasks as $tasks) 
        {   
            $task   =   new task();
            $task->task_title           =  $tasks['task_title'];
            $task->type                 =  $tasks['type'];
            $task->company_id           =  $tasks['company_id'];
            $task->price                =  $tasks['price'];
            $task->expiry_date          =  $tasks['expiry_date'];
            $task->number_of_steps      =  $tasks['number_of_steps'];
            $task->tnc                  =  $tasks['tnc'];
            $task->belong_to_project    =  $Project->id;
            $task->save();
            foreach ( $tasks['steps'] as $step) {
                    
                $steps  =   new task_steps();
                $steps->step_title          =   $step['step_title'];
                $steps->complete_in_days    =   $step['complete_in_days'];
                $steps->task_id             =   $task->id;
                $steps->save();
            }
        }
        return response()->json([
            'status'=>200,
            'message'=>'Project Details Inserted Successfully',
            'id'=> $Project->id
        ]);
           
            
    }
      
    public function edit($id)
    {
        $projects=  Project::with('task')->where('id', $id)->orderby('id','desc')->get();
        foreach ($projects as  $project) 
        {
            foreach ($project->task as $t) {
                $t['steps'] = task_steps::where('task_id', $t->id)->get();
            }
        }
        
        return response()->json([
                'status'=>200,
                'projects'=>$projects
        ]);
    }
 
    public function update(Request $request)
    { 
        $Project = Project::find($request->project_id);
        if(!$Project)
        {
            return response()->json([
                'status'=>404,
                'message'=>"Project Not Found"
            ],404);
        }
        if($request->task_updated=="yes")
        {
            // First Delete Old Tasks And Its Steps
            $tasks = task::with('task_steps')->where('belong_to_project', $request->project_id)->get();
            foreach ($tasks as $task) 
            {
                foreach($task['task_steps'] as $step)
                {
                    task_steps::find($step['id'])->delete();
                }
            }
            task::where('belong_to_project',$request->project_id )->delete();
        } 
        //Crreate Updated Data for Project
        $Project->project_title     =   $request->input('project_title');
        $Project->pr_sub_title      =   $request->input('pr_sub_title');
        $Project->project_type      =   $request->input('project_type');
        $Project->start_date        =   $request->input('start_date');
        $Project->end_date          =   $request->input('end_date');
        $Project->total_openings    =   $request->input('total_openings');
        $Project->amount            =   $request->input('amount');
        $Project->state             =   $request->input('state');
        $Project->city              =   $request->input('city');
        $Project->area              =   $request->input('area');
        $Project->term_condition    =   $request->input('term_condition');
        $Project->status            =   1;
        $Project->mini_edu_req      =   $request->input('mini_edu_req');
        $Project->experience_req    =   $request->input('experience_req');
        $Project->skill_req         =   $request->input('skill_req');
        $Project->doc_req           =   $request->input('doc_req');
        $Project->add_req           =   $request->input('add_req');
            
        if($request->hasfile('descri_video')){
            $video=$request->file('descri_video');
            $ext=$video->extension();
            $video_name=time().'.'.$ext;
            $video->move(public_path('video'),$video_name);
            $Project->descri_video=$video_name;
        }
        $Project->update();
        foreach ($request->tasks as $tasks) 
        {   
            $task   =   new task();
            $task->task_title           =  $tasks['task_title'];
            $task->type                 =  $tasks['type'];
            $task->company_id           =  $tasks['company_id'];
            $task->price                =  $tasks['price'];
            $task->expiry_date          =  $tasks['expiry_date'];
            $task->number_of_steps      =  $tasks['number_of_steps'];
            $task->tnc                  =  $tasks['tnc'];
            $task->belong_to_project    =  $request->project_id;
            $task->save();
            foreach ( $tasks['steps'] as $step) {
                    
                $steps  =   new task_steps();
                $steps->step_title          =   $step['step_title'];
                $steps->complete_in_days    =   $step['complete_in_days'];
                $steps->task_id             =   $task->id;
                $steps->save();
            }
        }    
        return response()->json([
            'status'=>200,
            'message'=>"Project Updated"
        ]);
    }

    public function delete(Request $request, $id)
    { 
        //Find and Delete related Tasks Steps too
        $Project = Project::find($id);
        task::where('belong_to_project',$id)->delete();
        $Project->delete();
        return response()->json([
            'status'=>204,
            'message'=>'Project Details Deleted'
        ]);
    }

    public function update_status(Request $request)
    {
        return $update = $this->toggle_is_enable('projects', 'id', $request->id, $request->new_status);
    }
    
    public function do_like(Request $request)
    {
        return $this->like($request);
    }

    public function create_project_application(Request $request)
    {
        $hasUserApplied        =   project_application::where('user_id',$request->user_id )->where('project_id', $request->project_id)->get();
        if(count($hasUserApplied))
        {
            return response()->json([
                'status'=>400,
                'message'=>'Already Applied For This Job',
            ],400);
        }
        else
        {        
            $application                    =   new project_application();
            $application->user_id           =   $request->user_id;
            $application->project_id        =   $request->project_id;
            $request->request->add(['status' => '0']);
            $application->save();
                       
            return response()->json([
                'status'=>200,
                'message'=>'Application Submitted successfully'
            ]);
        }
    }

 
    /**
     * Gyaanesh Work Ends
     */
   
  

    
    
    public function disable_status(Request $request, $id)
    {
        # code..
           $update= DB::table('projects')
        ->where('id', $id)
        ->update([
            'status'     => 'disable'
        ]);
        if($update){
            return response()->json([
                'status'=>200,
                'message'=>'projects status disable'
            ]);
        }
        
        
        

    }
    public function enable_status(Request $request, $id)
    {
        # code..
           $update= DB::table('projects')
        ->where('id', $id)
        ->update([
            'status'     => 'enable'
        ]);
        if($update){
            return response()->json([
                'status'=>200,
                'message'=>'projects status enable'
            ]);
        }    
    }
    
}
