import axios from 'axios';
import React, { useState, useEffect } from 'react';
import swal from 'sweetalert';
import Select from 'react-select';
import Breadcrums from './Breadcrums';
import TagsInput from 'react-tagsinput'
 import { withRouter } from 'react-router-dom';
 
import 'react-tagsinput/react-tagsinput.css' // If using WebPack and style-loader.
 



function Edit_product(props) {
  const defaultSrc =
    "https://www.pngkit.com/png/full/301-3012694_account-user-profile-avatar-comments-fa-user-circle.png";
    const [pro_cat, setPro_cat] = useState('');
  const [pro_subcat, setPro_subcat] = useState('');
  const [loading, setLoading] = useState(true);
  const [pro_catResponse, setpro_catResponse] = useState([]);
  const [pro_subcatResponse, setpro_subcatResponse] = useState([]);

  const handleproduct_cat = val => {
    setPro_cat(val);
    if(!pro_cat){
      error_list.product_category='';
    }
    
  }
  const handleproduct_subcat = val => {
    setPro_subcat(val);
    if(!pro_subcat){
      error_list.product_subcategory='';
    }
  }
  
  

  // Fetching State
  useEffect(() => {
    axios.get('api/product_category').then((res) => {
      setpro_catResponse(res.data.product_cat);

    })
  }, [])
  const pro_catOption = pro_catResponse.map((response) => (
    { label: response.cat_name, value: response.id }
  ))

  // Fetching City
  var pro_cat_id = pro_cat.value;
  useEffect(() => {
    if (pro_cat_id) {
      axios.get('api/product_subcategory/' + pro_cat_id).then((res) => {

        setpro_subcatResponse(res.data.product_subcat);

      })
    }
  }, [pro_cat_id])
  const pro_subcatOption = pro_subcatResponse.map((response) => (
    { label: response.subcat_name, value: response.id }
  ))

  
 
  const [imageUrl,setImageUrl]=useState();
  const [tags,setTags]=useState([]);
  const [isSubmit, setIsSubmit] = useState(false);
  const [product_img,setProduct_img]=useState([]);
  const [PicType,setPicType]=useState([]);
  const [product, setProduct] = useState([]);
  
  const [error_list, setError] = useState({
    product_name: "",
    product_category: "",
    product_subcategory: "",
    seo_title:"",
    meta_tag:"",
    meta_desc:"",
    min_price:"",
    max_price:"",
    discount:"",
    product_img:""
  });
//   handleChange(tags) {
//     this.setState({tags})
//   }
 const handleTag = (e)=>{
     setTags(e);
    }
  const handelproduct = (e) => {
    //const { name, value } = e.target;
    setProduct({ ...product, [e.target.name]: e.target.value });
    if(e.target.name==='product_name'){
      error_list.product_name='';
    }
    if(e.target.name==='seo_title'){
      error_list.seo_title='';
    }
    if(e.target.name==='meta_desc'){
      error_list.meta_desc='';
    }
    if(e.target.name==='min_price'){
      error_list.min_price='';
    }
    if(e.target.name==='max_price'){
      error_list.max_price='';
    }
    if(e.target.name==='discount'){
      error_list.discount='';
    }
    if(e.target.name==='product_img'){
      error_list.product_img='';
    }
    if(e.target.name==='product_img'){
    setProduct_img(e.target.files[0]);
    setPicType(e.target.files[0].name.split('.').pop());
    let value = URL.createObjectURL(e.target.files[0]);
    setImageUrl(value);
    }
  }
  useEffect(() => {
    if (Object.keys(error_list).length === 0 ) {
        

    }
  }, [error_list]);

  useEffect(() => {
    const product_id = props.match.params.id;
    axios.get(`/api/edit_product/${product_id}`).then(res => {
      console.log(res.data.product[0]);
      if (res.data.status === 200) {
        setProduct(res.data.product[0]);
        setPro_cat({ label:res.data.product[0].cat_name, value:res.data.product[0].product_category })
        setPro_subcat({ label:res.data.product[0].subcat_name, value:res.data.product[0].product_subcategory })

      } else if (res.data.status === 404) {
        swal('error', res.data.message, 'error');
        history.push('/admin/view_product');
      }
    });

  }, [props.match.params.id])
  const submitproduct = (e) => {

    e.preventDefault();
    setError(validate(product));
    setIsSubmit(true);
    const product_id = props.match.params.id;
    const formData = new FormData();


    formData.append('product_name', product.product_name);
    formData.append('product_category',pro_cat.value);
    formData.append('product_subcategory',pro_subcat.value);
    formData.append('seo_title',product.seo_title);
    // formData.append('meta_tag',tags);
    formData.append('meta_desc',product.meta_desc);
    formData.append('min_price',product.min_price);
    formData.append('max_price',product.max_price);
    formData.append('discount',product.discount);


    formData.append('product_img',product_img);
    

      axios.post('api/update_product/'+product_id, formData).then(res => {
        if (res.data.status === 200) {


          
          swal("Success", res.data.message, "success");

          setError([]);


        } else {
          alert('api is not work')
          //setError(res.data.validation_errors);
        }

      });
    
    


  }
  const validate = (values) => {
    const errors = {};
    

    if (!values.product_name) {
      errors.product_name = "Name is required!";
    }
    if (!pro_cat) {
      errors.product_category = "Select product category";
    } 
    if (!pro_subcat) {
      errors.product_subcategory = "Select product sub category";
    } 
     if (!values.max_price) {
      errors.max_price = "Enter max price";
    }
    if(!values.min_price){
        errors.min_price = "Enter min price";
      } 
      if(!values.seo_title){
        errors.seo_title = "Enter Seo tite";
      } 
      // if(values.meta_tag){
      //   errors.meta_tag = "meta_tag";
      // }  
      if(!values.meta_desc){
        errors.meta_desc = "Enter meta description";
      } 
      if(!values.discount){
        errors.discount = "Enter discount";
      } 
    if(PicType=='jpeg'|| PicType=='jpg' ||PicType=='png'||!values.profile_pic){

    }else{
      errors.profile_pic = "Profile image must be jpeg,png,jpg type ";
    }
    
    return errors;
  };

  return (
    <div className='container-fluid paddingRight mt-4'>   
       <Breadcrums name='Edit Product'/>

      <div className='row'>
        <div className='col-lg-6 col-md-6 col-sm-12 col-12 detailsbutton'>
          <div className='row'>
            <div className='col-lg-4 col-md-6 col-sm-12 col-12 buttoncolor buttonRL active'><a>Edit Product</a></div>

          </div>

        </div>
      </div>
      <div className='row'>
        <div className='col-12 MainBoxC-details'>
          <div className='row'>
            <div className='col-12'>
              <form onSubmit={submitproduct} encType="multipart/form-data" id="from_company">
                {/*  name */}
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Employee Name" className="labelStyle">Product Name</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                      <input type="text"
                        name="product_name"
                        value={product.product_name}
                        onChange={handelproduct}
                        placeholder='Enter Product Name'
                        className="form-control" id="product_name" />

                    </div>
                    <span className='text-danger'>{error_list.product_name}</span>
                    
                  </div>


                </div>
              
                {/* Product category & sub category  */}
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 '>
                    <label htmlFor="company popular name" className="labelStyle">Product Category  & Sub category</label>
                  </div>
                  <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-center InputBox InputHeight">
                    <div className="input-group">
                      <Select

                        className="custom-select  custom-selectMultiSelect"
                        onChange={handleproduct_cat}
                        value={pro_cat}
                        placeholder='Select  Category'
                        options={pro_catOption}
                        //defaultValue={{ label: '', value:pro_cat }}
                        id="employee_designation" />
                    </div>
                    <span className='text-danger'>{error_list.product_category}</span>
                    {/* {errors.employee_designation && <span  className='text-danger'> {errors.employee_designation?.message}</span>} */}

                  </div>
                  <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-center InputBox InputHeight">
                    <div className="input-group">
                      <Select

                        className="custom-select  custom-selectMultiSelect"
                        onChange={handleproduct_subcat}
                        value={pro_subcat}
                        placeholder='Select Sub Category'
                        options={pro_subcatOption}
                        id="employee_designation" />
                    </div>
                    <span className='text-danger'>{error_list.product_subcategory}</span>
                    {/* {errors.employee_designation && <span  className='text-danger'> {errors.employee_designation?.message}</span>} */}

                  </div>
                </div>
                 {/* Product max & min price */}
                 <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 '>
                    <label htmlFor="company popular name" className="labelStyle">Product Max & Min Price</label>
                  </div>
                  <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-center InputBox InputHeight">
                    <div className="input-group">
                    <input type="number"
                        name="min_price"
                        value={product.min_price}
                        onChange={handelproduct}
                        placeholder='Enter Min Price'
                        className="form-control" id="min_price" />
                    </div>
                    <span className='text-danger'>{error_list.min_price}</span>

                  </div>
                  <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 text-center InputBox InputHeight">
                    <div className="input-group">
                    <input type="number"
                        name="max_price"
                        value={product.max_price}
                        onChange={handelproduct}
                        placeholder='Enter Max Price'
                        className="form-control" id="max_price" />
                    </div>
                    <span className='text-danger'>{error_list.max_price}</span>

                  </div>
                </div>
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Employee Name" className="labelStyle">Seo Title</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                      <input type="text"
                        name="seo_title"
                        value={product.seo_title}
                        //{...register("employee_name",{required:'Employee Name is required'})}
                        onChange={handelproduct}
                        placeholder='Enter Seo Title'
                        className="form-control" id="seo_title" />

                    </div>
                    <span className='text-danger'>{error_list.seo_title}</span>
                    {/* {errors.employee_name && <span className='text-danger'>{errors.employee_name?.message}</span>} */}
                  </div>


                </div>
                {/* discount */}
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Discount" className="labelStyle">Discount</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                      <input type="number"
                        name="discount"
                        value={product.discount}
                        onChange={handelproduct}
                        placeholder='Enter Discount'
                        className="form-control" id="discount" />

                    </div>
                    <span className='text-danger'>{error_list.discount}</span>
                    {/* {errors.employee_name && <span className='text-danger'>{errors.employee_name?.message}</span>} */}
                  </div>
                </div>

                {/* <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Meta Tags" className="labelStyle">Meta Tags</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                    <TagsInput value={tags} name="meta_tag" onChange={handleTag}  />

                    </div>
                    <span className='text-danger'>{error_list.meta_tag}</span>
                  </div>
                </div> */}
                  
                    {/*  meta description */}
                
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Employee Name" className="labelStyle">Meta Description</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                    <textarea name="meta_desc"
                          placeholder='Ex : Eleifend risus ante ad erat elit quisque cursus, quisque mollis aenean ultrices arcu auctor, sollicitudin curae vitae quam netus facilisis, tristique tellus viverra, a cubilia luctus viverra eleifend magna varius, ultrices sed etiam at velit habitant tristique lectus!'
                          onChange={handelproduct}
                          className="form-control" id="meta_desc" rows="4" cols="5" value={product.meta_desc}>
                        </textarea>

                    </div>
                    <span className='text-danger'>{error_list.meta_desc}</span>
                    {/* {errors.employee_name && <span className='text-danger'>{errors.employee_name?.message}</span>} */}
                  </div>


                </div>
                {/* Button */}
                <div className='row'>
                  <div className='col-12 button-box'>
                    <button type="submit" className='backgroung-orange'>Submit</button>
                  </div>

                </div>
              </form>

            </div>
          </div>

        </div>
      </div>
    </div>);

}

export default withRouter(Edit_product);