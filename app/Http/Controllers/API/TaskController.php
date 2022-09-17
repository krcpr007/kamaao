<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\add_task;
use App\Models\task;
use App\Models\task_steps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use File;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     /**
     * GYAANESH WORK STARTS HERE
    */
    public function index()
    {
        //
        $task=task::with('task_steps')->orderby('id','desc')->get();

        return response()->json([
                'status'=>200,
                'task'=>$task
        ]);
    }
   


    public function store(add_task $request)
    {  

        $task   =   new task();
        $task->task_title           =  $request->task_title;
        $task->type                 =  $request->type;
        $task->company_id           =  $request->company_id;
        $task->price                =  $request->price;
        $task->expiry_date          =  $request->expiry_date;
        $task->number_of_steps      =  $request->number_of_steps;
        $task->tnc                  =  $request->tnc;
        $task->Belong_to_project    =  $request->belong_to_project;
        $task->save();

        // insert Steps in task_steps Table
        foreach ($request->steps as $value) {
            
            $steps  =   new task_steps();
            $steps->step_title          =   $value['step_title'];
            $steps->complete_in_days    =   $value['complete_in_days'];
            $steps->task_id             =   $task->id;
            $steps->save();
        }
        
            return response()->json([
                'status'=>200,
                'message'=>'Task Details Inserted Successfully'
        ]);

    }

    public function update_status(Request $request)
    {
        $update   =   task::where('id',$request->id)->update(['is_enabled'=>$request->new_status]);
       
        if($update)
        {
            return response()->json([
                'status'=>200,
                'message'=>'Task Status Updated'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>401,
                'message'=>'Something Went Wrong'
            ]);
        }
    }


    public function edit($id)
    {
        
        $task = task::with('task_steps')->where('id', $id)->get();
        if($task)
        {
            return response()->json([
                'status'=>200,
                'task'=>$task
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

    public function update(add_task $request)
    {
        if(!$request->has("task_id") || !$request->has("is_steps_updated") )
        {
            return response()->json([
                'status' => 422,
                'meaasge' => "The given data was invalid to process with",
                'errors' => "field task_id and is_steps_updated are required"                
            ], 422);
        }
                
        $task =  task::find($request->task_id);
        $task->task_title           =  $request->task_title;
        $task->type                 =  $request->type;
        $task->company_id           =  $request->company_id;
        $task->price                =  $request->price;
        $task->expiry_date          =  $request->expiry_date;
        $task->number_of_steps      =  $request->number_of_steps;
        $task->tnc                  =  $request->tnc;
        $task->Belong_to_project    =  $request->belong_to_project;   

        if($task->save())
        {
            if($request->is_steps_updated   ==  'yes')
            {
                task_steps::where('task_id',$request->task_id)->delete();
                foreach ($request->steps as $value) {
                
                    $steps  =   new task_steps();
                    $steps->step_title          =   $value['step_title'];
                    $steps->complete_in_days    =   $value['complete_in_days'];
                    $steps->task_id             =   $task->id;
                    $steps->save();
                }
            }
        
            return response()->json([
                'status'=>200,
                'message'=>'Task Details updated'

            ]);
        }else
        {
            return response()->json([
                'status'=>404,
                'message'=>'Task Id  Not Found'
        ]);
        
        }
    }
    
    /**
     * GYAANESH WORK Ends HERE
    */





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function enable_status(Request $request,$id)
    // {
    //     $task=DB::table('tasks')
    //     ->where('id', $id)
    //     ->update(['status' => 'enable']);
    //     if($task){
    //         return response()->json([
    //             'status'=>200,
    //             'message'=>'Task Enable Successfully'
    //     ]);
    //     }
    // }

    
    // public function disable_status(Request $request,$id)
    // {
    //     $task=DB::table('tasks')
    //     ->where('id', $id)
    //     ->update(['is_enabled' => 0]);
    //     if($task){
    //         return response()->json([
    //             'status'=>200,
    //             'message'=>'Task Disable Successfully'
    //     ]);
    //     }
    // }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function delete_task(Request $request,$id)
    {
        //
        $task=DB::table('tasks')
        ->where('id', $id)
        ->delete();
        if($task){
            return response()->json([
                'status'=>204,
                'message'=>'Task Disable Successfully'
        ]);
        }
    }
}
