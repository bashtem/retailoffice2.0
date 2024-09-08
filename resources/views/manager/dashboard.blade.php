@extends('layouts.manager')

@section('contentWrapper')
    <div class="container-fluid size12" ng-controller="dashboard">
        <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 d-flex justify-content-between ">
            <li class="breadcrumb-item active">     
                <a href="#" class="text-muted" id="title">
                    <i class="fas  fa-tachometer-alt" ></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>                
            <span >     
                <a href="#" class="text-muted" id="title">
                    <i class="far fa-calendar-alt" ></i>
                    <span class="nav-link-text">@{{todayDate | date}} </span>
                </a>
            </span>                
        </ol>
        <div class="col-md-12 text-muted">
            <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-primary o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                            <i class="fa fa-fw fa-money-bill-alt"></i>
                            </div>
                            <div class="mr-5">@{{d.salesAmount | currency:'':2}} </div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="#">
                            <span class="float-left">Total Sales Amount</span>
                            <span class="float-right">
                            {{-- <i class="fa fa-angle-right"></i> --}}
                            </span>
                        </a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-success o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                            <i class="fa fa-fw fa-shopping-cart"></i>
                            </div>
                            <div class="mr-5">@{{d.salesCount}} </div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="#">
                            <span class="float-left">Total Sales Order</span>
                            <span class="float-right">
                            {{-- <i class="fa fa-angle-right"></i> --}}
                            </span>
                        </a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                        <div class="card text-white bg-danger o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                            <i class="fa fa-fw fa-exchange-alt"></i>
                            </div>
                            <div class="mr-5">@{{d.transferCount}} </div>
                        </div>
                        <a class="card-footer text-white clearfix small z-1" href="#">
                            <span class="float-left">Total Transfer</span>
                            <span class="float-right">
                            {{-- <i class="fa fa-angle-right"></i> --}}
                            </span>
                        </a>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card text-white bg-warning o-hidden h-100">
                            <div class="card-body">
                                <div class="card-body-icon">
                                <i class="fa fa-fw fa-list"></i>
                                </div>
                                <div class="mr-5">@{{d.purchaseCount}} </div>
                            </div>
                            <a class="card-footer text-white clearfix small z-1" href="#">
                                <span class="float-left">Total Purchase Order</span>
                                <span class="float-right">
                                {{-- <i class="fa fa-angle-right"></i> --}}
                                </span>
                            </a>
                            </div>
                    </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                        <div class="form-group">
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text size10"><i class="fal fa-weight"></i></span>
                                </div>
                                <select  class="form-control custom-select custom-select-sm col-md-2 size10" ng-options="x.qty_desc for x in qtyTypes" ng-model="qtyTypeP" ng-change="qtyChange()">
                                </select>
                            </div>
                        </div>
                        <div ng-if="qtyChartLoad==false" class="text-center"><i class="fal fa-spinner-third fa-spin fa-3x"></i></div>
                        <canvas id="qtyChart" ></canvas>

                </div>
                <div class="col-md-3 size9">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="far fa-users"></i> Suppliers</span>
                            <span class="badge badge-primary badge-pill">@{{usersCount.suppliers}} </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="far fa-user-friends"></i> Customers</span>
                            <span class="badge badge-primary badge-pill">@{{usersCount.customers}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="far fa-user-tie"></i> Managers</span>
                            <span class="badge badge-primary badge-pill">@{{usersCount.managers}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="far fa-user"></i> Users</span>
                            <span class="badge badge-primary badge-pill">@{{usersCount.users}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="far fa-cubes"></i> Items</span>
                            <span class="badge badge-primary badge-pill">@{{usersCount.items}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection