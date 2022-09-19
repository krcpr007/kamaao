<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\DB;

trait do_likeTrait
{
    public function like($request)
    { 
        
        $table_name         =   $request->type;
        $users_liked_table  =   '';
        $relation_column    =   '';
        // print_r($request->all());
        switch ($table_name) 
        {
            
            case 'products': 
                
                $users_liked_table  =   'products_liked_by_user';
                $relation_column    =   'product_id';
                break;
            case 'projects': 

                $users_liked_table  =   'projects_liked_by_user';
                $relation_column    =   'project_id';

                break;
            
            default: 
                $users_liked_table = 'jobs_liked_by_user';
                $relation_column    =   'job_id';
                break;
        } 
            // Make Sure its unique
        $table_row =  DB::table($users_liked_table)
        ->where($relation_column, $request->id)
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
                DB::table($users_liked_table)->insert(
                    [
                        $relation_column => $request->id,
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
