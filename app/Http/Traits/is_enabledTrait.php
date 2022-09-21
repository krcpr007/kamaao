<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\DB;

trait is_enabledTrait
{
    public function toggle_is_enable($tableName, $whereColumn, $id, $status)
    {
        $update   =   DB::table($tableName)->where($whereColumn, $id)->update(['is_enabled'=>$status]);
        if($update)
        {
            return response()->json([
                'status'=>200,
                'message'=>'Status Updated'
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
}
