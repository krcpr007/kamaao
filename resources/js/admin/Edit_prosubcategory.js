import axios from 'axios';
import React, { useState, useEffect } from 'react';
import swal from 'sweetalert';
import {useHistory,withRouter} from 'react-router-dom';
import Breadcrums from './Breadcrums';
import {Select } from 'antd';




function Edit_prosubcategory(props) {
  const history = useHistory();
  const [loading,setLoading]=useState(true);
  const [product_cat,setproduct_catResponse]=useState('');
  const cat_id = props.match.params.id;
  const [isSubmit, setIsSubmit] = useState(false);
 
 
  const [procategory, setprocategory] = useState('');
// Fetching  Category
useEffect(() => {
  axios.get('api/product_category').then((res) => {
    setproduct_catResponse(res.data.product_cat);
    setLoading(false);
  })
}, [])

  //   const { register, handleSubmit,reset , formState: { errors } } = useForm({

  // });
 
  const [error_list, setError] = useState({});

  const [pro_cat,setPro_cat] =useState('');

  
  if(cat_id){
    useEffect(() => {
      axios.get(`/api/edit_prosubcategory/${cat_id}`).then(res => {
        if (res.data.status === 200) {
            setprocategory(res.data.category[0]);
            setPro_cat(res.data.category[0].cat_name);
            setLoading(false);
  
        } else if (res.data.status === 404) {
          swal('error', res.data.message, 'error');
          history.push('/admin/view_employee');
  
          // window.location.href='/admin/view_employee';
        }
      });
  
    }, [props.match.params.id])
    }
    function onChangepro_cat(value) {
      setPro_cat(value);
    }
    
    function onSearch(val) {
      setPro_cat(val);
    }
  const handelcat = (e) => {
    //const { name, value } = e.target;
    setprocategory({ ...procategory, [e.target.name]: e.target.value });
    // console.log(procategory);
    if(e.target.name=='subcat_name'){
      setError({subcat_name:''});
    }

  }
  
  useEffect(() => {
    //console.log(error_list);
    if (Object.keys(error_list).length === 0 && isSubmit) {
      //console.log(procategory);
    }
  }, [error_list]);

  const submitprocategory = (e) => {

    e.preventDefault();
    setError(validate(procategory));
    setIsSubmit(true);


    const formData = new FormData();


    formData.append('subcat_name', procategory.subcat_name);
    formData.append('cat_name', pro_cat);
    

      
      axios.post('api/update_prosubcategory/'+cat_id, formData).then(res => {
        if (res.data.status === 200) {


            // procategory.cat_name= '';
         
          swal("Success", res.data.message, "success");
            history.push('/admin/view_prosubcategory');
          setError([]);


        } else {
          // console.log(res);
          setError(res.data.validation_errors);
         // swal("Error", res.data.message, "error");
        }

      });
    


  }
  const validate = (values) => {
    const errors = {};
    // const regex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;
    if (!values.subcat_name) {
      errors.subcat_name = "product sub category name is required!";
    }
   
    return errors;
  };
  if(loading==true){
    return(<div>Loading</div>)
  }else{

  return (
    <div className='container-fluid paddingRight mt-4'>
      <Breadcrums name='Edit product sub category'/>
      <div className='row'>
        <div className='col-lg-6 col-md-6 col-sm-12 col-12 detailsbutton'>
          <div className='row'>
            <div className='col-lg-4 col-md-6 col-sm-12 col-12 buttoncolor buttonRL active'><a>Edit Product Sub Category</a></div>

          </div>

        </div>
      </div>
      <div className='row'>
        <div className='col-12 MainBoxC-details'>
          <div className='row'>
            <div className='col-12'>
              <form onSubmit={submitprocategory} encType="multipart/form-data" id="from_company">
                {/*  procategory Name */}
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Employee Name" className="labelStyle">Sub Category Name</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                      <input type="text"
                        name="subcat_name"
                        value={procategory.subcat_name}
                        onChange={handelcat}
                        placeholder='Enter category Name'
                        className="form-control" id="subcat_name" />

                    </div>
                    <span className='text-danger'>{error_list.subcat_name}</span>
                    {/* {errors.employee_name && <span className='text-danger'>{errors.employee_name?.message}</span>} */}
                  </div>


                </div>
                <div className="row mb-3">
                  <div className='col-xl-4 col-lg-4 col-md-4 col-sm-12 align-self-center'>
                    <label htmlFor="Employee Name" className="labelStyle">Category</label>
                  </div>
                  <div className="col-xl-8 col-lg-8 col-md-8 col-sm-12 InputBox InputHeight">
                    <div className="input-group">
                    <Select
                        showSearch
                        style={{ width: 200 }}
                        value={pro_cat}
                        placeholder="Search to Select"
                        optionFilterProp="children"
                        onChange={onChangepro_cat}
                        filterOption={(input, option) =>
                          option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                        }
                        filterSort={(optionA, optionB) =>
                          optionA.children.toLowerCase().localeCompare(optionB.children.toLowerCase())
                        }
                      >
                       { product_cat.map((item) => {
                            return( 
                                <Option key={item.id} value={item.id}  >{item.cat_name}</Option>
                           
                             );
                         })}
                                 


                                        
                                    </Select>
                    </div>
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

}

export default withRouter(Edit_prosubcategory);