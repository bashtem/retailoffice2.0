@extends('layouts.stock_keeper')

@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="stockout">
                    <!-- Breadcrumbs-->
                    <ol class="breadcrumb size11 ">
                        <li class="breadcrumb-item active">
                            <a href="#" class="text-muted" id="title">
                            <i class="fas fa-shopping-cart" ></i>
                            <span class="nav-link-text">Stock Out</span> </a>
                        </li>    
                        <li class="breadcrumb-item">
                                <span><a href="" ng-click="reloadStock()"  >Reload Stock <span ng-bind-html="spinStock"></span> </a></span>
                        </li>            
                    </ol>
                    
                    
                    <div class="row">

                        <div class="col-md-8 offset-md-2">
                            <div class="accordion  style-10 cusH2" id="accordionExample" >
                                    <div class="alert alert-secondary text-center text-muted" ng-show="stockOutItems.length == 0">
                                            <h6 class="alert-heading">No Available Items to Stock Out!</h6>
                                            <p class="mb-0">Kindly wait for an Order.</p>
                                    </div>
                                <div class="list-group" ng-repeat="x in stockOutItems" >
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="#" data-toggle="collapse"  data-target="#collapse@{{x.order_id}}" aria-expanded="true" >
                                                    <div class="form-group">
                                                            <label for="formGroupExampleInput" ><i class="far fa-user"></i> @{{x.cus.cus_name}}</label>
                                                            <div id="formGroupExampleInput">@{{x.order_no}} | @{{x.payment.payment_desc +' '+ x.order_date +' '+ x.order_time}}</div>
                                                    </div>    
                                                </a> 
                                                <div class="pretty  p-curve p-icon p-bigger p-pulse">
                                                    <input type="checkbox" ng-change="tickItem(x)" ng-model="x.itemChecked" />
                                                    <div class="state p-primary">
                                                    <i class="icon fa fa-check"></i>
                                                        <label></label>
                                                    </div>
                                                </div>                                                    
                                            </li> 
                                        <div id="collapse@{{x.order_id}}"  class="collapse " >
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="col-md-8 offset-md-2 size10 text-muted">
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
                                                </div>
                                            </div>
                                        </div>                        
                                </div>                               
                            </div><br>
                            <div class="form-group" ng-show="confirmStock.length != 0">
                                <a href="#" class="btn btn-outline-primary btn-sm size10 float-right" ng-click="confirmStockOut()"><i class="fa fa-arrow-right"></i> Stock out</a>
                            </div>                      
  
                        </div>
                    </div>
                      
    </div>
@endsection
