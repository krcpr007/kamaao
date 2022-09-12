import axios from "axios";
import React,{useState,useEffect,memo }from "react";
import swal from "sweetalert";
import { useHistory } from 'react-router-dom';
import {
    BrowserRouter as Router,
    Switch,
    Route,
    Link
} from "react-router-dom";
import SearchIcon from '@material-ui/icons/Search';
import ChatBubbleOutlineOutlinedIcon from '@material-ui/icons/ChatBubbleOutlineOutlined';
import NotificationsNoneOutlinedIcon from '@material-ui/icons/NotificationsNoneOutlined';
// import ReorderIcon from '@material-ui/icons/Reorder';
import LineWeightOutlinedIcon from '@material-ui/icons/LineWeightOutlined';
// import HiOutlineMenuAlt1 from '@material-ui/icons/HiOutlineMenuAlt1';
import { HiOutlineMenuAlt1 } from "react-icons/hi";
import "../../assets/admin/css/Navbar.css";

const clientId = "181412757037-coge70cv88lll7chf8q990pjoeaikjos.apps.googleusercontent.com";

const Navbar = (props) => {
    const history = useHistory();
    const logoutSubmit = (e) => {
        e.preventDefault();

        axios.post('api/logout').then(res => {

            if (res.data.status === 200) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('auth_name');

                swal("Success", res.data.message, "success");


                history.push('/admin');
                location.reload();



            }

        });
    }
   
    const onSignoutSuccess = () => {
        console.clear();

    };

    var AuthButtons = '';

    if (localStorage.getItem('auth_token')) {
        AuthButtons = (
            <li><a className="dropdown-item" onClick={logoutSubmit}>
                Logout
            </a></li>
        );
    }
    return (
        <div>

            <nav className="sb-topnav navbar navbar-expand navbarBackground">
                
                <button className="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 margin100" id="sidebarToggle" href="#">
                    <div className="navbarButtonBox">
                        {/* <ReorderIcon className="NavButton" /> */}
                        <HiOutlineMenuAlt1 className="NavButton" />
                    </div>
                </button>
                <button className="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 margin100" id="sidebarToggleTab" href="#">
                    <div className="navbarButtonBox">
                        {/* <ReorderIcon className="NavButton" /> */}
                        <HiOutlineMenuAlt1 className="NavButton" />
                    </div>
                </button>
                <div className="TitleTextBox">
                    {/* <p className="titleText">Dashboard</p> */}
                </div>
                <form className="FormPosition d-md-inline-block form-inlin">
                    <div className="input-group NavSearchBox">
                        <SearchIcon className="SearchIcon" />
                        <input className="form-control" type="text" placeholder="Search by jobs, company, skills....." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    </div>
                </form>
                {/* msg box */}
                <div className="HeaderMsgBox">
                    <ChatBubbleOutlineOutlinedIcon className="MsgIcon" />
                </div>
                {/* notification box */}
                <div className="NotificationBox">
                    <NotificationsNoneOutlinedIcon className="NotificationIcon" />
                </div>
                <div className="nameBox">
                         <p className="nameText">{props.name}</p>
                    <p className="AdminText">{(props.user_type==2)?'Sub Admin':(props.user_type==3)?'Sales':(props.user_type==4)?'Back Office':(props.user_type==5)?'Tele caller':'Admin'}</p>

                </div>
                <ul className="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                    <li className="nav-item dropdown">
                        <Link className="nav-link" id="navbarDropdown" to="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div className="ProfileImageBox">
                                <div className="ProfileImage" ></div>
                            </div>
                        </Link>
                        <ul className="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><Link className="dropdown-item" to="/admin/profile">Profile</Link></li>
                            <li><Link className="dropdown-item" to="#!">Activity Log</Link></li>
                            <li className="NotificationMessage"><Link className="dropdown-item" to="#!">Notification</Link></li>
                            <li className="NotificationMessage"><Link className="dropdown-item" to="#!">Message</Link></li>
                            <li><hr className="dropdown-divider" /></li>
                            {AuthButtons}
                        </ul>
                    </li>
                </ul>
            </nav>

        </div>

    );

}

export default memo(Navbar);