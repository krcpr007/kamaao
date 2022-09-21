<?php


namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Traits\is_enabledTrait;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Storage;
use File;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    use is_enabledTrait;
    
    public function index()
    {
        # code...
        $Company= Company::all();

        return response()->json([
                'status'=>200,
                'company'=>$Company
        ]);

    }

    public function update_status(Request $request)
    {
        return $update = $this->toggle_is_enable('companies', 'id', $request->id, $request->new_status);
    }
    

    public function select_company($company_legal_name){
        $Company = Company::find($company_legal_name);
        if($Company){
        return response()->json(
            $Company
            );
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Company Id Not Found'
                    ]);
                
            }

    }
    public function edit($id){
        $Company = Company::find($id);
        if($Company){
        return response()->json([
            'status'=>200,
            'company'=>$Company
            ]);
            }else{
                return response()->json([
                    'status'=>404,
                    'message'=>' Company Id Not Found'
                    ],404);
                
            }
    }
    public function store(Request $request)
    {
        $validater = Validator::make($request->all(),[
            'company_legal_name'=>'bail|required|unique:companies,company_legal_name|min:3',
            'company_popular_name'=>'required',
            'company_url'=>'required|url',
            'company_logo'=>'required|mimes:jpeg,jpg,png',
            'about_company'=>'required',
        ]);

        if($validater->fails())
        {
            return response()->json([
                    'status'            => 422,
                    'errors' =>  $validater->errors()
            ],422);
        }
        else
        {
            $Company = new Company;
            $Company->company_legal_name=$request->input('company_legal_name');
            $Company->company_popular_name=$request->input('company_popular_name');
            $Company->company_url=$request->input('company_url');
            $Company->about_company=$request->input('about_company');
            $Company->call_action=$request->input('call_action');
            $Company->call_action1=$request->input('call_action1');
                
            if($request->hasfile('company_logo')){
                $image=$request->file('company_logo');
                $ext=$image->extension();
                $image_name=time().'.'.$ext;
                $image->move(public_path('company'),$image_name);
                $Company->company_logo=$image_name;
            }
            
            $Company->save();
                return response()->json([
                    'status'=>200,
                    'message'=>'Company Details Inserted Successfully'
            ]);
        }

    }

    

    public function update(Request $request, $id)
    {    
        
        $validater = Validator::make($request->all(),[
            'company_legal_name'=>'required|min:3|unique:companies,company_legal_name,'.$id,
            'company_popular_name'=>'required|min:3',
            'company_popular_name'=>'required',
            'company_url'=>'required|url',
            'company_logo'=>'nullable|mimes:jpeg,jpg,png',
            'about_company'=>'required',
        ]);

        if($validater->fails())
        {
                return response()->json([
                        'status'=>422,
                        'errors'=>$validater->errors(),
                ],422);
        }
        else
        {
            $Company =  Company::find($id);
            if($Company){
            $Company->company_legal_name=$request->input('company_legal_name');
            $Company->company_popular_name=$request->input('company_popular_name');
            $Company->company_url=$request->input('company_url');
            $Company->about_company=$request->input('about_company');
            $Company->call_action=$request->input('call_action');
            $Company->call_action1=$request->input('call_action1');
                
            if($request->hasfile('company_logo'))
            {
                $Image=DB::table('companies')->where(['id'=>$id])->get();
                $file=public_path('company/'.$Image[0]->company_logo);
                if(File::exists($file))
                {
                    File::delete($file);
                }
                $image=$request->file('company_logo');
                $ext=$image->extension();
                $image_name=time().'.'.$ext;
                $image->move(public_path('company'),$image_name);
                $Company->company_logo=$image_name;
            }

            $Company->save();
                return response()->json([
                    'status'=>200,
                    'message'=>'Company Details updated'

            ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'Company Id  Not Found'
                ]);
            }
        }
    }   

   public function enable_company(Request $request, $id)
   {
       # code...
       
       $Company=Company::find($id);
       if($Company){
           $Company->status='enable';
           $Company->save();
           return response()->json([
               'status'=>200,
               'message'=>'Company status enable'
           ]);
       }
    }
    
    public function disable_company(Request $request, $id)
    {
        $Company=Company::find($id);
        if($Company)
        {
           $Company->status='disable';
           $Company->save();
           return response()->json([
               'status'=>200,
               'message'=>'Company status disable'
           ]);
        }
    }

    public function delete(Request $request, $id)
    {
        $Company = Company::find($id);
        
        $Image=DB::table('companies')->where(['id'=>$id])->get();
            $file=public_path('company/'.$Image[0]->company_logo);
        if(File::exists($file))
        {
            File::delete($file);    
        }
        $Company->delete();
        return response()->json([
            'status'=>204,
            'message'=>'Company Details  Deleted'
        ]);
    }
}
