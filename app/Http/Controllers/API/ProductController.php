<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Storage;
use File;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products=DB::table('products')->orderby('id','desc')->get();

        return response()->json([
                'status'=>200,
                'products'=>$products
        ]);
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $product = new Product;
            $product->product_name=$request->input('product_name');
            $product->product_category=$request->input('product_category');
            $product->product_subcategory=$request->input('product_subcategory');
            $product->seo_title=$request->input('seo_title');
            $product->meta_desc=$request->input('meta_desc');
            // $product->meta_tag=$request->input('meta_tag');
            $product->min_price=$request->input('min_price');


            $product->max_price=$request->input('max_price');
            $product->discount=$request->input('discount');
            
           
            $product->status='enable';
            
           
            
                
        if($request->hasfile('product_img')){
            $image=$request->file('product_img');
            $ext=$image->extension();
            $image_name=time().'.'.$ext;
            $image->move(public_path('product'),$image_name);
            $product->product_img=$image_name;
         }else{
            $product->product_img=$request->input('product_img'); 
        }

       
        $product->save();
            return response()->json([
                'status'=>200,
                'message'=>'Product Details Inserted Successfully'
        ]);
        }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product,$id)
    {
        $Product = DB::table('products')
        ->join('product_categories', 'products.product_category', '=', 'product_categories.id')    
        ->join('product_subcategories', 'products.product_subcategory', '=', 'product_subcategories.id')   
        ->select('products.*', 'product_categories.cat_name','product_subcategories.subcat_name')->where('products.id',$id)->get()
        ;
        if($Product){
        return response()->json([
            'status'=>200,
            'product'=>$Product
            ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Product Id Not Found'
                    ]);
                
            }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $product =  Product::find($id);
            if($product){
            $product->product_name=$request->input('product_name');
            $product->product_category=$request->input('product_category');
            $product->product_subcategory=$request->input('product_subcategory');
            $product->seo_title=$request->input('seo_title');
            $product->meta_desc=$request->input('meta_desc');
            // $product->meta_tag=$request->input('meta_tag');
            $product->min_price=$request->input('min_price');


            $product->max_price=$request->input('max_price');
            $product->discount=$request->input('discount');
            
           
            $product->status='enable';
            
           
           
                    
                        
                    if($request->hasfile('product_img')){
                    $Image=DB::table('products')->where(['id'=>$id])->get();
                    $file=public_path('product/'.$Image[0]->product_img);
                    if(File::exists($file)){
                        File::delete($file);
                    }
                    $image=$request->file('product_img');
                    $ext=$image->extension();
                    $image_name=time().'.'.$ext;
                    $image->move(public_path('product'),$image_name);
                    $product->product_img=$image_name;
                }else{
                    $product->product_img=$request->input('product_img'); 
                }
                

                $product->save();
                    return response()->json([
                        'status'=>200,
                        'message'=>'Product Details updated'

                ]);
                }else{
                    return response()->json([
                        'status'=>404,
                        'message'=>'projects Id  Not Found'
                ]);
            }
        
            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        # code...
        $Job = Product::find($id);
        
        
        $Job->delete();
        return response()->json([
            'status'=>204,
            'message'=>'Product Details  Deleted'
        ]);
        

    }
    
    public function disable_status(Request $request, $id)
    {
        # code..
           $update= DB::table('products')
        ->where('id', $id)
        ->update([
            'status'     => 'disable'
        ]);
        if($update){
            return response()->json([
                'status'=>200,
                'message'=>'Product status disable'
            ]);
        }
        
        
        

    }
    public function enable_status(Request $request, $id)
    {
        # code..
           $update= DB::table('products')
        ->where('id', $id)
        ->update([
            'status'     => 'enable'
        ]);
        if($update){
            return response()->json([
                'status'=>200,
                'message'=>'Product status enable'
            ]);
        }
    }
}
