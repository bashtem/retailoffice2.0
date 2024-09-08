@extends('layouts.manager')
@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="customers">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
            <li class="breadcrumb-item active">     
                <a href="#" class="text-muted" >
                    <i class="fa fa-user-friends"></i>
                    <span class="nav-link-text">Customers</span>
                </a>
            </li>                
        </ol>
        <div class="col-md-12 text-muted">
            <div class="row">
                <div class="col-md-3">
                        <a href="#!newcustomer" class=" size9 btn-sm btn-outline-secondary col-md-6"><i class="fa fa-user-plus"></i> Add Customer</a><br><br>
                        <table class="table table-hover table-sm" >
                            <thead>
                                <tr>
                                    <th>
                                        <input type="text" class="form-control form-control-sm size10" ng-model="searchCus" aria-label="" ng-change="searchCustomer()"  placeholder="Search Customer...">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat= "x in searched">
                                    <td><a href="#!cusprofile" ng-click="pickCus($index)" >@{{x.cus_name}}</a></td>
                                </tr>
                                
                            </tbody>
                        </table>
                        <div ng-if="searched.length==0" class="alert alert-light">
                                <h6 class="size11">No Record Found!</h6>
                        </div>
                </div>
                <div class="col-md-9" ng-view>
                    
                </div>
            </div>
        </div>
    </div>

<script type="text/ng-template" id="home">
    <div class="text-center col-md-6 text-light"><i class="fal fa-user-friends fa-9x"></i></div>
</script>

<script type="text/ng-template" id="newcustomer">
    <div class="col-md-7 text-muted">
        <p><i class="far fa-user-plus"> </i> New Customer</p><hr>
        <form ng-submit="addCustomer()">
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="far fa-user"></i> Full Name</label>
                <div class="col-sm-6">
                <input type="text"  class="form-control btn-sm size9" ng-model="$parent.cusName" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-mobile"></i> Phone</label>
                <div class="col-sm-6">
                <input type="tel"  class="form-control btn-sm size9" ng-model="$parent.cusPhone" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-at"></i> E-mail</label>
                <div class="col-sm-6">
                <input type="email"  class="form-control btn-sm size9" ng-model="$parent.cusMail" placeholder="Optional" >
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fa fa-home"></i> Address</label>
                <div class="col-sm-6">
                <input type="text"  class="form-control btn-sm size9"  required  ng-model="$parent.cusAddress">
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fab fa-amazon-pay"></i> Payment</label>
                <div class="col-sm-6">
                    <select class="custom-select custom-select-sm size9" ng-options="x.payment_desc for x in payData" ng-model="$parent.cusPay" required>
                        <option value="">Select Payment</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><a href="#!profile"></a></label>
                <div class="col-sm-6">
                <button type="submit" class="btn btn-outline-secondary btn-sm size9 float-right"><i class="far fa-user-plus"></i> Add User</button>
                </div>
            </div>
        </form>
    </div>
</script>

<script type="text/ng-template" id="editcustomer">
    <div class="text-muted col-md-6">
        <p><i class="far fa-user-edit"> </i> Edit Customer profile</p><hr>
        <form ng-submit="editCustomer()">
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="far fa-user"></i> Name</label>
                <div class="col-sm-6">
                    <input type="text"  class="form-control-plaintext size10" ng-model="$parent.editcusName" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-mobile"></i> Phone</label>
                <div class="col-sm-6">
                    <input type="tel"  class="form-control-plaintext size10"  ng-model="$parent.editcusPhone" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"> <i class="fal fa-at"></i> E-mail</label>
                <div class="col-sm-6">
                    <input type="email"  class="form-control-plaintext size10"  ng-model="$parent.edicustMail" >
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"> <i class="fab fa-amazon-pay"></i> Payment</label>
                <div class="col-sm-6">
                        <select class="custom-select custom-select-sm size9" ng-options="x.payment_desc for x in payData" ng-model="$parent.editcusPay" required>
                                <option value="">Select Payment</option>
                        </select>
                </div>
            </div>
            <div class="form-group row">
                    <label  class="col-sm-3 col-form-label"> <i class="fa fa-home"></i> Address</label>
                    <div class="col-sm-6">
                        <input type="text"  class="form-control-plaintext size10"  ng-model="$parent.editcusAddress" required>
                    </div>
                </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><a href="#!cusprofile"> <i class="far fa-long-arrow-left"></i> Back</a></label>
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-outline-secondary btn-sm size9 float-right"><i class="far fa-save"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</script>

