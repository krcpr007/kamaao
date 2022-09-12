import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import swal from 'sweetalert';
import RemoveOutlinedIcon from '@material-ui/icons/RemoveOutlined';
import FavoriteOutlinedIcon from '@material-ui/icons/FavoriteOutlined';
import SortOutlinedIcon from '@material-ui/icons/SortOutlined';
import AddBoxIcon from '@material-ui/icons/AddBox';
import {Spin} from 'antd';
import Breadcrums from './Breadcrums';
// import FontAwesomeIcon from '@fortawesome/free-solid-svg-icons';

function View_prosubcategory() {
 
  const [loading , setLoading ] =useState(true);
  const [ procategorylist , setprocategory] =useState({
    cat_name:''
  });
  function showlist(){
    axios.get('api/view_prosubcategory').then(res=>{

      if(res.status === 200){
        setprocategory(res.data.prosubcategory)
      }

      setLoading(false);

    });
  }
  useEffect(() =>{
    showlist();
  },[]);

   

  function prosubcategory_delete(id){
    axios.delete('api/delete_prosubcategory/'+id).then((res)=>{
      if(res.data.status === 204){
        
        swal('warning',res.data.message,'warning');
        showlist();
      } 
  })

  } 
  function enable_prosubcategory(id){
      
   axios.post('api/enable_prosubcategory/'+id).then((res)=>{
     if(res.data.status === 200){
      
       
       swal('success',res.data.message,'success');
       showlist();
       //location.reload();
     }

 })

 }  
 
 function disable_prosubcategory(id){
     
    axios.post('api/disable_prosubcategory/'+id).then((res)=>{
      if(res.data.status === 200){
        
       
        swal('success',res.data.message,'success');
        showlist();
        
      }
 
  })
 
  } 
  if(loading==true){
    return(<div><Spin /></div>)
  }else{
  var viewdata='';
  
  viewdata=
  procategorylist.map((item, index) => {
    return(
        
          <tr key={item.id}>
            <th scope="row">{index + 1}</th>
            <td>{item.subcat_name}</td>
            <td>{item.cat_name}</td>
            
          
            <td><Link className='btn btn-success btn-sm' to={`edit_prosubcategory/${item.id}`} >Edit</Link> {(item.status=='enable')?<span className='btn btn-outline-warning btn-sm' onClick={()=>disable_prosubcategory(item.id)}>Disable</span>:<span className='btn btn-outline-primary  btn-sm' onClick={()=>enable_prosubcategory(item.id)}>Enable</span>} <span className='btn btn-danger btn-sm' onClick={()=>prosubcategory_delete(item.id)}>Delete</span></td>
          </tr>
         
      
      );
  });
  }
  return (
    <div className='row mt-2'>
      <div className='col-12 paddingR'>
        <Breadcrums name='Product > Sub Category ' />
        <div className='row'>
          <div className='col-12 cardHeader'>
            <a><SortOutlinedIcon className='ThreeLine' /></a>
            <a><p className='SortText marginT15'>Sort by</p></a>
            <div className='FilterImage'></div>
            <a><p className='SortText marginT20'>Filter</p></a>
            <Link to='/admin/add_prosubcategory'><AddBoxIcon className='PlusIcon' /></Link>
            {/* <FontAwesomeIcon icon='fa-solid fa-filter' /> */}
          </div>
        </div>
        {/* first Row */}
        <div className='row card'>
        <table className="table">
        <thead className="thead-dark">
          <tr>
            <th scope="col">#</th>
            <th scope="col">sub category Name</th>
            <th scope="col">category Name</th>
            
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
        {viewdata}
        </tbody>
      </table>
          
        </div>
    

      </div>
    </div>

  );
}


export default View_prosubcategory;
