<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class dynamic_dropdownController extends Controller
{
    public function state(){
        $state=DB::table('states')->get();

        return response()->json([
                'status'=>200,
                'state'=>$state
        ]);
    }

    public function city(Request $request,$id){
        $city=DB::table('cities')->where(['state_id'=>$id])->get();

        return response()->json([
                'status'=>200,
                'city'=>$city
        ]);
    }
    public function area(Request $request,$id){
        $area=DB::table('areas')->where(['city_id'=>$id])->get();

        return response()->json([
                'status'=>200,
                'area'=>$area
        ]);
    }

    public function job_category(){
        $job_category=DB::table('categories')->where(['status'=>'enable'])->get();

        return response()->json([
                'status'=>200,
                'job_category'=>$job_category
        ]);
    }
    public function product_cat(){
        $product_cat=DB::table('product_categories')->where(['status'=>'enable'])->get();

        return response()->json([
                'status'=>200,
                'product_cat'=>$product_cat
        ]);
    }
    public function product_subcat($id){
        $product_subcat=DB::table('product_subcategories')->where([['status','=','enable'],['cat_id','=',$id]])->get();
        if($product_subcat){
        return response()->json([
                'status'=>200,
                'product_subcat'=>$product_subcat
        ]);
    }else{
        return response()->json([
            'status'=>404,
            'product_subcat'=>'Not Found'
    ]);
    }
    }
}
