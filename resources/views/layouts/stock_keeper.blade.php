@extends('layouts.portal')

@section('nav')
<ul class="navbar-nav navbar-sidenav  " id="exampleAccordion">
                        <hr>
                        <li class="nav-item text-center" data-toggle="tooltip" data-placement="right" title='{{Auth::user()->name}}'>
                                <a class="nav-link size11 id" href="#">
                                <i class="fas fa-user-circle text-muted"></i>
                                <span class="nav-link-text"> {{Auth::user()->name}} </span>
                                </a>
                        </li> 
                        
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Stock Out">
                                <a class="nav-link menu " href="stockout"  >
                                <i class="fas fa-shopping-cart" ></i>
                                <span class="nav-link-text">Stock Out</span>
                                </a>
                        </li> 
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Stock Transfer">
                                <a class="nav-link menu" href="stocktransfer" >
                                <i class="fas fa-exchange-alt"></i>
                                <span class="nav-link-text">Stock Transfer</span>                            
                                </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Confirm Purchase">
                                <a class="nav-link menu" href="confirmpurchase" >
                                <i class="fa fa-truck"></i>
                                <span class="nav-link-text">Stock Delivery</span>                            
                                </a>
                        </li>                                              
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="History">
                                <a class="nav-link menu" href="history" >
                                <i class="fa fa-history"></i>
                                <span class="nav-link-text">History</span>                             
                                </a>
                        </li> 
                        {{-- <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Report">
                                <a class="nav-link menu" href="#" id="">
                                <i class="fas fa-file-contract"></i>
                                <span class="nav-link-text">Report</span>                             
                                </a>
                        </li>                        --}}
                </ul>                

                <ul class="navbar-nav ml-auto">

                        <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-envelope"></i>
                        <span class="d-lg-none">Messages
                        <span class="badge badge-pill badge-primary">12 New</span>
                        </span>
                        <span class="indicator text-primary d-none d-lg-block">
                        <i class="fa fa-fw fa-circle"></i>
                        </span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">New Messages:</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                <strong>David Miller</strong>
                                <span class="small float-right text-muted">11:21 AM</span>
                                <div class="dropdown-message small">Hey there! This new version of SB Admin is pretty awesome! These messages clip off when they reach the end of the box so they don't overflow over to the sides!</div>
                                </a>                        
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small" href="#">View all messages</a>
                        </div>
                        </li>

                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-bell"></i>
                        <span class="d-lg-none">Alerts
                        <span class="badge badge-pill badge-warning">6 New</span>
                        </span>
                        <span class="indicator text-warning d-none d-lg-block">
                        <i class="fa fa-fw fa-circle"></i>
                        </span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="alertsDropdown">
                        <h6 class="dropdown-header">New Alerts:</h6>
                        <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                <span class="text-success">
                                <strong><i class="fa fa-long-arrow-up fa-fw"></i>Status Update</strong>
                                </span>
                                <span class="small float-right text-muted">11:21 AM</span>
                                <div class="dropdown-message small">This is an automated server response message. All systems are online.</div>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                <span class="text-danger">
                                <strong><i class="fa fa-long-arrow-down fa-fw"></i>Status Update</strong>
                                </span>
                                <span class="small float-right text-muted">11:21 AM</span>
                                <div class="dropdown-message small">This is an automated server response message. All systems are online.</div>
                                </a>                  
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small" href="#">View all alerts</a>
                        </div>
                        </li> -->
                        
                        {{-- <li class="nav-item">
                        <form class="form-inline my-2 my-lg-0 mr-lg-2" id="ticketSearch">
                        <div class="input-group ">
                        <input class="form-control form-control-sm size10" type="text" placeholder="Search Item">
                        <span class="input-group-append">
                                <button class="btn btn-outline-primary size10" id="ticketSearchBtn" type="button">
                                <i class="fa fa-search"></i>
                                </button>
                        </span>
                        </div>
                        </form>
                        </li> --}}
                        
                        <li class="nav-item">
                        <a class="nav-link size12 text-danger" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-sign-out-alt"></i> Logout</a>
                        </li>
                </ul>
@endsection




