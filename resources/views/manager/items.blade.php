@extends('layouts.manager')

@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="itemPriceCtrl">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
            <li class="breadcrumb-item active">     
            <a href="#" class="text-muted" id="title">
            <i class="fa fa-cubes" ></i>
            <span class="nav-link-text">Items</span> </a>
            </li>                
        </ol>

        <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active show" data-toggle="tab" href="#itemList"><i class="fa fa-list-ul"></i> Items List </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#itemPrice"><i class="far fa-money-bill-alt"></i> Item Price</a>
                </li>                   
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#qtyConversion" ng-click="fetchConvertData()"><i class="fa fa-exchange-alt" ></i> Quantity Conversion</a>
                </li>                   
        </ul>
        <div id="myTabContent" class="tab-content"  ng-init="fetchItemData()" >
                <div class="tab-pane fade active show" id="itemList">
                    <div class="row">            
                        <div class="col-md-5  style-10 cusH1"><br>
                                {{-- @foreach($items as $item) --}}
                                    <div class="list-group" ng-repeat="item in itemData">
                                        <li class="list-group-item list-group-item-secondary  text-muted justify-content-between d-flex">
                                            <span class="font-weight-bold">@{{item.cat_desc}}</span>
                                            <span ><i class="far fa-calendar-check"></i> Date Added</span>
                                        </li>
                                        {{-- @foreach($item->items as $item) --}}
                                        <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="itemInfo in item.items">
                                            @{{itemInfo.item_name}}
                                            <div class="justify-content-end">
                                               @{{itemInfo.created_at}}
                                            </div>
                                        </li>
                                        {{-- @endforeach --}}
                                    </div><br>
                                {{-- @endforeach                   --}}
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-4 size11 text-muted">
                            <br>
                            <p><i class="fa fa-plus"></i> Add Item</p>                                 
                                <form method="POST" action="addItem" >
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text size10"><i class="fa fa-tag"></i> </span>
                                            </div>
                                            <input type="text" maxlength="20" class="form-control form-control-sm size10" required name="itemName" placeholder="Item Name" aria-label="Amount (to the nearest dollar)">                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text size10"><i class="fa fa-layer-group"></i> </span>
                                            </div>
                                            <select class="custom-select custom-select-sm size10" required name="itemCategory" >
                                                <option value="">Select item Category</option>
                                                {{-- @foreach($items as $itemCat) --}}
                                                <option value="@{{item.cat_id}}" ng-repeat="item in itemData" >@{{item.cat_desc}}</option>
                                                {{-- @endforeach --}}
                                            </select>              
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text size10"><i class="fa fa-file-alt"></i></span>
                                            </div>
                                            <textarea class="form-control form-control-sm size10" name="itemDescription" placeholder="Item Description (Optional)"  ></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary float-right size9" name="addItem"><i class="fa fa-plus"></i> Add Item</button>
                                    </div>
                                </form>                            
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade " id="itemPrice">
                    <div class="row" >
                        <div class="col-md-5 style-10 cusH1"><br>
                                {{-- @foreach($items as $itemEach) --}}
                                    <div class="list-group" ng-repeat="item in itemData">
                                        <li class="list-group-item list-group-item-secondary  text-muted justify-content-between d-flex">
                                            <span class="font-weight-bold">@{{item.cat_desc}}</span>
                                            <span class="">Sales Price (N)</span>
                                        </li>
                                        {{-- @foreach($itemEach->items as $item) --}}
                                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="itemInfo in item.items">
                                                    <a href="#" ng-click="showPrice($parent.$index,$index)" class="showClass ">
                                                            <span >@{{itemInfo.item_name}} <i class="fa fa-pen hideClass"></i></span>
                                                    </a>
                                                    <div class="justify-content-end">
                                                            <div class="dropdown">
                                                                    <a href="#" class=" btn-outline-secondary btn-sm size9 " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fas fa-eye"></i> View Price </a>
                                                             
                                                               <div class="dropdown-menu" >
                                                                    <h6 class="dropdown-header size10">@{{itemInfo.item_name}}</h6>
                                                                    <ul class="list-group list-group-flush size10">
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="itemPrice in itemInfo.item_price">
                                                                              @{{itemPrice.qty_type.qty_desc}}
                                                                                    <span class="badge badge-primary badge-pill ">@{{itemPrice.price | currency:"":0}}</span>
                                                                            </li>
                                                                    </ul>
                                                               </div>
                                                             </div>
                                                    </div>
                                                </li>
                                        {{-- @endforeach --}}
                                    </div><br>
                                {{-- @endforeach   --}}
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-5 style-10 cusH1"><br>
                                <div class="card mb-3" ng-hide="hideCard">
                                    <h6 class="card-header text-muted size10">@{{itemName}} </h6>
                                        <div class="card-body priceCard" ng-hide="hidePrice">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card text-white bg-secondary animated flipInX ">
                                                            <div class="card-body priceCard">
                                                                <div class="d-flex bd-highlight">
                                                                        <div class="flex-fill ">@{{qtyName}} </div>
                                                                        <div class="flex-fill size13">@{{qty | number}} </div>
                                                                </div>                                                                
                                                            </div>
                                                            <div class="card-footer text-white clearfix  z-1 small noPaddingBottom" >
                                                                    <span class="float-left size11 ">Quantity</span>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card text-white bg-danger animated flipInY ">
                                                            <div class="card-body priceCard">
                                                                <div class="d-flex bd-highlight">
                                                                        <div class="flex-fill ">@{{qtyName}}</div>
                                                                        <div class="flex-fill size13">@{{salePrice}} </div>
                                                                </div>                                                                
                                                            </div>
                                                            <div class="card-footer text-white clearfix  z-1 small noPaddingBottom" >
                                                                    <span class="float-left size11 ">Selling Price</span>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div><br>                                            
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card text-white bg-info animated flipInX ">
                                                            <div class="card-body priceCard">
                                                                <div class="d-flex bd-highlight">
                                                                        <div class="flex-fill ">COST PRICE</div>
                                                                        <div class="flex-fill ">@{{costPrice | currency:"":0}} </div>
                                                                </div>
                                                                <div class="d-flex bd-highlight">
                                                                        <div class="flex-fill ">TOTAL PRICE</div>
                                                                        <div class="flex-fill ">@{{totalPrice | currency:"":0}} </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer text-white clearfix  z-1 small noPaddingBottom" >
                                                                    <span class="float-left size11 ">Expenses</span>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card text-white bg-success animated flipInY ">
                                                            <div class="card-body priceCard">
                                                                    <div class="d-flex bd-highlight">
                                                                            <div class="flex-fill ">REVENUE</div>
                                                                            <div class="flex-fill ">@{{revenue | currency:"":0}} </div>
                                                                    </div>
                                                                    <div class="d-flex bd-highlight">
                                                                            <div class="flex-fill ">PROFIT</div>
                                                                            <div class="flex-fill ">@{{profit | currency:"":0}} </div>
                                                                    </div>
                                                            </div>
                                                            <div class="card-footer text-white clearfix  z-1 small noPaddingBottom" >
                                                                    <span class="float-left size11 ">Expected Income</span>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div><br>                                            
                                            </div>                                                                                
                                        </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between">
                                                <div class="form-group">
                                                        <label for="">Minimum Price</label>
                                            <input type="text" name="minPrice" placeholder="Minimum Price" ng-class="error" ng-model="minPrice" class="form-control form-control-sm size10">
                                                </div>
                                                <div class="form-group">
                                                        <label for="">Sales Price</label>
                                            <input type="text" name="minPrice" placeholder="Sales Price" ng-model="salePrice" class="form-control form-control-sm size10">
                                                </div>
                                                <div class="form-group">
                                                        <label for="">Maximum Price</label>
                                            <input type="text" name="minPrice" placeholder="Maximum Price" ng-model="maxPrice" class="form-control form-control-sm size10">
                                                </div>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between form-inline">
                                              <select  class="form-control size10 " ng-model="qtyType" ng-change="qtyTypeSelect()">
                                                 <option value="" >Select Quantity</option>
                                                  @foreach($qtyType as $qType)
                                                    <option  value="{{$qType->qty_id}}">{{$qType->qty_desc}} </option>
                                                  @endforeach
                                              </select>                                            
                                            <button type="button"  class="btn btn-sm btn-outline-secondary size10 " ng-click="updatePrice()" ng-disabled="qtyType == null"><i class="fa fa-sync-alt"></i> Update Price</button>
                                        </li>
                                    </ul>                                    
                                </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="qtyConversion">
                    <br>
                    <div class="row text-muted">
                        <div class="col-md-1 "></div>
                        <div class="col-md-3">
                            <p><i class="fa fa-plus"></i> Add Quantity Conversion</p> 
                                    <div class="form-group">
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text size10 "><i class="fa fa-tag"></i> </span>
                                            </div>
                                            <select class="custom-select custom-select-sm size10 text-muted" ng-model="itemCnv" ng-options="x.item_name group by x.cat_desc for x in itemsConv" >
                                                <option value="">Select Item </option>
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="form-group d-flex ">
                                        
                                                <div class="input-group ">                                                    
                                                    <select class="custom-select custom-select-sm size10 text-muted" ng-model="srcQty" ng-options="x.qty_desc for x in qtyData" ng-change="selQtyCnv(srcQty)">
                                                                <option value="">Source Qty. </option>
                                                    </select>
                                                    <div class="input-group-append">
                                                            <span ><input type="number" class="form-control form-control-sm size10" ng-model="srcNum" placeholder="Qty." ng-change="chgInput(srcNum)"><span>
                                                    </div>              
                                                </div>

                                                <div class="align-self-center" style="padding-left:10px; padding-right:10px"><i class="fa fa-arrow-right"></i></div> 
                                                
                                                <div class="input-group ">
                                                    <select class="custom-select custom-select-sm size10 text-muted" ng-model="cnvQty" ng-disabled="srcQty==null || srcNum==null || srcNum <= 0"  ng-options="x.qty_desc for x in qtyCnvData">
                                                                <option value="">Convert Qty. </option>
                                                    </select>
                                                    <div class="input-group-append">
                                                            <span><input type="number" class="form-control form-control-sm size10" placeholder="Qty." ng-model="cnvNum" ng-disabled="srcQty==null || srcNum==null || srcNum <= 0" ></span>
                                                    </div>              
                                                </div>        
                                    </div><hr>
                                    <div class="form-group">
                                            <button type="button" ng-disabled="cnvQty== null || cnvNum == null || cnvNum <= 0 || itemCnv == null" class="btn btn-sm btn-outline-info float-right size9" ng-click ="addConvertData()"><i class="fa fa-plus"></i> Add Conversion</button>                                            
                                    </div>
                        </div>
                        <div class="col-md-7 style-10 cusH2" >
                            <p><i class="fa fa-th-list"></i> Conversion List</p>
                             
                                <table class="table table-hover table-striped">
                                        <thead>
                                        <tr class="table-primary">
                                            <th scope="col">Item</th>
                                            <th scope="col">Source Unit</th>
                                            <th scope="col">Converted Unit</th>
                                            <th scope="col">Source Qty.</th>
                                            <th scope="col">Converted Qty.</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        
                                        <tbody>
                                            
                                        <tr class="delbtn" ng-repeat = "x in cnvDatas">
                                            <td >@{{x.items.item_name}} </td>
                                            <td>@{{x.src_qty_type.qty_desc}} </td>
                                            <td>@{{x.cnv_qty_type.qty_desc}}</td>
                                            <td>@{{x.initial_qty}} </td>
                                            <td>@{{x.converted_qty}}</td>
                                            <td><a href="#" class="text-danger delbtn" ng-click="delCnv(x)" title="Remove" ><i class="fal fa-trash-alt size12"></i> </a> </td>
                                        </tr>
                                        
                                        
                                        </tbody>
                                </table>        
                                <div class="alert alert-secondary" ng-hide="cnvDatas.length != 0">
                                        <h6 class="alert-heading">Empty Quantity Conversion List</h6>
                                        <p class="mb-0">Kindly add Quantity Conversion.</p>
                                </div>                    
                        </div>
                    </div>
                </div>
        </div>
    </div>
@endsection