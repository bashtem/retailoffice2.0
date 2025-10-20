@extends('layouts.manager')
@section('contentWrapper')
    <div class="container-fluid size10 text-muted" ng-controller="history">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
            <li class="breadcrumb-item active">     
                <a href="#" class="text-muted" >
                    <i class="fas fa-history"></i>
                    <span class="nav-link-text">History</span>
                </a>
            </li>                
        </ol>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active"  data-toggle="tab" href="#orders" ng-click="salesHistory()"><i class="fa fa-shopping-cart"></i> Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#transfers" ng-click="transferHistory()" ><i class="fa fa-exchange-alt"></i> Transfers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#purchase" ng-click="purchaseHistory()"><i class="fa fa-truck"></i> Purchase</a>
            </li>
                
        </ul><br>
        <div id="myTabContent" class="tab-content">

            <div class="tab-pane fade show active" id="orders">
                    <div class="form-group col-md-2">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                            </div>
                            <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" ng-change="salesHistory()" class="form-control form-control-sm size10" ng-model="salesDateHis" aria-label="">
                            <div class="input-group-append">
                                        <span class="input-group-text text-muted size10"> <a href="#" ng-click="salesHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover " >
                        <thead>
                            <tr>
                            <th >Order No.</th>
                            <th >Customer</th>
                            <th >Payment</th>
                            <th >Unit</th>
                            <th >Quantity</th>
                            <th >Total Amount</th>
                            <th >Order Status</th>
                            <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="x in salesHistoryData" ng-init="orderId = x.order_id">
                            <td>@{{x.order_no}} </td>
                            <td>@{{x.cus.cus_name}} </td>
                            <td>@{{x.payment.payment_desc}} </td>
                            <td>@{{x.qty.qty_desc}} </td>
                            <td>@{{x.order_total_qty | number}} </td>
                            <td>@{{x.order_total_amount | currency:'':2}} </td>
                            <td ng-if="x.order_status == 'SUCCESS'"><span class="badge badge-pill badge-success">@{{x.order_status}} </span> </td>
                            <td ng-if="x.order_status == 'PENDING'"><span class="badge badge-pill badge-warning">@{{x.order_status}} </span> </td>
                            <td ng-if="x.order_status == 'CANCLED'"><span class="badge badge-pill badge-danger">@{{x.order_status}} </span> </td>
                            <td><span class="mr-2"><a href="#" ng-click="orderItems($index)"  title="View" ><i class="fa fa-eye"></i></a></span><span><a ng-if="x.order_status != 'CANCLED'" href="#" ng-click="cancleOrder(orderId)" title="Cancle Order" > <i class="far fa-times-circle text-danger"></i></a></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-light text-center" ng-show ="salesHistoryData.length == 0">
                            <h6 class="alert-heading"><i class="fa fa-database"></i> No Data Available</h6>
                            <p class="mb-0">.</p>
                    </div> 
            </div>

            <div class="tab-pane fade" id="transfers">
                <div class="col-md-8 offset-md-2">
                    <div class="form-group col-md-3">
                        <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                                </div>
                                <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" ng-change="transferHistory()" class="form-control form-control-sm size10" ng-model="salesDateHis" aria-label="">
                                <div class="input-group-append">
                                    <span class="input-group-text text-muted size10"> <a href="#" ng-click="transferHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                                </div>
                        </div>
                    </div>
                    <div class="alert alert-light text-center text-muted" ng-show="transferHistoryData.length == 0">
                            <h6 class="alert-heading"><i class="fa fa-book"></i> No Records Found!</h6>
                            {{-- <p class="mb-0">.</p> --}}
                    </div> 
                    <div class="accordion" id="accordionExample">
                        <div class="list-group" ng-repeat="x in transferHistoryData">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="#" data-toggle="collapse" data-target="#collapse@{{x.transfer_id}}" aria-expanded="true" aria-controls="" >
                                            <div class="form-group">
                                                <label for="formGroupExampleInput" ><i class="far fa-user"></i> @{{x.user.name}} </label>
                                                    <div id="formGroupExampleInput"><i class="far fa-clock"></i>  @{{x.transfer_date +' '+ x.transfer_time}} </div>
                                            </div>
                                        </a>
                                        <span> 
                                            <span ng-if="x.transfer_status == 'SUCCESS' " class="badge badge-success badge-pill">@{{x.transfer_status}} </span>
                                            <span ng-if="x.transfer_status == 'PENDING' " class="badge badge-warning badge-pill">@{{x.transfer_status}} </span>
                                            <span ng-if="x.transfer_status == 'CANCLED' " class="badge badge-danger badge-pill">@{{x.transfer_status}} </span>
                                            <a ng-if="x.transfer_status == 'SUCCESS' || x.transfer_status == 'PENDING'" href="#" ng-click="cancleTransfer(x.transfer_id)" title="Cancle Transfer" ><i class="far fa-times-circle text-danger"></i></a>
                                        </span>
                            </li> 
                            <div id="collapse@{{x.transfer_id}}" class="collapse " >
                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-md-12 size10 text-muted">
                                            <table class="table table-hover table-sm text-justify">
                                                <thead class="table-light">
                                                    <tr >
                                                        <th >Item</th>
                                                        <th >Source Unit</th>
                                                        <th >Transfer Unit</th>
                                                        <th >Source Qty</th>
                                                        <th >Transfer Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="y in x.transfer_items">
                                                        <td>@{{y.conversion.items.item_name}} </td>
                                                        <td>@{{y.conversion.src_qty_type.qty_desc}} </td>
                                                        <td>@{{y.conversion.cnv_qty_type.qty_desc}} </td>
                                                        <td>@{{y.transfer_qty | number}} </td>
                                                        <td>@{{y.transferred_qty | number}} </td>
                                                    </tr>
                                                </tbody>
                                            </table> 
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>                                   
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="purchase">
                <div class="col-md-8 offset-md-2">
                    <div class="form-group col-md-3">
                        <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                                </div>
                                    <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" ng-change="purchaseHistory()" class="form-control form-control-sm size10" ng-model="salesDateHis" aria-label="">
                                <div class="input-group-append">
                                    <span class="input-group-text text-muted size10"> <a href="#" ng-click="purchaseHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                                </div>
                        </div>
                    </div>
                    <div class="alert alert-light text-center text-muted" ng-show="purchaseHistoryData.length == 0">
                            <h6 class="alert-heading"><i class="fa fa-book"></i> No Records Found!</h6>
                            {{-- <p class="mb-0">.</p> --}}
                    </div> 
                    <div class="accordion" id="accordionExample">
                        <div class="list-group" ng-repeat="x in purchaseHistoryData">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="#" data-toggle="collapse" data-target="#collapse@{{x.purchase_id}}" aria-expanded="true" aria-controls=""  >
                                    <div class="form-group">
                                        <label for="formGroupExampleInput" ><i class="fa fa-truck"></i> @{{x.supplier.sup_company_name}} </label>
                                            <div id="formGroupExampleInput"><i class="far fa-clock"></i>  @{{x.purchase_date +' '+ x.purchase_time}} | @{{x.payment.payment_desc}} </div>
                                    </div>
                                </a>
                                <span> 
                                    <span  class="badge badge-primary badge-pill">@{{x.qty.qty_desc}} </span>
                                    <a ng-if="x.user_id_cancled == null" href="#" ng-click="cancleAllPurchase(x.purchase_id)" title="Cancle Purchase" ><i class="far fa-times-circle text-danger"></i></a>
                                </span>
                            </li>
                            <div id="collapse@{{x.purchase_id}}" class="collapse">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-md-12 size10 text-muted">
                                            <table class="table table-hover table-sm text-justify">
                                                <thead class="table-light">
                                                    <tr >
                                                        <th >Item</th>
                                                        <th >Quantity</th>
                                                        <th >Price</th>
                                                        <th >Amount</th>
                                                        <th ></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="y in x.purchase_order_item">
                                                        <td>@{{y.items.item_name}} </td>
                                                        <td>@{{y.purchase_qty | number}} </td>
                                                        <td>@{{y.purchase_price | currency:'':2}} </td>
                                                        <td>@{{(y.purchase_qty * y.purchase_price) | currency:'':2}} </td>
                                                        <td>
                                                            <span ng-if="y.purchase_status =='SUCCESS'" class="badge badge-pill badge-success">SUCCESS</span> 
                                                            <span ng-if="y.purchase_status =='PENDING'" class="badge badge-pill badge-warning">PENDING</span> 
                                                            <span ng-if="y.purchase_status =='CANCLED'" class="badge badge-pill badge-danger">CANCLED</span> 
                                                                <a ng-if="y.purchase_status != 'CANCLED'" href="#" ng-click="canclePurchase(y.purchase_id,y.item_id)" title="Cancle Purchase" ><i class="far fa-times-circle text-danger"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table> 
                                        </div>
                                    </div>
                                </div>
                            </span>                        
                        </div>                                   
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Order History Modal --}}
        <div class="modal fade orderHistory">
                <div class="modal-dialog " role="document">
                  <div class="modal-content text-muted">
                    <div class="card">
                        <div class="card-header size11">Sale Order @{{orderItemsData.order_no}} </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="far fa-user"></i> @{{orderItemsData.cus.cus_name}} </span>
                                    <span><i class="fa fa-mobile-alt"></i> @{{orderItemsData.cus.cus_mobile}} </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center" >
                                    <span><i class="far fa-calendar"></i> @{{orderItemsData.order_date +' '+ orderItemsData.order_time}}</span>
                                    <span> 
                                        <span ng-if="orderItemsData.order_status == 'SUCCESS'" class="badge badge-success badge-pill"> @{{orderItemsData.order_status}} </span> 
                                        <span ng-if="orderItemsData.order_status == 'PENDING'" class="badge badge-warning badge-pill"> @{{orderItemsData.order_status}} </span> 
                                        <span ng-if="orderItemsData.order_status == 'CANCLED'" class="badge badge-danger badge-pill"> @{{orderItemsData.order_status}} </span> 
                                    <a ng-if="orderItemsData.order_status != 'CANCLED'" href="#" ng-click="cancleOrder(orderItemsData.order_id)" data-toggle="tooltip" title="Cancle Order" class="mr-2"><i class="far fa-times-circle text-danger"></i></a>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-money-bill-alt"></i> @{{orderItemsData.order_total_amount | currency:'':2}} | Discount: @{{orderItemsData.discount.total_discount | currency:'':2}} </span>
                                    <span >@{{orderItemsData.payment.payment_desc}} </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Sales Agent: @{{orderItemsData.user.name}}  </span>
                                    <span><i class="fa fa-box-open"></i> @{{orderItemsData.qty.qty_desc}} </span>
                                </li>
                            </ul>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="x in orderItemsData.items ">
                                        <td>@{{x.item.item_name}} </td>
                                        <td>@{{x.quantity | number }} </td>
                                        <td>@{{x.amount | currency:'':2}} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                  </div>
                </div>
        </div>
    </div>
@endsection