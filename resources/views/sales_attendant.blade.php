<!DOCTYPE html>
<html ng-app="salesPortal">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sales Attendant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/saleCustom.css")}} />
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/animate.css")}} />
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/bootstrap.min.css")}} />
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/select2.css")}} /> 
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/jquery-ui.min.css")}} /> 
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-177459834-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-177459834-1');
    </script>
</head>
<body>
    <div class="container-fluid" ng-controller="sales">
        <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top" >
            <a class="navbar-brand text-center" href="#" >
               <div style="width:130px; height:50px"> 
                   <img src={{asset("img/logo.svg")}}  style="width:50px; height:50px"  class="rounded-circle img-thumbnail mb-2" >
                 </div> 
            </a> 
            <span class="size11 text-muted"></span>          
             
                <!-- <div class="nav navbar-nav ml-auto">
                   <span class="badge"> </span>  
                </div> -->
                <ul class="nav navbar-nav ml-auto text-muted ">
                    <li class="size12"><i class="far fa-user "></i><span>  {{ Auth::user()->name }} </span></li>
                   <a href="{{route('logout')}}" style="color:#dc3545" > <li class="pad-space size13" data-toggle="tooltip" title="Logout" data-placement="bottom"> <i class="fa fa-sign-out-alt"></i></li></a>
                        <form id="sales_logout" method="POST" action="{{route('logout')}}" >
                            @csrf
                        </form>
                </ul>    
        </nav><br> 

        <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link active show size11" data-toggle="tab" href="#sales"><i class="fal fa-print"></i> Sales</a>
            </li>
            <li class="nav-item">
              <a class="nav-link size11" data-toggle="tab" href="#history" ng-click="salesHistory()"><i class="fal fa-history"></i> History</a>
            </li>            
          </ul><br>
          <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active show" id="sales">
                  <div class="row" >
                          
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 " >                    
                                <div class="card">
                                    <div class="card-header text-muted size12">
                                        <i class="fal fa-store"></i> Stock
                                    </div>
                                    <div class="card-body table-responsive customHeight style-10"  id="stockItems">
                                            <select class="form-control form-control-sm custom-select size11" ng-disabled="itemAdd.length !=0" ng-options="x.qty_desc for x in selectQtyType" ng-model="selectQty" ng-change="itemQtyPick()" >
                                                    <option value="" >SELECT UNIT</option>
                                            </select>
                                        <table class="table table-hover text-justify customHeight style-10" id="stockItemsTable">
                                            <thead class="thead-default size12 text-muted">
                                                <tr>
                                                    <th class="text-center">
                                                        
                                                        <div class="input-group size10">
                                                                <input type="text" class="form-control form-control-sm size10" ng-model="searchData" aria-label="" ng-change="searchItem(searchData)" placeholder="Search Item...">
                                                                {{-- <div class="input-group-append">
                                                                  <span class="input-group-text size10"><i class="fa fa-search"></i> </span>
                                                                </div> --}}
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                                <tbody class="size10">
                                                        <tr ng-repeat="item in items" ng-click="pickItem($index)" class="finger">
                                                            <td> @{{item.item_name}}  <b class="text-danger">    ( @{{ item.quantity | number}} )</b> </td>
                                                        </tr>                                     
                                                </tbody>
                                        </table>
                                        <div class="alert alert-light" ng-show="items.length == 0">
                                                <span class="alert-heading text-muted text-center size10"><i class="fa fa-box"></i> Item Not Available!</span>
                                        </div>
                                    </div>                            
                                </div>
                                
                        </div>
                                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header text-muted size12">
                                            <i class="fal fa-user-alt"> </i> Customer Information
                                        </div>
                                        <div class="card-body">                                                                    
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="input-group btn-sm" style="font-size:0.7rem">                                                                
                                                                <select ui-select2  style="width:100%;" ng-model="customer" data-placeholder='Choose Customer' ng-options="x.cus_name for x in customers" ng-change="pickCustomer()" ng-disabled="itemAdd.length !=0" >
                                                                        <option value=""></option>
                                                                </select>                                                
                                                        </div>
                                                    </div> 
                                                </div>                                 
                                                <div class="row no-gutters">
                                                        <div class="col-md-6">
                                                            <div  class="input-group btn-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text size11 alert-danger text-muted">Payment</span>
                                                                </div>
                                                                <select  class="form-control form-control-sm custom-select btn-sm size11" ng-class="creditValid" ng-disabled="itemAdd.length !=0" ng-model="payment" ng-options="x.payment_desc for x in payments" ng-class="payValid">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group btn-sm">                                                            
                                                                    <a id="" class="form-control btn-sm size10 btn btn-outline-primary " href="#"  data-toggle="modal" data-target=".bd-example-modal-sm" ><i class="fas fa-user-plus"></i> Add Customer</a>                                                    
                                                            </div> 
                                                        </div>                                                    
                                                </div>                                 
                                        </div>                       
                                    </div>
                                </div>
                            </div><br>                    
        
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header text-muted size12">
                                            <i class="fal fa-info-circle"></i>  Item Information
                                        </div>
                                        <div class="card-body">                               
                                            <div class="row no-gutters">
                                                <div class="col-md-6">                                         
                                                        <div  class="input-group btn-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text size11 alert-danger text-muted">Store Qty</span>
                                                            </div>
                                                            <input  class="form-control btn-sm size11" readonly type="text" ng-model="storeQty"  >
                                                        </div>
                                                        <div  class="input-group btn-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text size11 alert-info text-muted">Price</span>
                                                                </div>
                                                                <input  class="form-control btn-sm size11"  readonly type="text"  ng-model="itemPrice" placeholder="">
                                                        </div>
                                                        <div  class="input-group btn-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text size11 alert-success text-muted">Amount</span>
                                                                </div>
                                                                <input  class="form-control btn-sm size11"  readonly type="text"  ng-model="itemAmount"  placeholder="" >
                                                        </div>                                              
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div  class="input-group btn-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text size11 text-muted">Name</span>
                                                            </div>
                                                            <input  class="form-control btn-sm size11" readonly type="text" ng-model="itemName"  placeholder="">
                                                    </div> 
                                                    <div  class="input-group btn-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text size11 alert-success text-muted">Sell Qty</span>
                                                            </div>
                                                            <input  class="form-control btn-sm size11" ng-model="sellQty" ng-change="tieredPriceFn()"  type="number"  placeholder="">
                                                    </div>
                                                        
                                                    
                                                </div>
        
                                            </div>                                        
                                        </div>   <!--info card body end !-->                         
                                    </div>
                                </div>
                            </div> <br>  <!-- End row item info!-->
                            
                            <div class="row">                        
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-header text-muted size12">
                                            <i class="fal fa-money-bill-alt"></i> Discount Information
                                        </div>
                                        <div class="card-body">                                    
                                                <div class="d-flex justify-content-center">
                                                    <div class="col-md-5">                                     
                                                            <div class="input-group mb-2 mr-sm-2">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text size11 alert-default text-muted">Discount</span>
                                                                    </div>
                                                                    <input type="text"  class="form-control form-control-sm btn-sm size11" ng-model="itemDiscount" readonly    placeholder="">
                                                            </div>
                                                    </div>
                                                            
                                                    <div class="col-md-4 ">
                                                            <div  class="input-group mb-2 mr-sm-2">
                                                                <button  class="btn btn-outline-secondary btn-sm size10"  ng-model="addItemBtn" ng-click="addItem()" ng-disabled="storeQty <= 0 || storeQty ==null || itemPriceVal <=0 || itemPriceVal == null || sellQty <= 0 || sellQty == null || sellQty > storeQty || itemAmount == null || itemAmount <= 0  || payment == null" > <i class="fa fa-plus" aria-hidden="true"></i> Add Item</button>
                                                            </div>
                                                    </div>
                                                </div>
                                        </div>                               
                                    </div>
                                </div>                        
                            </div>
        
                        </div>                           
                        
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header text-muted size12">
                                                <i class="fal fa-cart-plus"></i> Items Lists
                                            </div>                        
                                            <div class="card-body table-responsive style-10"  id="itemList" style="max-height:242px" >
                                                
                                                    <table class="table table-hover "  width="100%" >
                                                        <thead class="thead-default size10 text-muted">
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Unit</th>                                            
                                                                <th>Qty</th> 
                                                                <th>Price</th> 
                                                                <th>Amount</th>
                                                                <th>Discount</th>                                      
                                                                <th></th>                                      
                                                            </tr>
                                                        </thead>
                                                        <tbody class="size10">
                                                            <tr ng-repeat="item in itemAdd" class="delbtn finger" ng-click="editPickedItem($index)" >
                                                                <td>@{{item.name}} </td>
                                                                <td>@{{item.unit}} </td>
                                                                <td>@{{item.qty}} </td>
                                                                <td>@{{item.price}} </td>
                                                                <td>@{{item.amount}} </td>
                                                                <td>@{{item.discount | currency:"":0}} </td>
                                                                <td > <a class="text-danger delbtn" href="#" role="button" ng-click="removeItem($index)" title="Remove"><i class="fas fa-minus size12"></i> </a> </td>
                                                            </tr>                                                                                                                     
                                                        </tbody>
                                                    </table>
                                                    <div class="alert alert-light text-muted small text-center" ng-hide="itemAdd.length != 0">
                                                        <h6 class="alert-heading">Empty Items List</h6>
                                                        <p class="mb-0 small">Kindly add items.</p>
                                                    </div>
                                            </div>
                                        </div>     
                                    </div>
                                </div> <br>  
                                
                                <div class="row">                        
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="card">
                                            <div class="card-header text-muted size12">
                                                <i class="fal fa-handshake"></i> Transaction
                                            </div>                        
                                            <div class="card-body"  >
                                                <div class="d-flex justify-content-between">
                                                        <div class="input-group mb-2 mr-sm-2">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text size11 alert-danger text-muted font-weight-bold">Total Amount:</span>
                                                                </div>
                                                                <input type="text" class="form-control form-control-sm btn-sm size11" ng-class="[cashValid, creditValid]" readonly  ng-model="totalAmount"  placeholder="">
                                                        </div>
                                                        <div class="input-group mb-2 mr-sm-2">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text size11 alert-warning text-muted font-weight-bold">Total Discount:</span>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm btn-sm size11" ng-model="totalDiscount" readonly  placeholder="">
                                                        </div>
                                                        <div class="input-group mb-2 mr-sm-2">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text size11 alert-success text-muted font-weight-bold">Available Credit:</span>
                                                                </div>
                                                                <input type="text" class="form-control form-control-sm btn-sm size11" ng-model="credit" ng-class="creditValid" readonly  placeholder="">
                                                        </div>
                                                        {{-- <div class="input-group mb-2 mr-sm-2">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text size11 alert-primary text-muted">Cash Paid:</span>
                                                                </div>
                                                                <input type="number" class="form-control form-control-sm btn-sm size11" ng-class="cashValid" ng-model="cashPaid" ng-disabled="payment.payment_desc != 'CASH' "  placeholder="">
                                                        </div>                                                                                                               --}}
                                                </div>
                                            </div>
                                        </div>          
                                    </div>                        
                                </div><br>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="d-flex justify-content-end">
                                                <button   class="btn btn-outline-success btn-sm size10 mr-2" ng-disabled="processingOrder || itemAdd.length==0"   ng-click="saveTransaction()"> <i class="far fa-save"></i> Save Transaction</button>
                                                <a   class="btn btn-outline-danger btn-sm size10" href="#" role="button" ng-click="clearItems()"> <i class="fas fa-eraser"></i> Clear</a>
                                        </div>
                                </div>
                                </div>                  
                        </div>
                                      
                  </div>
            </div>
            <div class="tab-pane fade " id="history">
                <div class="container">
                    <div class="col-md-8 offset-md-2 size10">
                        <div class="form-group col-md-3 pl-0">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                                </div>
                            <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" ng-change="salesHistory()" class="form-control form-control-sm size10" ng-model="salesDate" aria-label="">
                                <div class="input-group-append">
                                         <span class="input-group-text text-muted size10"> <a href="#" ng-click="salesHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="alert noPaddingAlert alert-primary d-flex justify-content-between">
                                <div class="p-2 bd-highlight">@{{dateBanner | date :'yyyy-MM-dd'}} </div>
                                <div class="p-2 bd-highlight">NGN @{{totalAmountDaily | currency:'':2}} </div>
                        </div>
                        <div class="alert alert-primary text-center text-muted" ng-show="historyData.length == 0">
                            <h6 class="alert-heading text-muted"><i class="fa fa-book"></i> No Records Found!</h6>
                            <p class="mb-0">.</p>
                        </div> 
                        <div class="accordion customHeight2 style-10" id="accordionExample">
                                <div class="list-group" ng-repeat="x in historyData">
                                        <a href="#" data-toggle="collapse" class="list-group-item d-flex justify-content-between align-items-center" data-target="#collapse@{{x.order_id}}" aria-expanded="true" aria-controls="collapse@{{x.order_id}}">
                                                <div class="form-group">
                                                    <label for="formGroupExampleInput" ><i class="far fa-user"></i> @{{x.cus.cus_name}}</label>
                                                            <div id="formGroupExampleInput">@{{x.order_no}} | @{{x.payment.payment_desc +' '+ x.order_date +' '+ x.order_time}}</div>
                                                </div>                                                     
                                                <span class="badge badge-primary badge-pill">@{{x.order_total_amount | currency:'':2}}</span>
                                        </a> 
                                        <div id="collapse@{{x.order_id}}" class="collapse" >
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-1"></div>
                                                        <div class="col-md-8  size10 text-muted">
                                                            <table class="table table-hover table-sm text-justify">
                                                                <thead class="table-light">
                                                                    <tr >
                                                                        <th >Item</th>
                                                                        <th >Qty (@{{x.qty.qty_desc}})</th>
                                                                        <th >Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr ng-repeat="y in x.items">
                                                                        <td>@{{y.item.item_name }}</td>
                                                                        <td>@{{y.quantity | number}} </td>
                                                                        <td>@{{y.amount | currency:'':2}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table> 
                                                        </div>
                                                        <div class="col-md-1"></div>
                                                        <div class="col-md-2">
                                                            <a ng-show="x.receipt_printed == 0" href="#" ng-click="reprintReceipt(x.order_id)" class="btn btn-sm"><i class="far fa-print"></i> </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                        
                                </div>                                   
                        </div>
                    </div>
                </div>
            </div>            
          </div>

        <div class="row">
            <nav class="navbar fixed-bottom navbar-expand navbar-light bg-light justify-content-between">                
                    <div class="col-md-6 offset-md-5 size12 text-muted">
                            Retail Office &copy; <?php echo date('Y') ?>                   
                    </div>                      
            </nav>
        </div>  
    </div>

 <!--Notification Modal Templates-->
 <div id="modalNotify" class="modal modal-message modal-info fade " style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header ">
                           <div class="col-md-6 offset-md-3"><i class="far fa-bell"></i></div>
                </div>
                <div class="modal-body size11" id="modalMsg"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info form-control btn-sm size9" data-dismiss="modal"><i class="far fa-thumbs-up"></i> OK</button>
                </div>
            </div> <!-- / .modal-content -->
        </div> <!-- / .modal-dialog -->
</div>
<!--End Notification Modal Templates-->

<!--Processing Modal Templates-->
<div id="modalP" class="modal modal-message modal-info fade " style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header "></div>
            <div class="modal-body size11" >
                <div class="alert alert-light" id="modalMsgP"></div>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<!--End Notification Modal Templates-->


<!-- NEW CUSTOMER MODAL -->

<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="card">
                <div class="card-header  text-muted size12">Add New Customer</div>
                <div class="card-body">
                    <form method="POST" action="{{url('savecustomer')}}" >
                            {{csrf_field()}}
                        <div class="input-group btn-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text "> <i class="fa fa-user-alt size10"></i> </span>
                            </div>
                            <input type="text" name="cusName" required class="form-control btn-sm size10"   placeholder="Customer Name">                                            
                        </div>
                        <div class="input-group btn-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text "> <i class="fas fa-phone size10"></i> </span>
                            </div>
                            <input name="cusMobile" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                            type = "number"
                            maxlength = "11" required class="form-control btn-sm size10"  placeholder="Phone Number">                                            
                        </div>
                        <div class="input-group btn-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text "> <i class="fas fa-home size10"></i> </span>
                            </div>
                            <input type="text" name="cusAddress" required class="form-control btn-sm size10"  placeholder="Home Address">                                            
                        </div>
                        <div class="input-group btn-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text "> <i class="fa fa-envelope size10" aria-hidden="true"></i> </span>
                            </div>
                            <input type="email" name="cusMail" class="form-control btn-sm size10"  placeholder="E-mail (Optional)">                                            
                        </div>
                        <div class="input-group btn-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text "> <i class="fas fa-filter size10"></i> </span>
                            </div>
                            <select name="cusPayment" required class="form-control custom-select size10">
                                <option value="">Customer Payment</option>
                                @foreach($payment as $pay)
                                    <option value="{{$pay->payment_id}}">{{$pay->payment_desc}} </option>
                                @endforeach
                            </select>                                          
                        </div>
                        <div class="form-group btn-sm">
                            <button type="submit" class="btn btn-outline-primary size10 float-right btn-sm size10" ><i class="fas fa-user-plus"></i> Add Customer</button> 
                        <div>
                    </form>
                </div>
            </div>            
        </div>
    </div>
</div>

    <script src={{asset("/js/jquery-1.12.3.js")}} ></script>
    {{-- <script src={{asset("/js/jquery-ui.min.js")}} ></script> --}}
    <script src={{asset("/js/lodash.js")}} ></script>
    <script src={{asset("/js/angular.min.js")}} ></script> 
    <script src={{asset("/js/angular-animate.min.js")}} ></script> 
    <script src={{asset("/js/ui-bootstrap-tpls-2.5.0.js")}} ></script> 
    {{-- <script src={{asset("/js/date.js")}} ></script>  --}}
    <script  src={{asset("/js/popper.js")}} ></script> 
    <script src={{asset("/js/bootstrap.min.js")}} ></script> 
    <script src={{asset("/js/select2.js")}} ></script> 
    <script src={{asset("/js/angular-ui-select2.js")}} ></script> 
    <script src={{asset("/js/fontawesome-all.min.js")}} ></script>
    <script src={{asset("/js/rsvp-3.1.0.min.js")}} ></script> 
    <script src={{asset("/js/sha-256.min.js")}} ></script> 
    <script src={{asset("/js/qz-tray.js")}} ></script> 
    <script src={{asset("/js/jsrsasign-qz-all-min.js")}} ></script> 
    <script src={{asset("/js/qz-cert.js")}} ></script> 
    <script  src={{asset("/js/ngapp.js")}} ></script> 
    <script src={{asset("/js/ajax.js")}} ></script>
    
@include('layouts.notification')
    
</body>
</html>