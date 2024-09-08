@extends('layouts.manager')

@section('contentWrapper')
        <div class="container-fluid size10" ng-controller="purchase">
                <!-- Breadcrumbs-->
                <ol class="breadcrumb size11 ">
                    <li class="breadcrumb-item active">     
                    <a href="#" class="text-muted" id="title">
                    <i class="fa fa-truck" ></i>
                    <span class="nav-link-text">Purchase</span> </a>
                    </li>                
                </ol>
                
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#Purchase"><i class="fa fa-cart-plus"></i> Make Purchase</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#History"><i class="fa fa-history"></i> Purchase History</a>
                    </li>                    --}}
                </ul>
                <div id="myTabContent" class="tab-content" ><br>                    
                    <div class="tab-pane fade active show" id="Purchase">
                        <div class="row">
                            <div class="col-md-3">

                                <div class="form-group" ng-init="fetchPurchaseDatas()">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="fa fa-users"></i></span>
                                        </div>
                                        <select ng-disabled="purchaseItem.length != 0" class="custom-select custom-select-sm size10 text-muted" ng-model="supplier" ng-options="x.sup_company_name for x in suppliers">
                                            <option value="">Select Supplier</option>
                                        </select>              
                                    </div>                                          
                                </div>
                                <div class="form-group">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="fab fa-amazon-pay"></i></span>
                                        </div>
                                        <select ng-disabled="purchaseItem.length != 0" class="custom-select custom-select-sm size10 text-muted" ng-model="payment_method" ng-options="x.payment_desc for x in paymentTypes">
                                                <option value="">Select Payment Method</option>                                            
                                        </select>              
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="fa fa-box-open"></i></span>
                                        </div>
                                        <select ng-disabled="purchaseItem.length != 0" class="custom-select custom-select-sm size10 text-muted" ng-model="quantity_type" ng-options="x.qty_desc for x in qtyTypes">
                                                <option value="">Select Quantity Type</option>                                            
                                        </select>              
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="fa fa-tag"></i></span>
                                        </div>
                                        <select class="custom-select custom-select-sm size10 text-muted" ng-model="item" ng-options="x.item_name group by x.cat_desc for x in itemCategory">
                                            <option value="" >Select Item</option>                                           
                                                                                                                            
                                        </select>              
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="fa fa-weight"></i></span>
                                        </div>
                                        <input type="number" class="form-control form-control-sm size10" ng-model="item_quantity" placeholder="Item Quantity" aria-label="Item Quantity">                
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="fa fa-money-bill-alt"></i></span>
                                        </div>
                                        <input type="number" class="form-control form-control-sm size10" ng-model="item_price"   placeholder="Purchase Price" aria-label="Price">                
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="far fa-clipboard"></i></span>
                                        </div>
                                        <textarea ng-disabled="purchaseItem.length != 0" class="form-control form-control-sm size10" placeholder="Purchase Note (Optional)" ng-model="purchase_note" ></textarea>
                                    </div>
                                </div>
                                <div class="form-group" >                                            
                                            <button type="button" class="btn btn-sm btn-outline-info float-right size9" ng-disabled="supplier == null || payment_method == null || quantity_type == null || item == null || item_quantity == null || item_quantity <= 0 || item_price == null || item_price <=0" ng-click="addItem()"><i class="fa fa-plus"></i> Add Item</button>                                            
                                </div>                                        
                            </div>
                            <div class="col-md-8 " id="purchaseList">                                
                                <div class="alert alert-light text-center" ng-hide="purchaseItem.length != 0">
                                    <h6 class="alert-heading">Empty Purchase List</h6>
                                    <p class="mb-0">Kindly add items in order to make purchase.</p>
                                </div>  
                                <div class="style-10 cusH2">
                                        <div class="card border-info mb-3"  ng-repeat="purchase in purchaseItem">
                                            <div class="card-header " >@{{purchase.supplier}} <span class="float-right">@{{quantity_type.qty_desc}} </span></div>
                                            {{-- <div class="card-body"> --}}
                                                <table class="table table-hover table-striped">
                                                    <thead class="text-muted">
                                                        <tr>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Quantity.</th>
                                                        <th scope="col">Price (N)</th>
                                                        <th scope="col">Amount (N)</th>
                                                        <th scope="col">Payment Method</th>
                                                        <th scope="col"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>                                        
                                                        <tr class="table-secondary delbtn" ng-repeat="item in purchase.data">
                                                        <td scope="row" > @{{item.name}}</td>
                                                        <td > @{{item.qty}}</td>
                                                        <td >@{{item.price | currency:"":0}}</td>
                                                        <td >@{{item.itemAmount | currency:"":0}}</td>
                                                        <td >@{{item.payment}}</td>
                                                        <td ><a href="#" class="text-danger delbtn" ng-click="delItem($parent.$index,$index)" title="Remove" ><i class="fas fa-minus size12"></i> </a> </td>
                                                        </tr>                                                      
                                                    </tbody>
                                                </table>
                                            {{-- </div> --}}
                                        </div>
                                       
                                </div><br>
                                <div ng-hide="purchaseItem.length == 0">
                                    <div class="badge badge-pill badge-danger size10">Total Amount : @{{amount | currency:"":0}} </div>                                
                                    <div class="btn-group float-right">                                            
                                        <button type="button" ng-click="cancle()" class="btn btn-sm btn-outline-danger float-right size9"><i class="fa fa-ban"></i> Cancle</button>                                            
                                        <button type="button" ng-click="savePurchase()" class="btn btn-sm btn-outline-success float-right size9"><i class="fa fa-save"></i> Save Purchase</button>                                            
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-1"></div> -->
                        </div>
                    </div>

                    {{-- <div class="tab-pane fade" id="History">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Cras justo odio
                                        <span class="badge badge-primary badge-pill">14</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Dapibus ac facilisis in
                                        <span class="badge badge-primary badge-pill">2</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Morbi leo risus
                                        <span class="badge badge-primary badge-pill">1</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>                     --}}
                </div>   
        </div>
@endsection