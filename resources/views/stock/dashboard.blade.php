@extends('layouts.stock_keeper')

@section('contentWrapper')
    <div class="container-fluid size10">
                    <!-- Breadcrumbs-->
                    <ol class="breadcrumb size11 ">
                        <li class="breadcrumb-item active">
                        <a href="#" class="text-muted" id="title">
                        <i class="fas  fa-home" ></i>
                        <span class="nav-link-text">Welcome</span> </a>
                        </li>                
                    </ol>
                    
                    <div class="col-md-12 text-muted" >
                        <div class="row" >
                            <div class="col-md-4 offset-md-3 align-self-center text-center">
                                <span class="text-muted  "><i class="far fa-user-circle fa-10x"></i></span>
                                <h6>Welcome  </h6>
                                <h5>{{ Auth::user()->name }} </h5>
                            </div>       
                        </div>
                    </div>     
    </div>
@endsection
