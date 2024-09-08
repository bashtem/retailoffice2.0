@extends('layouts.stock_keeper')

@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="history" >
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
            <li class="breadcrumb-item active">
            <a href="#" class="text-muted" id="title">
            <i class="fa fa-history" ></i>
            <span class="nav-link-text"> History</span> </a>
            </li>                
        </ol>
        <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#stockout">Stock Out</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link " data-toggle="tab" href="#stocktransfer">Stock Transfer</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link " data-toggle="tab" href="#purchased">Purchased</a>
                </li>
        </ul><br>
        <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade show active" id="stockout">
                <div class="form-group col-md-2">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                            </div>
                            <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" class="form-control form-control-sm size10" ng-model="salesDateHis" aria-label="">
                            <div class="input-group-append">
                                        <span class="input-group-text text-muted size10"> <a href="#" ng-click="salesHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                            </div>
                        </div>
                </div>
        </div>
        <div class="tab-pane fade " id="stocktransfer">
                <div class="form-group col-md-2">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                            </div>
                            <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" class="form-control form-control-sm size10" ng-model="salesDateHis" aria-label="">
                            <div class="input-group-append">
                                        <span class="input-group-text text-muted size10"> <a href="#" ng-click="salesHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                            </div>
                        </div>
                </div>
        </div>
        <div class="tab-pane fade " id="purchased">
                <div class="form-group col-md-2">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text text-muted size10"><i class="far fa-calendar-alt"></i> </span>
                            </div>
                            <input type="text" uib-datepicker-popup myDate  ng-click="showDate()" is-open="uiOpen" class="form-control form-control-sm size10" ng-model="salesDateHis" aria-label="">
                            <div class="input-group-append">
                                        <span class="input-group-text text-muted size10"> <a href="#" ng-click="salesHistory()" title="Search"><i class="fa fa-search"></i> </a></span>
                            </div>
                        </div>
                </div>
        </div>
        </div>

    </div>
@endsection