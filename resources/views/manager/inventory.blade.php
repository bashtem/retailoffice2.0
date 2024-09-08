@extends('layouts.manager')
@section('contentWrapper')
<div class="container-fluid size10 text-muted" ng-controller="inventory">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb size11 ">
        <li class="breadcrumb-item active">     
            <a href="#" class="text-muted" >
                <i class="far fa-inventory"></i>
                <span class="nav-link-text">Inventory</span>
            </a>
        </li>                
    </ol>
    <div class="row">
        <div class="col-md-5 ">
            <div class="table-responsive size10">
                <table class="table table-hover table-sm " id="inventory" datatable="ng">
                    <thead>
                        <tr class="table-light">
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="pointer" ng-repeat="x in invLists" ng-click="selectInvItem(x.item_id)" >
                            <td>@{{$index+1}}</td>
                            <td class="text-primary">@{{x.item_name}} </td>
                            <td>@{{x.item_category.cat_desc}} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-7 style-10" >
            {{-- <div ng-if="selectedInv==null" class="text-center"><i class="fal fa-spinner-third fa-spin fa-3x"></i></div> --}}
            <div ng-hide="selectedInv == null">
                <h5>@{{selectedInv.item_name}} </h5><hr>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#details">Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#log" >Quantity Log</a>
                    </li>                        
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active show" id="details">
                                <br>
                            <ul class="list-group list-group-flush">
                                <li class=" d-flex justify-content-start ">
                                    <span><i class="fal fa-calendar-alt"></i> Date Added: &nbsp;</span>
                                    <span>@{{selectedInv.created_at}} </span>
                                </li>
                            </ul>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label"><i class="fal fa-file-alt"></i> Description:</label>
                                        <div class="form-group">
                                            <blockquote class="blockquote size11">
                                                <p class="mb-0">@{{selectedInv.item_desc}} </p>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 size10">
                                    <p>
                                        <a href="#" class=" btn-outline-danger btn-sm size10"  data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapseExample1 multiCollapseExample2"><i class="far fa-trash"></i> Remove Items</a>
                                    </p>
                                    <div class="collapse multi-collapse" id="multiCollapseExample1">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="input-group ">
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text size10"><i class="fal fa-weight"></i></span>
                                                        </div>
                                                        <select  class="form-control custom-select custom-select-sm  size10" ng-options="x.qty_type.qty_desc for x in selectedInv.item_qty" ng-model="remQtyType">
                                                                <option value=""> Unit</option>
                                                        </select>
                                                    </div>
                                                </div> 
                                                <div class="form-group">
                                                    <div class="input-group ">
                                                            <div class="input-group-prepend">
                                                            <span class="input-group-text size10"><i class="fal fa-weight-hanging"></i></span>
                                                            </div>
                                                        <input class="form-control form-control-sm size10" type="number" ng-model="remQty" placeholder="Quantity" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                        <textarea class="form-control size10" id="exampleTextarea" rows="2" ng-model="remNote" placeholder="Notes... (Optional)"></textarea>
                                                </div>
                                                <button class="btn btn-sm size9 btn-outline-danger float-right" ng-disabled="remQtyType==null || remQty == null || remQty <= 0 || remQty > remQtyType.quantity" ng-click="removeItem(selectedInv.item_id)" ><i class="far fa-minus-circle"></i> Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <table class="table table-hover table-sm">
                                <thead>
                                <tr class="table-light">
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Min Price</th>
                                    <th>Price</th>
                                    <th>Max Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr  ng-repeat="x in selectedInv.item_qty">
                                <td>@{{x.qty_type.qty_desc}} </td>
                                <td class="text-primary">@{{x.quantity | number}} </td>
                                <td>@{{x.item_price.min_price | currency:"":2}} </td>
                                <td class="text-primary">@{{x.item_price.price | currency:"":2}} </td>
                                <td>@{{x.item_price.max_price | currency:"":2}} </td>
                                </tr>
                                </tbody>
                            </table>                        
                    </div>  
                    <div class="tab-pane fade" id="log">
                        <br>
                        <div class="row">
                            <div class="form-group col-md-2">
                                    <label for="fromDate">From:</label>
                                    <div class="input-group mb-3" id="fromDate">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                                            </div>
                                            <input type="text" uib-datepicker-popup myDate  ng-click="showFromDate()" is-open="fromUiOpen"  class="form-control form-control-sm size10" ng-model="fromDate" aria-label="">
                                    </div>
                            </div>
                            <div class="form-group col-md-2">
                                    <label for="toDate">To:</label>
                                    <div class="input-group mb-3" id="toDate">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                                            </div>
                                            <input type="text" uib-datepicker-popup myDate  ng-click="showToDate()" is-open="toUiOpen" ng-change="salesReport()" class="form-control form-control-sm size10" ng-model="toDate" aria-label="">
                                    </div>
                            </div>
                            <div class="form-group col-md-2 size9 mt-4">
                                <select  class="form-control custom-select custom-select-sm  size10" ng-options="x.qty_type.qty_desc for x in selectedInv.item_qty" ng-model="qtyTypeId">
                                    {{-- <option value=""> Unit</option> --}}
                                </select>
                            </div>
                            <div class="form-group mr-4 mt-4">
                                    <button type="button" ng-click="itemQtyHistory()" class="btn-sm btn btn-outline-primary size9"><i class="fa fa-search"></i> Search</button>
                            </div>
                            <div class="form-group mt-4" ng-hide="showLoader">
                                    <button type="button" ng-click="downloadQtyHistory()" class="btn-sm btn btn-outline-info size9"><i class="fal fa-download"></i> Download</button>
                            </div>
                        </div>
                        
                        <div class="loaders col-md-8" ng-show="showLoader">
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                            <span>loading...</span>
                            <p style="margin-bottom:0.5rem"></p>
                        </div>

                        <div ng-hide="showLoader">
                            <table class="table table-hover table-sm table-striped"  >
                                <thead>
                                    <tr class="table-light">
                                        <th>USER</th>
                                        <th>OLD QTY </th>
                                        <th>NEW QTY</th>
                                        <th>DIFF</th>
                                        <th>DATE</th>
                                        <th>TIME</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="x in itemHistoryList">
                                        <td>@{{x.name}} </td>
                                        <td>@{{x.old_qty}} </td>
                                        <td>@{{x.new_qty }} </td>
                                        <td>@{{x.diff}} </td>
                                        <td>@{{x.date }} </td>
                                        <td>@{{x.time }} </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="alert alert-light text-center" ng-show ="itemHistoryList.length == 0" >
                                <h6 class="alert-heading"><i class="fa fa-database"></i> No Records Available...</h6>
                                <p class="mb-0">.</p>
                            </div>

                        </div>
                        

                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection