@extends('layouts.manager')
@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="stocktransfer">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
            <li class="breadcrumb-item active">     
            <a href="#" class="text-muted" >
                    <i class="fa fa-exchange-alt"></i>
            <span class="nav-link-text">Transfer</span> </a>
            </li>                
        </ol>
        
        <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active text-muted" data-toggle="tab" href="#transferItem"><i class="far fa-calendar-check"></i> Transfer Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  text-muted" data-toggle="tab" href="#confirmTransfer" ng-click="confirmTransferDatas()" ><i class="far fa-hand-holding-water"></i> Transfer Requests</a>
                </li>
        </ul><br>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade show active" id="transferItem">
                <div class="row container text-muted"> 
                    <div class="col-md-3">
                        <span><i class="fa fa-plus"></i> Add Transfer Item</span><hr> 
                        <div class="form-group">
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text size10"><i class="fa fa-tag"></i></span>
                                </div>
                                <select class="custom-select custom-select-sm size10 text-muted" ng-model="itemTrn" ng-change=clr() ng-options="x.item_name group by x.cat_desc for x in itemsTrans" >
                                    <option value="" >Select Item </option>
                                </select>              
                            </div>
                        </div>
                        <div class="form-group d-flex ">
                            <div class="input-group ">                                                    
                                <select class="custom-select custom-select-sm size10 text-muted" ng-model="srcQtyTrn" ng-options="x.qty_desc for x in qtyTransData" ng-change="selTranQty(srcQtyTrn)">
                                    <option value="">Select Qty</option>
                                </select>
                                <div class="input-group-append">
                                    <span ><input type="number" class="form-control form-control-sm size10" ng-model="srcNumTrn" placeholder="Qty." ng-change="cnvQtyTrn=null; cnvNumTrn=null"><span>
                                </div>              
                            </div>
                
                            <div class="align-self-center" style="padding-left:10px; padding-right:10px"><i class="fa fa-arrow-right"></i></div> 
                            
                            <div class="input-group ">
                                <select class="custom-select custom-select-sm size10 text-muted" ng-model="cnvQtyTrn" ng-change="fetchTrnQty(cnvQtyTrn)"   ng-options="x.qty_desc for x in qtyTransDataC" ng-disabled="srcQtyTrn==null || srcNumTrn <=0 || srcNumTrn==null">
                                            <option value="">Transfer Qty </option>
                                </select>
                                <div class="input-group-append">
                                        <span><input type="text" class="form-control form-control-sm size10" placeholder="Qty."  ng-model="cnvNumTrn" ng-disabled="1==1" ></span>
                                </div>              
                            </div>        
                        </div><hr>
                        <div class="form-group">
                            <button type="button" class="btn btn-sm btn-outline-info float-right size9" ng-disabled="cnvQtyTrn == null || cnvNumTrn == null || srcQtyTrn == null || srcNumTrn == null || srcNumTrn <= 0 " ng-click="addTrnItems()"><i class="fa fa-plus"></i> Add Transfer Item </button>                                            
                        </div>
                    </div>
                    <div class="col-md-9">
                        <span><i class="fa fa-clipboard-list"></i> Transfer List</span><hr> 
                            <div class="table-responsive"  id="itemList" style="max-height:282px" ng-hide="transferItems.items.length == 0">
                                <table class="table table-hover "  width="100%">
                                        <thead class="thead-default size11 text-muted">
                                            <tr>
                                                <th>Item</th>
                                                <th>Source Unit</th> 
                                                <th>Transfer Unit</th> 
                                                <th>Source Qty.</th> 
                                                <th>Transfer Qty.</th> 
                                                <th>Store Qty.</th> 
                                                <th></th> 
                                            </tr>
                                        </thead>
                                        <tbody class="size10">
                                                    <tr ng-repeat="x in transferItems.items" class="delbtn">
                                                        <td>@{{x.item_name}}  </td>
                                                        <td> @{{x.srcType}} </td>
                                                        <td> @{{x.trnType}}</td>                                                                
                                                        <td> @{{x.transfer_qty}} </td>                                                                
                                                        <td> @{{x.transferred_qty}}</td>                                                                
                                                        <td>
                                                            <div class="dropdown">
                                                                    <a href="#" ng-click="storeQty(x.item_id)" class=" btn-outline-secondary btn-sm size9" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fas fa-eye"></i> </a>                                                        
                                                                <div class="dropdown-menu" >
                                                                    <h6 class="dropdown-header size10">@{{storeQtyData.item_name}} </h6>
                                                                    <div ng-hide="storeQtyData != null" class="text-center text-muted"><i class="fal fa-spinner-third fa-spin "></i> </div>
                                                                    <ul class="list-group list-group-flush size10">
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="z in storeQtyData.item_qty">
                                                                            @{{z.qty_type.qty_desc}}
                                                                            <span class="badge badge-primary badge-pill ">@{{z.quantity | number}} </span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>                                                                
                                                        <td > <a  class="text-danger delbtn" href="#" role="button" ng-click="delTrnItems($index)" title="Remove" ><i class="fas fa-minus size12"></i> </a> </td>                                                                
                                                    </tr>                                                                                                          
                                        </tbody>
                                </table>                                        
                            </div>
                            <a ng-hide="transferItems.items.length==0" href="#" class="btn btn-outline-primary btn-sm size10 float-right" ng-click="transferStock()"><i class="fal fa-people-carry"></i> Transfer Stock  </a>
                            <div class="alert alert-secondary text-center text-muted" ng-hide="transferItems.items.length != 0">
                                <h6 class="alert-heading">Transfer List Empty!</h6>
                                <p class="mb-0">Kindly add items to transfer.</p>
                            </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade " id="confirmTransfer"> 
                    <div class="row">
                        <div class="col-md-8 offset-md-2 style-10 cusH2">
                            <div class="alert alert-secondary text-center text-muted" ng-show="transferDatas.length == 0">
                                <h6 class="alert-heading">No Available Transfer Request!</h6>
                                <p class="mb-0">Kindly wait for a Request.</p>
                            </div>
                            <div class="accordion" id="accordionExample" >
                                <div class="list-group" ng-repeat="x in transferDatas" >
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="#" data-toggle="collapse"  data-target="#collapse@{{x.transfer_id}}" aria-expanded="true" >
                                            <div class="form-group">
                                                <label for="formGroupExampleInput" ><i class="far fa-user"></i> @{{x.user.name}}</label>
                                                <div id="formGroupExampleInput"><i class="far fa-clock"></i>  @{{x.transfer_date +' '+ x.transfer_time}} </div>
                                            </div>    
                                        </a> 
                                        <span> 
                                            <span ng-if="x.transfer_status == 'SUCCESS' " class="badge badge-success badge-pill">@{{x.transfer_status}} </span>
                                            <span ng-if="x.transfer_status == 'PENDING' " class="badge badge-warning badge-pill">@{{x.transfer_status}} </span>
                                            <span ng-if="x.transfer_status == 'CANCLED' " class="badge badge-danger badge-pill">@{{x.transfer_status}} </span>
                                            <a ng-if="x.transfer_status == 'SUCCESS' || x.transfer_status == 'PENDING'" href="#" ng-click="cancleTransfer(x.transfer_id)" title="Cancle Transfer" ><i class="fal fa-times-circle text-danger size12"></i></a>
                                            <a ng-if=" x.transfer_status == 'PENDING'" href="#" ng-click="confirmTransfer(x.transfer_id)" title="Confirm Transfer" ><i class="fal fa-check-double text-success size12"></i></a>
                                        </span>                                                    
                                    </li> 
                                    <div id="collapse@{{x.transfer_id}}"  class="collapse " >
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="col-md-12 size10 text-muted">
                                                    <table class="table table-hover table-sm text-justify">
                                                        <thead class="table-light">
                                                            <tr >
                                                                <th>Item</th>
                                                                <th>Source Unit</th> 
                                                                <th>Transfer Unit</th> 
                                                                <th>Source Qty.</th> 
                                                                <th>Transfer Qty.</th> 
                                                                <th>Store Qty.</th> 
                                                                <th>Transfer Status</th> 
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr ng-repeat="y in x.transfer_items" class="delbtn">
                                                                <td>@{{y.item.item_name}}  </td>
                                                                <td> @{{y.conversion.src_qty_type.qty_desc}} </td>
                                                                <td> @{{y.conversion.cnv_qty_type.qty_desc}}</td>                                                                
                                                                <td> @{{y.transfer_qty | number}} </td>                                                                
                                                                <td> @{{y.transferred_qty | number}}</td>                                                                
                                                                <td> 
                                                                    <div class="dropdown">
                                                                            <a href="#" ng-click="storeQty(y.item_id)" class=" btn-outline-secondary btn-sm size9" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fal fa-eye"></i> </a>                                                        
                                                                        <div class="dropdown-menu" >
                                                                            <h6 class="dropdown-header size10">@{{storeQtyData.item_name}} </h6>
                                                                            <div ng-hide="storeQtyData != null" class="text-center text-muted"><i class="fal fa-spinner-third fa-spin "></i> </div>
                                                                            <ul class="list-group list-group-flush size10">
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="z in storeQtyData.item_qty">
                                                                                    @{{z.qty_type.qty_desc}}
                                                                                    <span class="badge badge-primary badge-pill ">@{{z.quantity | number}} </span>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span ng-if="y.transfer_status == 'SUCCESS' " class="badge badge-success badge-pill">@{{y.transfer_status}} </span>
                                                                    <span ng-if="y.transfer_status == 'PENDING' " class="badge badge-warning badge-pill">@{{y.transfer_status}} </span>
                                                                    <span ng-if="y.transfer_status == 'CANCLED' " class="badge badge-danger badge-pill">@{{y.transfer_status}} </span>
                                                                </td>                                                                
                                                                <td > 
                                                                    <a ng-if="y.transfer_status == 'SUCCESS' || y.transfer_status == 'PENDING'" class="text-danger delbtn" href="#" role="button" ng-click="cancleEachTrn(y.transfer_id, y.item_id)" title="Remove" ><i class="fal fa-times-circle size12"></i> </a> 
                                                                    <a ng-if=" y.transfer_status == 'PENDING'" class="text-success delbtn" href="#" role="button" ng-click="confirmEachTrn(y.transfer_id, y.item_id)" title="Confirm" ><i class="fal fa-check-double size12"></i> </a> 
                                                                </td>                                                                
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                        
                                </div>                               
                            </div><br>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection