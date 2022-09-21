<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\is_enabledTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;


class SubcategoryController extends Controller
{
     //Start product category

    use is_enabledTrait;

    public function update_status(Request $request)
    {
        return $update = $this->toggle_is_enable('product_subcategories', 'id', $request->id, $request->new_status);
    }
        public function prosubcategory(Request $request )
        {
            $validater = Validator::make($request->all(),[
                'subcat_name'=>'unique:product_subcategories,subcat_name',
                
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
                $subcat_name=$request->input('subcat_name');
                $cat_name=$request->input('cat_name');
                $created_at	=date('d M,Y h:i:s a');
                $updated_at=date('d M,Y h:i:s a');
                $query=DB::table('product_subcategories')->insert(['subcat_name' =>$subcat_name, 'cat_id' =>$cat_name, 'status' =>'enable','created_at'=>$created_at ,'updated_at'=>$updated_at]);
                if($query){
                    return response()->json([
                        'status'=>200,
                        'message'=>'Sub Category Added'
                        ]);
                }
                else
                {
                    return response()->json([
                        'status'=>404,
                        'message'=>'Sub Category Not Added '
                    ],404);
                }
        }
    }


    public function pro_subcat()
    {
        # code...
        $sub_category= DB::table('product_subcategories')
        ->join('product_categories', 'product_subcategories.cat_id', '=', 'product_categories.id')
        
        ->select('product_subcategories.*', 'product_categories.cat_name')
        ->orderby('id','desc')->get();
        return response()->json([
                'status'=>200,
                'prosubcategory'=>$sub_category
        ]);

    }

    public function pro_subcatedit($id)     
    {

        $Category = DB::table('product_subcategories')
        ->join('product_categories', 'product_subcategories.cat_id', '=', 'product_categories.id')    
        ->select('product_subcategories.*', 'product_categories.cat_name')
        ->where('product_subcategories.id',$id)->get();
        if($Category)
        {
            return response()->json([
                'status'=>200,
                'category'=>$Category
                ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>' Sub Category Id Not Found'
            ]);
        }
    }

    public function pro_subcatupdate(Request $request,$id)
    {
        $Category = DB::table('product_subcategories')->find($id);
        if($Category){
            $subcat_name=$request->input('subcat_name');
            $cat_name=$request->input('cat_name');
            $status='enable';
            $updated_at=date('d M,Y h:i:s a');

        $update=DB::table('product_subcategories')
        ->where('id', $id)
        ->update(['subcat_name' => $subcat_name,'cat_id' => $cat_name,'status'=>$status,'updated_at'=>$updated_at]);
            return response()->json([
                'status'=>200,
                'message'=>'Category Details Update Successfully'
        ]);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>'Category Id  Not Found'
        ]);

        }
    
    
    }

    public function pro_subcatenable_status(Request $request, $id)
    {
        $Category=DB::table('product_subcategories')
        ->where('id', $id)
        ->update(['status'=>'enable']);
        if($Category)
        {
            return response()->json([
                'status'=>200,
                'message'=>'Category status enable'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>200,
                'message'=>'Category status not enable'
            ]); 
        }
    }
    
    public function pro_subcatdisable_status(Request $request, $id)
    {
        $Category=DB::table('product_subcategories')
        ->where('id', $id)
        ->update(['status'=>'disable']);
        if($Category)
        {    
            return response()->json([
                'status'=>200,
                'message'=>'Category status disable'
            ]);
        }
    }
    
    public function pro_subcatdelete(Request $request, $id)
    {
        $Category = DB::table('product_subcategories')->where('id', $id)->delete();
        if($Category)
        {
            return response()->json([
                'status'=>200,
                'message'=>'Category Details  Deleted'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>400,
                'message'=>'Something Went Wrong'
            ]);
        }
    }
}
