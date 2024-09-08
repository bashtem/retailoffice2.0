@extends('layouts.stock_keeper')

@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="confirmPurchase">
            <!-- Breadcrumbs-->
            <ol class="breadcrumb size11 ">
                <li class="breadcrumb-item active">
                <a href="#" class="text-muted" id="title">
                <i class="fas fa-clipboard-check" ></i>
                <span class="nav-link-text">Confirm Delivery</span> </a>
                </li>                
            </ol>                        
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active show" data-toggle="tab" href="#confirm_purchase" ng-click="fetchpurchase()"><i class="fa fa-clipboard-list" ></i> Confirm Delivery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#outstandingPurchase" ng-click="fetchOutstanding()"><i class="far fa-calendar-times"></i> Outstanding Delivery</a>
                </li>                   
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#history"><i class="fa fa-history"></i> History</a>
                </li>                    --}}
            </ul>
            <div class="col-md-8 offset-md-2 " >         
                <div id="myTabContent" class="tab-content"  ng-init="fetchpurchase()" ><br>
                        <div class="tab-pane fade active show col-md-12 " id="confirm_purchase" >
                            <div class="style-10 cusH2">
                                <div class="alert alert-dark align-self-center text-muted" ng-show="confirmPurchaseData.length == 0" >
                                    <h6 class="alert-heading">No Delivery to Confirm!</h6>
                                    <p class="mb-0">Kindly wait for an Order.</p>
                                </div>                                
                                <div ng-repeat="confPur in confirmPurchaseData" >
                                    <div class="card" >
                                    <div class="card-header  alert-primary"> @{{confPur.supplier.sup_company_name}} <span class="float-right">@{{confPur.qty.qty_desc}}</span></div>
                                        <div class="card-body table-responsive"   >                                    
                                            <table class="table table-hover"  width="100%">
                                                <thead class="thead-default size11 text-muted">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Quantity</th> 
                                                        <th>Outstanding / Excess</th> 
                                                        <th>Confirm</th> 
                                                    </tr>
                                                </thead>
                                                <tbody class="size10">
                                                    <tr ng-repeat="x in confPur.purchase_order_item" >
                                                        <td>@{{x.items.item_name}} </td>
                                                        <td>@{{x.purchase_qty | number}} </td>
                                                        <td>
                                                                <div class="input-group outstandingWidth" >
                                                                <div class="input-group-prepend">
                                                                    <select class="custom-select custom-select-sm" ng-disabled="x.isChecked" ng-options="x for x in qtyStatus" ng-model="selOutExcess" ng-change="selDelType()" style="width:12em; height:3.2em">
                                                                        <option value="">COMPLETE</option>
                                                                    </select>
                                                                </div>
                                                                <input  type="number" class="te" aria-label="" ng-model="outExcessInput" ng-disabled="!selOutExcess || x.isChecked" >
                                                                </div>
                                                        </td>
                                                        <td> 
                                                            <div class="pretty  p-curve p-icon p-bigger p-pulse">
                                                                <input ng-model=x.isChecked ng-change="tickpurchase(x)" type="checkbox" ng-disabled="(outExcessInput <=0  || outExcessInput==null || ( (selOutExcess=='OUTSTANDING') && (outExcessInput >= x.purchase_qty) )) && selOutExcess!=null" />
                                                                <div class="state p-success">
                                                                    <i class="icon fa fa-check"></i>
                                                                    <label></label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>                                                                                                       
                                                </tbody>
                                            </table>                                        
                                        </div>
                                    </div><br>
                                </div>
                            </div>
                            <br/>
                            <button type="button" ng-hide="confirmItem.length == 0" class="btn btn-outline-success btn-sm size10 float-right" ng-click="confirmPurchaseBtn()"><i class="fa fa-check-double"  ></i> Confirm Delivery </button>
                        </div>
                    
                        <div class="tab-pane fade" id="outstandingPurchase">    {{-- Outstanding Purchase --}}
                                <div class="alert alert-dark align-self-center text-muted" ng-show="outstandingPurchaseData.length == 0" >
                                        <h6 class="alert-heading">No Outstanding Delivery !</h6>
                                        <p class="mb-0">Kindly wait for an Order.</p>
                                </div>
                                <div ng-repeat="conOutstand in outstandingPurchaseData" >
                                        <div class="card" >
                                        <div class="card-header  alert-primary"> @{{conOutstand.supplier.sup_company_name}} <span class="float-right">@{{conOutstand.qty.qty_desc}}</span></div>
                                            <div class="card-body table-responsive"   >                                    
                                                <table class="table table-hover"  width="100%">
                                                    <thead class="thead-default size11 text-muted">
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Outstanding Quantity</th> 
                                                            <th>Confirm Quantity </th> 
                                                            <th>Confirm</th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody class="size10">
                                                        <tr ng-repeat="xo in conOutstand.purchase_excess_out" >
                                                            <td>@{{xo.purchase_order_item.items.item_name}} </td>
                                                            <td>@{{xo.qty | number}} </td>
                                                            <td>
                                                                <div class="input-group outstandingWidth" >
                                                                    <input  type="number" class="te" aria-label="" ng-model="confirmQty" ng-disabled="xo.isChecked" >
                                                                </div>
                                                            </td>
                                                            <td> 
                                                                <div class="pretty  p-curve p-icon p-bigger p-pulse">
                                                                    <input ng-model=xo.isChecked ng-change="tickOutstanding(xo,conOutstand)" type="checkbox" ng-disabled="confirmQty==null || (xo.qty < confirmQty || confirmQty <= 0)" />
                                                                    <div class="state p-success">
                                                                        <i class="icon fa fa-check"></i>
                                                                        <label></label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>                                                                                                       
                                                    </tbody>
                                                </table>                                        
                                            </div>
                                        </div><br>
                                </div>                                
                                    
                                <button type="button" ng-hide="confirmOutstanding.length == 0" class="btn btn-outline-primary btn-sm size10 float-right" ng-click="confirmOutstandingBtn()"><i class="fa fa-check-double"  ></i> Confirm Outstanding</button>
                        </div>            
                                            
                                            
                </div>
            </div>
                    
    </div>
@endsection
