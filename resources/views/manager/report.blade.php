@extends('layouts.manager')
@section('contentWrapper')
<div class="container-fluid size10 text-muted" ng-controller="report">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
                <li class="breadcrumb-item active">
                        <a href="#" class="text-muted">
                                <i class="far fa-file-excel"></i>
                                <span class="nav-link-text">Reports</span> </a>
                </li>
        </ol>

        <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade show active" id="dailyReport">
                        <div class="row">

                                <div class="form-group col-md-2">
                                        <label for="fromDate">From:</label>
                                        <div class="input-group mb-3" id="fromDate">
                                                <div class="input-group-prepend">
                                                        <span class="input-group-text text-muted size10"><i
                                                                        class="far fa-calendar-alt"></i> </span>
                                                </div>
                                                <input type="text" uib-datepicker-popup myDate ng-click="showFromDate()"
                                                        is-open="fromUiOpen" class="form-control form-control-sm size10"
                                                        ng-model="fromDate" aria-label="">
                                        </div>
                                </div>
                                <div class="form-group col-md-2">
                                        <label for="toDate">To:</label>
                                        <div class="input-group mb-3" id="toDate">
                                                <div class="input-group-prepend">
                                                        <span class="input-group-text text-muted size10"><i
                                                                        class="far fa-calendar-alt"></i> </span>
                                                </div>
                                                <input type="text" uib-datepicker-popup myDate ng-click="showToDate()"
                                                        is-open="toUiOpen" ng-change="salesReport()"
                                                        class="form-control form-control-sm size10" ng-model="toDate"
                                                        aria-label="">
                                        </div>
                                </div>
                                <div class="form-group col-md-2 size9 mt-4">
                                        <select class="custom-select custom-select-mod" ng-model="selectedReport"
                                                ng-options="x.type for x in reportMenu" ng-change="salesReport()">

                                        </select>
                                </div>
                                <div class="form-group col-md-2 size9 mt-4">
                                        <select class="form-control custom-select custom-select-sm  size10"
                                                ng-options="x.qty_desc for x in qtyTypeList | orderBy: 'qty_id'" ng-model="qtyTypeId" ng-change="salesReport()">
                                                {{-- <option value=""> Unit</option> --}}
                                        </select>
                                </div>
                                <div class="form-group mr-4 mt-4">
                                        <button type="button" ng-click="salesReport()"
                                                class="btn-sm btn btn-outline-primary size9"><i
                                                        class="fa fa-search"></i> Search</button>
                                </div>
                                <div class="form-group mt-4" ng-hide="showLoader">
                                        <button type="button" ng-click="downloadReport()"
                                                class="btn-sm btn btn-outline-info size9"><i
                                                        class="fal fa-download"></i> Download</button>
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
                        </div>
                        <div class="reports" ng-hide="showLoader">
                                <div class="row" ng-show="1 == selectedReport.value">
                                        <div class="col-md-8">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ITEM</th>
                                                                        <th>UNIT </th>
                                                                        <th>UNIT PRICE</th>
                                                                        <th>QTY</th>
                                                                        <th>SALES AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in topSales">
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.qty_desc}} </td>
                                                                        <td>@{{x.price | currency:"":2}} </td>
                                                                        <td>@{{x.totalQty | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                </tr>
                                                        </tbody>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="topSales.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                        <div class="col-md-4">
                                                <h6>Summary</h6>
                                                <table class="table table-sm table-hover">
                                                        <thead>
                                                                <tr>
                                                                        <th>UNIT</th>
                                                                        <th>TOTAL AMOUNT</th>
                                                                        <th>TOTAL QTY.</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in totalSummary">
                                                                        <td>@{{x.unit}} </td>
                                                                        <td class="text-primary">
                                                                                @{{x.totalAmount | currency:"":2}} </td>
                                                                        <td class="text-secondary">
                                                                                @{{x.totalQty | number }} </td>
                                                                </tr>
                                                                <tr>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                </tr>
                                                                <tr>
                                                                        <td class="text-danger font-weight-bold">GRAND
                                                                                TOTAL:</td>
                                                                        <td class="font-weight-bold">
                                                                                @{{grandTotal | currency:"":2}} </td>
                                                                        <td></td>
                                                                </tr>
                                                        </tbody>
                                                </table>
                                        </div>
                                </div>
                                <div class="row" ng-show="2 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ORDER DATE</th>
                                                                        <th>ORDER NO. </th>
                                                                        <th>CUSTOMER</th>
                                                                        <th>CASHIER</th>
                                                                        <th>ITEM</th>
                                                                        <th>UNIT</th>
                                                                        <th>PRICE</th>
                                                                        <th>QTY.</th>
                                                                        <th>AMOUNT</th>
                                                                        <th>COST</th>
                                                                        <th>GROSS PROFIT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in saleItems">
                                                                        <td>@{{x.order_date}} </td>
                                                                        <td>@{{x.order_no}} </td>
                                                                        <td>@{{x.cus_name}} </td>
                                                                        <td>@{{x.name}} </td>
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.qty_desc}} </td>
                                                                        <td>@{{x.price | currency:"":2}} </td>
                                                                        <td>@{{x.quantity | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                        <td>@{{x.cost | currency:"":2}} </td>
                                                                        <td>@{{x.gross_profit | currency:"":2}} </td>
                                                                </tr>

                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th>@{{saleItemsTotals.totalQty | number}} </th>
                                                                        <th>@{{saleItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{saleItemsTotals.totalCost | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{saleItemsTotals.totalGrossProfit | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="3 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ORDER DATE</th>
                                                                        <th>CUSTOMER</th>
                                                                        <th>ORDER NO. </th>
                                                                        <th>CASHIER</th>
                                                                        <th>PAYMENT</th>
                                                                        <th>AMOUNT</th>
                                                                        <th>COST</th>
                                                                        <th>GROSS MARGIN</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in sales">
                                                                        <td>@{{x.order_date}} </td>
                                                                        <td>@{{x.cus_name}} </td>
                                                                        <td>@{{x.order_no}} </td>
                                                                        <td>@{{x.name}} </td>
                                                                        <td>@{{x.payment_desc}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                        <td>@{{x.cost | currency:"":2}} </td>
                                                                        <td>@{{x.gross_margin | currency:"":2}} </td>
                                                                </tr>
                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th>@{{saleItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{saleItemsTotals.totalCost | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{saleItemsTotals.totalGrossProfit | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center" ng-show="sales.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="4 == selectedReport.value">
                                        <div class="col-md-8 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ITEM</th>
                                                                        <th>UNIT</th>
                                                                        <th>PRICE</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>SALE AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in salesItemsByUnitPrice">
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.qty_desc}} </td>
                                                                        <td>@{{x.price | currency:"":2}} </td>
                                                                        <td>@{{x.quantity | number}} </td>
                                                                        <td>@{{x.sale_amount | currency:"":2}} </td>
                                                                </tr>
                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th>@{{saleItemsTotals.totalQty | number}} </th>
                                                                        <th>@{{saleItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="salesItemsByUnitPrice.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="5 == selectedReport.value">
                                        <div class="col-md-8 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ORDER DATE</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>AMOUNT</th>
                                                                        <th>COST</th>
                                                                        <th>GROSS PROFIT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in dailySalesSummary">
                                                                        <td>@{{x.order_date}} </td>
                                                                        <td>@{{x.volume | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                        <td>@{{x.cost | currency:"":2}}</td>
                                                                        <td>@{{x.gross_profit | currency:"":2}}</td>
                                                                </tr>
                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th>@{{saleItemsTotals.totalQty | number}} </th>
                                                                        <th>@{{saleItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{saleItemsTotals.totalCost | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{saleItemsTotals.totalGrossProfit | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="dailySalesSummary.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="6 == selectedReport.value">
                                        <div class="col-md-8 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ORDER DATE</th>
                                                                        <th>ITEM</th>
                                                                        <th>UNIT</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in saleItemsSummary">
                                                                        <td>@{{x.order_date}} </td>
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.qty_desc}} </td>
                                                                        <td>@{{x.volume | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                </tr>
                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th>@{{saleItemsTotals.totalQty | number}} </th>
                                                                        <th>@{{saleItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItemsSummary.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="7 == selectedReport.value">
                                        <div class="col-md-8 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ITEM</th>
                                                                        <th>UNIT</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>COST PRICE</th>
                                                                        <th>SALE PRICE</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in allItems">
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.qty_desc}} </td>
                                                                        <td>@{{x.qty_in_store | number}} </td>
                                                                        <td>@{{x.cost_price | currency:"":2}} </td>
                                                                        <td>@{{x.sale_price | currency:"":2}}</td>
                                                                </tr>
                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th>@{{allItemsTotals.totalQty | number}} </th>
                                                                        <th></th>
                                                                        <th></th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="allItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="8 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ORDER DATE</th>
                                                                        <th>ORDER NO. </th>
                                                                        <th>CUSTOMER</th>
                                                                        <th>CASHIER</th>
                                                                        <th>ITEM</th>
                                                                        <th>UNIT</th>
                                                                        <th>PRICE</th>
                                                                        <th>QTY.</th>
                                                                        <th>AMOUNT</th>
                                                                        <th>NOTE</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in cancledItems">
                                                                        <td>@{{x.order_date}} </td>
                                                                        <td>@{{x.order_no}} </td>
                                                                        <td>@{{x.cus_name}} </td>
                                                                        <td>@{{x.name}} </td>
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.qty_desc}} </td>
                                                                        <td>@{{x.price | currency:"":2}} </td>
                                                                        <td>@{{x.quantity | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                        <td>@{{x.note}} </td>
                                                                </tr>

                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th>@{{cancledItemsTotals.totalQty | number}}
                                                                        </th>
                                                                        <th>@{{cancledItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                        <th></th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="9 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>DATE</th>
                                                                        <th>SUPPLIER </th>
                                                                        <th>PAYMENT</th>
                                                                        <th>ITEM</th>
                                                                        <th>PRICE</th>
                                                                        <th>QTY.</th>
                                                                        <th>AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in purchasedItems">
                                                                        <td>@{{x.purchase_date}} </td>
                                                                        <td>@{{x.sup_company_name}} </td>
                                                                        <td>@{{x.payment_desc}} </td>
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.purchase_price | currency:"":2}} </td>
                                                                        <td>@{{x.quantity | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                </tr>

                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th>@{{purchasedItemsTotals.totalQty | currency:"":2}}
                                                                        </th>
                                                                        <th>@{{purchasedItemsTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="10 == selectedReport.value">
                                        <div class="col-md-8 ">
                                                <div ng-repeat="eachStore in totalDailySummary">
                                                        <h6>@{{eachStore.store}} </h6>
                                                        <table class="table table-hover table-sm table-striped">
                                                                <thead>
                                                                        <tr class="table-light">
                                                                                <th>ORDER DATE</th>
                                                                                <th>QUANTITY</th>
                                                                                <th>AMOUNT</th>
                                                                                <th>COST</th>
                                                                                <th>GROSS PROFIT</th>
                                                                        </tr>
                                                                </thead>
                                                                <tbody>
                                                                        <tr ng-repeat="x in eachStore.data">
                                                                                <td>@{{x.order_date}} </td>
                                                                                <td>@{{x.volume | number}} </td>
                                                                                <td>@{{x.amount | currency:"":2}} </td>
                                                                                <td>@{{x.cost | currency:"":2}}</td>
                                                                                <td>@{{x.gross_profit | currency:"":2}}
                                                                                </td>
                                                                        </tr>
                                                                </tbody>
                                                                <thead>
                                                                        <tr>
                                                                                <th>TOTAL:</th>
                                                                                <th>@{{eachStore.total.totalQty | number}}
                                                                                </th>
                                                                                <th>@{{eachStore.total.totalAmount | currency:"":2}}
                                                                                </th>
                                                                                <th>@{{eachStore.total.totalCost | currency:"":2}}
                                                                                </th>
                                                                                <th>@{{eachStore.total.totalGrossProfit | currency:"":2}}
                                                                                </th>
                                                                        </tr>
                                                                </thead>

                                                                <thead ng-show="$index == totalDailySummary.length-1">
                                                                        <tr>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                        </tr>
                                                                        <tr>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                        </tr>
                                                                </thead>

                                                                <thead ng-show="$index == totalDailySummary.length-1">
                                                                        <tr>
                                                                                <th>GRAND TOTAL</th>
                                                                                <th>@{{dailySummaryGrandTotal.grandTotalQty | number}}
                                                                                </th>
                                                                                <th>@{{dailySummaryGrandTotal.grandTotalAmount | currency:"":2}}
                                                                                </th>
                                                                                <th>@{{dailySummaryGrandTotal.grandTotalCost | currency:"":2}}
                                                                                </th>
                                                                                <th>@{{dailySummaryGrandTotal.grandTotalGrossProfit | currency:"":2}}
                                                                                </th>
                                                                        </tr>
                                                                </thead>
                                                        </table>
                                                </div>

                                                <div class="alert alert-light text-center"
                                                        ng-show="totalDailySummary.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="11 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>CUSTOMER</th>
                                                                        <th>TICKET</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in cusByTicketList">
                                                                        <td>@{{x.name}} </td>
                                                                        <td>@{{x.tickets | number}} </td>
                                                                        <td>@{{x.qty | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                </tr>

                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th>
                                                                                @{{cusByTicketTotals.totalTicket | number}}
                                                                        </th>
                                                                        <th>
                                                                                @{{cusByTicketTotals.totalQty | number}}
                                                                        </th>
                                                                        <th>
                                                                                @{{cusByTicketTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="12 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>CUSTOMER</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in cusByAmountList">
                                                                        <td>@{{x.name}} </td>
                                                                        <td>@{{x.qty | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                </tr>

                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th>
                                                                                @{{cusByAmountTotals.totalQty | number}}
                                                                        </th>
                                                                        <th>
                                                                                @{{cusByAmountTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="13 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>CUSTOMER</th>
                                                                        <th>QUANTITY</th>
                                                                        <th>AMOUNT</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in cusByVolumeList">
                                                                        <td>@{{x.name}} </td>
                                                                        <td>@{{x.qty | number}} </td>
                                                                        <td>@{{x.amount | currency:"":2}} </td>
                                                                </tr>

                                                        </tbody>
                                                        <thead>
                                                                <tr>
                                                                        <th>TOTAL:</th>
                                                                        <th>
                                                                                @{{cusByVolumeTotals.totalQty | number}}
                                                                        </th>
                                                                        <th>
                                                                                @{{cusByVolumeTotals.totalAmount | currency:"":2}}
                                                                        </th>
                                                                </tr>
                                                        </thead>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="saleItems.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                                <div class="row" ng-show="14 == selectedReport.value">
                                        <div class="col-md-10 ">
                                                <table class="table table-hover table-sm table-striped">
                                                        <thead>
                                                                <tr class="table-light">
                                                                        <th>ADMIN</th>
                                                                        <th>ITEM</th>
                                                                        <th>OLD QTY</th>
                                                                        <th>NEW QTY</th>
                                                                        <th>DIFFERENCE</th>
                                                                        <th>DATE</th>
                                                                        <th>TIME</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr ng-repeat="x in inventoryAdjustmentList">
                                                                        <td>@{{x.admin}} </td>
                                                                        <td>@{{x.item_name}} </td>
                                                                        <td>@{{x.old_qty | number}} </td>
                                                                        <td>@{{x.new_qty | number}} </td>
                                                                        <td>@{{x.difference | number}} </td>
                                                                        <td>@{{x.date}} </td>
                                                                        <td>@{{x.time}} </td>
                                                                </tr>

                                                        </tbody>
                                                </table>
                                                <div class="alert alert-light text-center"
                                                        ng-show="inventoryAdjustmentList.length == 0">
                                                        <h6 class="alert-heading"><i class="fa fa-database"></i> No
                                                                Records Available...</h6>
                                                        <p class="mb-0">.</p>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>
@endsection