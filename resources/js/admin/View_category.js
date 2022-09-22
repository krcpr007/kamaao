import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import swal from 'sweetalert';
import RemoveOutlinedIcon from '@material-ui/icons/RemoveOutlined';
import FavoriteOutlinedIcon from '@material-ui/icons/FavoriteOutlined';
import SortOutlinedIcon from '@material-ui/icons/SortOutlined';
import AddBoxIcon from '@material-ui/icons/AddBox';
import {Spin} from 'antd';
import Breadcrums from "./Breadcrums";
// import FontAwesomeIcon from '@fortawesome/free-solid-svg-icons';

function View_category() {
 
  const [loading , setLoading ] =useState(true);
  const [ categorylist , setCategory] =useState({
    cat_name:''
  });
  function showlist(){
    axios.get('api/view_category').then(res=>{

      if(res.status === 200){
        setCategory(res.data.category)
      }

      setLoading(false);

    });
  }
  useEffect(() =>{
    showlist();
  },[]);

   
    function category_delete(id) 
    {
      console.log(id)
      swal({
        title: "Are you sure?",
        text: "You want to delete this user?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(willDelete => {
        if (willDelete) 
        {
          axios.delete('api/delete_category/' + id)
          .then(res => {  
            swal({
              title: "Done!",
              text: "Record Deleted ",
              icon: "success",
              timer: 1500,
              button: false
            })
            showlist();
          });
        }
      });
    }
   
 

function toggle_status(id, newStatus) {

  axios.post('api/jobs/category/update_status',  {
    id:id,
    new_status:newStatus
  }).then((res) => {
    if (res.data.status === 200) {
      showlist();
      swal('success', res.data.message, 'success');
    }
  })
}

 
  if(loading==true){
    return(<div><Spin /></div>)
  }else{
  var viewdata='';
  
  viewdata=
  categorylist.map((item, index) => {
    return(
        
          <tr key={item.id}>
            <th scope="row">{index + 1}</th>
            <td>{item.cat_name}</td>
            <td><Link className='btn btn-success btn-sm' to={`edit_category/${item.id}`} >Edit</Link> {(item.is_enabled=='1')?<span className='btn btn-outline-warning btn-sm' onClick={()=>toggle_status(item.id, 0)}>Disable</span>:<span className='btn btn-outline-primary  btn-sm' onClick={()=>toggle_status(item.id,1)}>Enable</span>} <span className='btn btn-danger btn-sm' onClick={()=>category_delete(item.id)}>Delete</span></td>
          </tr>
         
      
      );
  });
  }
  return (
    <div className='row mt-2'>
      <div className='col-12 paddingR'>
      <Breadcrums name="View Category"/>
        <div className='row'>
          <div className='col-12 cardHeader'>
            <a><SortOutlinedIcon className='ThreeLine' /></a>
            <a><p className='SortText marginT15'>Sort by</p></a>
            <div className='FilterImage'></div>
            <a><p className='SortText marginT20'>Filter</p></a>
            <Link to='/admin/add_category'><AddBoxIcon className='PlusIcon' /></Link>
            {/* <FontAwesomeIcon icon='fa-solid fa-filter' /> */}
          </div>
        </div>
        {/* first Row */}
        <div className='row card'>
        <table className="table">
        <thead className="thead-dark">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Category Name</th>
            
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


export default View_category;
