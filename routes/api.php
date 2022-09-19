<?php


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\JobsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\dynamic_dropdownController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ProductController;

use App\Http\Controllers\frontend\UserRegistrationController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/* ---------------------- Admin Api Start ----------------------*/
//for admin login
// Route::middleware(['auth:sanctum'])->group(function (){
    
    Route::post('/logout',[AuthController::class,'logout']);



    
// });
Route::post('/admin_register',[AuthController::class,'register']);
Route::post('/add_employee',[AuthController::class,'add_employee']);
Route::get('/get_team',[AuthController::class,'get_team']);
Route::get('/view_employee',[AuthController::class,'view_employee']);
Route::post('/enable_employee/{id}',[AuthController::class,'enable_employee']);
Route::post('/disable_employee/{id}',[AuthController::class,'disable_employee']);
Route::get('/edit_employee/{id}',[AuthController::class,'edit']);
Route::post('/update_employee/{id}',[AuthController::class,'update_employee']);
Route::get('/check_user/{id}',[AuthController::class,'check_user']);
Route::get('/get_user/{id}',[AuthController::class,'edit']);

Route::delete('/delete_employee/{id}',[AuthController::class,'employe_delete']);

Route::post('/admin_login',[AuthController::class,'login']);
//login with google
Route::post('/logingoogle',[AuthController::class,'google_login']);
// Company

Route::post('/add_company',[CompanyController::class,'store']);
Route::get('/view_company',[CompanyController::class,'index']);
Route::get('/edit_company/{id}',[CompanyController::class,'edit']);
Route::post('/update_company/{id}',[CompanyController::class,'update']);
Route::delete('/delete_company/{id}',[CompanyController::class,'delete']);
Route::post('/enable_company/{id}',[CompanyController::class,'enable_company']);
Route::post('/disable_company/{id}',[CompanyController::class,'disable_company']);
Route::get('/fetch_company/{company_legal_name}',[CompanyController::class,'select_company']);


//job Category

Route::post('/add_category',[CategoryController::class,'store']);

Route::get('/view_category',[CategoryController::class,'index']);
Route::get('/edit_category/{id}',[CategoryController::class,'edit']);
Route::post('/update_category/{id}',[CategoryController::class,'update']);
Route::delete('/delete_category/{id}',[CategoryController::class,'delete']);
Route::post('/disable_category/{id}',[CategoryController::class,'disable_status']);
Route::post('/enable_category/{id}',[CategoryController::class,'enable_status']);
//product category
Route::post('/add_procategory',[CategoryController::class,'procategory']);
Route::get('/view_procategory',[CategoryController::class,'pro_cat']);
Route::get('/edit_procategory/{id}',[CategoryController::class,'pro_catedit']);
Route::post('/update_procategory/{id}',[CategoryController::class,'pro_catupdate']);
Route::delete('/delete_procategory/{id}',[CategoryController::class,'pro_catdelete']);
Route::post('/disable_procategory/{id}',[CategoryController::class,'pro_catdisable_status']);
Route::post('/enable_procategory/{id}',[CategoryController::class,'pro_catenable_status']);

//product sub category
Route::post('/add_prosubcategory',[SubcategoryController::class,'prosubcategory']);
Route::get('/view_prosubcategory',[SubcategoryController::class,'pro_subcat']);
Route::get('/edit_prosubcategory/{id}',[SubcategoryController::class,'pro_subcatedit']);
Route::post('/update_prosubcategory/{id}',[SubcategoryController::class,'pro_subcatupdate']);
Route::delete('/delete_prosubcategory/{id}',[SubcategoryController::class,'pro_subcatdelete']);
Route::post('/disable_prosubcategory/{id}',[SubcategoryController::class,'pro_subcatdisable_status']);
Route::post('/enable_prosubcategory/{id}',[SubcategoryController::class,'pro_subcatenable_status']);

//Jobs

Route::post('/add_job',[JobsController::class,'store']);
Route::middleware('auth:sanctum')->get('/view_jobs',[JobsController::class,'index']);
Route::get('/view_jobs/{id}',[JobsController::class,'index']);
Route::get('/edit_jobs/{id}',[JobsController::class,'edit']);
Route::post('/update_job',[JobsController::class,'update']);
Route::post('/do_like',[JobsController::class,'do_like']);
Route::delete('/delete_jobs/{id}',[JobsController::class,'delete']);
Route::post('/disable_jobs/{id}',[JobsController::class,'disable_status']);
Route::post('/enable_jobs/{id}',[JobsController::class,'enable_status']);

