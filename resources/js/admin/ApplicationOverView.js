import axios from 'axios';
import React, { useState, useEffect } from 'react';
import swal from 'sweetalert';
import { Link } from 'react-router-dom';
import Breadcrums from './Breadcrums';
// Import CSS
import "../assets/admin/css/Application.css";
// import Bar chat
import ApplicationOverViewTable from "../components/ApplicationOverViewTable";
function ApplicationOverView() {

    return (
        <div className='container-fluid'>

            <div className='applicationHeadingBox paddingTopBottom20'>
                <Breadcrums name='All Application'/>
                <div className='row'>
                
                    <div className='col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12'>
                        <h4 className='applicationHeadingText'>Overview</h4>
                    </div>
                    <div className='col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ApplicationButtonBox'>
                    <button className='ApplicationButton'>
                        <Link to='/admin/all_refferd_application' className=' text-decoration-none'>Refferd Application</Link>
                    </button>

                     <button className='ApplicationButton'> Massage</button>
                        {/* <!-- Button for assign modal --> */}
                        <button type="button" className="ApplicationButton" data-bs-toggle="modal" data-bs-target="#exampleModal">Assign</button>
                        <button className='ApplicationButton'>Export</button>
                    </div>
                </div>
            </div>
            <div className='row'>
                <div className='col-12'>
                    <ApplicationOverViewTable />
                    {/* <ApplicationRefferedTable /> */}
                </div>
            </div>
        </div>
    );

}

export default ApplicationOverView;