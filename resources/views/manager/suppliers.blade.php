@extends('layouts.manager')
@section('contentWrapper')
        <div class="container-fluid size10">
                <!-- Breadcrumbs-->
                <ol class="breadcrumb size11 ">
                    <li class="breadcrumb-item active">     
                    <a href="#" class="text-muted" id="title">
                    <i class="fa fa-users" ></i>
                    <span class="nav-link-text">Suppliers</span> </a>
                    </li>                
                </ol>

                <div class="row text-muted ">
                    <div class="col-md-1 "></div>
                    <div class="col-md-3 ">
                        <p><i class="fa fa-user-plus"></i> Add Supplier</p> 
                            <form method="POST" name="addSupllier" id="addSupllier" action="addSupplier">
                                {{csrf_field()}}
                                <div class="form-group">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text size10"><i class="far fa-building"></i> </span>
                                                </div>
                                                <input type="text" required class="form-control form-control-sm size10" name="companyName" placeholder="Company Name" aria-label="Company Name">                
                                            </div>
                                </div>
                                <div class="form-group">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text size10"><i class="far fa-user"></i></span>
                                                </div>
                                                <input type="text" required class="form-control form-control-sm size10" name="contactName" placeholder="Contact Name" aria-label="Contact Name">                
                                            </div>
                                </div>
                                <div class="form-group">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text size10"><i class="fa fa-phone-volume"></i></span>
                                                </div>
                                                <input type="number" required class="form-control form-control-sm size10" name="mobileNumber" placeholder="Mobile Number" aria-label="Mobile Number">                
                                            </div>
                                </div>
                                <div class="form-group">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text size10"><i class="fa fa-at"></i></span>
                                                </div>
                                                <input type="email" required class="form-control form-control-sm size10" name="mail" placeholder="E-Mail" aria-label="E-Mail">                
                                            </div>
                                </div>
                                <div class="form-group">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text size10"><i class="fa fa-address-card"></i></span>
                                                </div>
                                                <input type="text" required class="form-control form-control-sm size10" name="address" placeholder="Address" aria-label="Address">                
                                            </div>
                                </div>
                                <div class="form-group">
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text size10"><i class="fa fa-link"></i></span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm size10" name="website" placeholder="www.example.com (Website URL Optional)" aria-label="website url">                
                                            </div>
                                </div>
                                <div class="form-group">
                                        <button type="submit"  class="btn btn-sm btn-outline-info float-right size9" id="add-supplier_btn"><i class="fa fa-user-check"></i> Add Supplier</button>                                            
                                </div>
                            </form>
                    </div>
                    <div class="col-md-7 style-10 cusH1">
                        <p><i class="fa fa-users"></i> Suppliers</p> 
                        @if(count($suppliers)>0)
                            
                            <div class="accordion" id="accordionExample">
                                @foreach($suppliers as $supplier)
                                 
                                    <div class="card">
                                        <a href="#collapseOne{{$supplier->sup_id}}" data-toggle="collapse" class="">
                                            <div class="card-header size11" id="headingOne">
                                                    {{$supplier->sup_company_name}}
                                            </div>
                                        </a>
                                        <div id="collapseOne{{$supplier->sup_id}}" class="collapse" aria-labelledby="headingOne" >
                                            <div class="card-body size11">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">                                                    
                                                        <span class="badge badge-primary badge-pill"><i class="far fa-user"></i> Contact Name :</span>
                                                        {{$supplier->sup_contact_name}}
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">                                                    
                                                        <span class="badge badge-primary badge-pill"><i class="fa fa-phone-volume"></i> Mobile Number :</span>
                                                        {{$supplier->sup_mobile}}
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">                                                    
                                                        <span class="badge badge-primary badge-pill"><i class="fa fa-at"></i> E-mail :</span>
                                                        {{$supplier->sup_mail}}
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">                                                    
                                                        <span class="badge badge-primary badge-pill"><i class="fa fa-address-card"></i> Address :</span>
                                                        {{$supplier->sup_address}}
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">                                                    
                                                        <span class="badge badge-primary badge-pill"><i class="fa fa-link"></i> Website :</span>
                                                        <a href="http://{{$supplier->sup_website}}" target="new" >{{$supplier->sup_website}}</a>
                                                    </li>
                                                                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="alert alert-dismissible alert-light">                                
                                <h6 class="alert-heading">No Suppliers Available!</h6>
                                <p class="mb-0">Kindly use the form to add new suppliers.</p>
                            </div>    
                        @endif
                    </div>
                </div>
                
        </div>

        <?php 
        // echo( $suppliers) ?>
                
@endsection