<script type="text/ng-template" id="cusprofile">
    <div >
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active pointer" data-toggle="tab" data-target="#profile"><i class="far fa-user"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link pointer" data-toggle="tab" data-target="#credit" ng-click="creditOrder()"><i class="fal fa-credit-card-front"></i> Credit</a>
            </li>
            <li class="nav-item">
                <a class="nav-link pointer" data-toggle="tab" data-target="#discount" ng-click="discountItems()" ><i class="fal fa-percent"></i> Discount</a>
            </li>
        </ul><br>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade show active col-md-8" id="profile">
                <ul class="list-group list-group-flush ">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="far fa-user"></i> Name:</span>
                        <span>
                            <span>@{{pickedCus.cus_name}} </span>
                            <span><a ng-if="pickedCus" href="#!editcustomer"><i class="far fa-pen"></i> </a></span>
                        </span>                                      
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fal fa-mobile"></i> Phone No.:</span>
                            <span>@{{pickedCus.cus_mobile}} </span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fal fa-at"></i> E-Mail:</span>
                            <span>@{{pickedCus.cus_mail}} </span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fab fa-amazon-pay"></i> Payment:</span>
                            <span>@{{pickedCus.payment.payment_desc}} </span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="far fa-user-tie"></i> Registered By:</span>
                            <span>@{{pickedCus.registered_by.name}} </span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-home"></i> Address:</span>
                            <span>@{{pickedCus.cus_address}} </span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fal fa-calendar-check"></i> Date Registered:</span>
                            <span>@{{pickedCus.created_at}} </span>
                      </li>
                    
                    </ul>
            </div>
            <div class="tab-pane fade " id="credit">
                <div class="row col-md-8">
                    <ul class="list-group list-group-flush col-md-6">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Available Credit</span>
                        <span class="badge badge-primary badge-pill size9">@{{pickedCus.credit.available_credit | currency:'':2}} </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Outstanding Credit</span>
                        <span class="badge badge-primary badge-pill size9">@{{pickedCus.credit.out_credit | currency:'':2}}</span>
                        </li>
                    </ul>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="custom-select custom-select-sm size10" ng-options="x for (x,y) in creditTypes"  ng-model="$parent.creditType">
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="number" class="form-control btn-sm size10" placeholder="Amount" ng-model="$parent.creditAmt"> 
                        </div>
                        <div class="form-group">
                            <button type="button" ng-disabled="creditAmt== null || creditAmt <= 0 || (creditType == 'DEBIT' && creditAmt > pickedCus.credit.available_credit)" ng-click="creditProcess()" class=" size9 btn btn-outline-danger btn-sm float-right"><i class="fal fa-cogs"></i> Process</button> 
                        </div>
                    </div>
                </div><hr>
                <div class="row col-md-10">
                    <table class="table table-hover table-sm" >
                        <thead>
                            <tr class="table-light">
                                <th>Order No.</th>
                                <th>Unit </th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Order Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="x in creditOrders">
                                <td>@{{x.order.order_no}} </td>
                                <td>@{{x.order.qty.qty_desc}} </td>
                                <td>@{{x.order.order_total_qty | number}} </td>
                                <td>@{{x.order.order_total_amount | currency:'':2}} </td>
                                <td>@{{x.order.order_status}}</td>
                                <td>
                                    <span ng-if="x.credit_order_status=='PAID'" class="badge badge-success badge-pill">@{{x.credit_order_status}}</span>
                                    <span ng-if="x.credit_order_status=='OUTSTANDING'" class="badge badge-danger badge-pill">@{{x.credit_order_status}}</span>
                                </td>
                                <td>@{{x.order.order_date}} </td>
                                <td><a ng-if="x.credit_order_status=='OUTSTANDING'" href="" ng-click="payDebit(x.order.order_total_amount,x.credit_order_id)"  title="Clear Debt"><i class="far fa-hand-holding-box"></i></a> </td>
                            </tr>
                        </tbody>
                    </table>
                    <div ng-if="creditOrders.length==0" class="alert alert-light col-md-4 offset-md-4">
                            <span >No Data Available!</span>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="discount">
                <div class="row">
                    <div class="col-md-4">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Discount Balance</span>
                                    <span class="badge badge-primary badge-pill size9">@{{pickedCus.discount.discount_credit | currency:'':2}}</span>
                                </li>
                            </ul><br>
                            <div>
                                <div class="d-flex justify-content-between">
                                    <div class="input-group col-md-8">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text size10"><i class="far fa-money-bill-alt"></i></span>
                                        </span>
                                        <input type="number" class="form-control btn-sm size10" placeholder="Amount" ng-model="$parent.disAmount">
                                    </div>
                                    <button type="button" ng-disabled="disAmount <= 0 || disAmount == null || disAmount > pickedCus.discount.discount_credit" ng-click="payDiscount()" class="btn btn-outline-success btn-sm size10 col-md-4"> Pay Discount</button>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-8">
                        <span class="size10"><i class="far fa-layer-plus"></i> Add Discount Item</span><hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="custom-select custom-select-sm size10" ng-options="x.qty_desc for x in disDatas.qtyTypes"  ng-model="$parent.disQtyType">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="number" class="form-control btn-sm size10" placeholder="Quantity" ng-model="$parent.discountQty"> 
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button class="btn btn-outline-secondary btn-sm size10" ng-disabled="disItem == null || discountQty == null || discountQty <= 0 || discountAmt == null || discountAmt <= 0" ng-click="addDiscount()">Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="custom-select custom-select-sm size10" ng-options="x.item_name group by x.cat_desc for x in disItemsTypes"  ng-model="$parent.disItem">
                                        <option value="">Select Item</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="number" class="form-control btn-sm size10" placeholder="Amount" ng-model="$parent.discountAmt"> 
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div><hr>
                <div class="row col-md-10">
                    <table class="table table-hover table-sm" >
                            <thead>
                                <tr class="table-light">
                                    <th>Item</th>
                                    <th>Unit </th>
                                    <th>Quantity</th>
                                    <th>Discount Amount</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="x in fetchedDisItems" class="pointer delbtn">
                                    <td>@{{x.item.item_name}} </td>
                                    <td>@{{x.unit.qty_desc}} </td>
                                    <td>@{{x.item_qty}} </td>
                                    <td>@{{x.discount_amount}} </td>
                                    <td>@{{x.enabled_date}} </td>
                                    <td><a href='' ng-click="delDiscount(x)" class="delbtn"><span class="text-danger "><i class="far fa-trash-alt"></i><span></a></td>
                                </tr>
                            </tbody>
                    </table>
                    <div ng-if="fetchedDisItems.length==0" class="alert alert-light col-md-4 offset-md-4">
                            <span >No Data Available!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

@endsection