//product

Route::post('/add_product',[ProductController::class,'store']);
Route::get('/view_product',[ProductController::class,'index']);
Route::post('/project/do_like',[ProductController::class,'do_like']);
Route::get('/edit_product/{id}',[ProductController::class,'edit']);

Route::post('/update_product/{id}',[ProductController::class,'update']);
Route::delete('/delete_product/{id}',[ProductController::class,'delete']);
Route::post('/disable_product/{id}',[ProductController::class,'disable_status']);
Route::post('/enable_product/{id}',[ProductController::class,'enable_status']);

//Tasks
Route::post('/add_task',[TaskController::class,'store']);
Route::get('/view_task',[TaskController::class,'index']);
Route::get('/edit_task/{id}',[TaskController::class,'edit']);
Route::post('/update_task_status',[TaskController::class,'update_status']);
Route::post('/update_task',[TaskController::class,'update']);

Route::delete('/delete_task/{id}',[TaskController::class,'delete_task']);
Route::post('/disable_task/{id}',[TaskController::class,'disable_status']);
Route::post('/enable_task/{id}',[TaskController::class,'enable_status']);

//Project 
Route::get('/projects',[ProjectController::class,'index']);
Route::post('/add_project',[ProjectController::class,'store']);
Route::post('/create_project_application',[ProjectController::class,'create_project_application']);
Route::get('/edit_project/{id}',[ProjectController::class,'edit']);
Route::post('/update_project',[ProjectController::class,'update']);
Route::post('/project/update_status',[ProjectController::class,'update_status']);
Route::delete('/delete_project/{id}',[ProjectController::class,'delete']);
Route::post('/disable_project/{id}',[ProjectController::class,'disable_status']);
Route::post('/enable_project/{id}',[ProjectController::class,'enable_status']);

//application 

Route::get('/view_application',[ApplicationController::class,'application_view']);
Route::post('/store_application',[ApplicationController::class,'store']);
Route::post('/update_application_stage',[ApplicationController::class,'update_application_stage']);
Route::get('/view_application_refferd',[ApplicationController::class,'application_view_refferd']);
Route::post('/assign_application',[ApplicationController::class,'Application_assign']);
Route::get('/application_view/{id}',[ApplicationController::class,'get_application_details']);
Route::post('/application_status/{id}',[ApplicationController::class,'update_status']);
Route::post('/application_remark/{id}',[ApplicationController::class,'application_remark']);
Route::post('/create_follow_up',[ApplicationController::class,'create_follow_up']);
Route::post('/close_follow_up',[ApplicationController::class,'close_follow_up']);
Route::get('/get_my_follow_up',[ApplicationController::class,'get_my_follow_up']);
Route::post('/referredstatus',[ApplicationController::class,'referredstatus']);
Route::get('/applcation_followup_fetch/{id}',[ApplicationController::class,'applcation_followup_fetch']);
Route::get('/applcation_expire/{id}',[ApplicationController::class,'applcation_expire']);
Route::delete('/delete_project/{id}',[ProjectController::class,'delete']);


//dynamic_dropdown

Route::get('/state',[dynamic_dropdownController::class,'state']);
Route::get('/city/{id}',[dynamic_dropdownController::class,'city']);
Route::get('/area/{id}',[dynamic_dropdownController::class,'area']);
Route::get('/job_category',[dynamic_dropdownController::class,'job_category']);

Route::get('/product_category',[dynamic_dropdownController::class,'product_cat']);
Route::get('/product_subcategory/{id}',[dynamic_dropdownController::class,'product_subcat']);
/* ---------------------- Admin Api End ----------------------*/


/* ---------------------- Front Api Start ----------------------*/

//user registration
Route::post('/user_login',[UserRegistrationController::class,'phone_number']);
Route::post('/verify_user',[UserRegistrationController::class,'verify_number']);
Route::post('/upload_documents',[UserRegistrationController::class,'upload_document']);
Route::get('/document_status/{id}',[UserRegistrationController::class,'document_status']);


/* ---------------------- Front Api End ----------------------*/


/* EXPORT DATA APIs */
Route::get('/exportjobs',[JobsController::class,'export_jobs']);
