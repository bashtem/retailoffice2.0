@extends('layouts.manager')
@section('contentWrapper')
    <div class="container-fluid size10" ng-controller="manageUsers">
            <!-- Breadcrumbs-->
        <ol class="breadcrumb size11 ">
            <li class="breadcrumb-item active">     
            <a href="#" class="text-muted" >
            <i class="fas fa-users-cog" ></i>
            <span class="nav-link-text">Manage Users</span> </a>
            </li>                
        </ol>
        <div class="col-md-12 text-muted">
            <div class="row">
                <div class="col-md-3 ">
                    <a href="#!adduser" class=" size9 btn-sm  btn-outline-primary col-md-4"><i class="far fa-user-plus"></i> Add User</a><br><br>
                    <table class="table table-hover " >
                        {{-- <thead>
                          <tr>
                            <th></th>
                          </tr>
                        </thead> --}}
                        <tbody>
                          <tr ng-repeat= "x in usersData" >
                            <td><a href="#!profile" ng-click="pickUser($index)" >@{{x.name}} </a> </td>
                            <td><span class="badge badge-pill badge-success" ng-if="x.status=='ACTIVE'">ACTIVE</span><span class="badge badge-pill badge-danger" ng-if="x.status=='INACTIVE'">INACTIVE</span></td>
                          </tr>
                        </tbody>
                    </table>
                    <div ng-if="usersData.length==0" class="alert alert-light">
                            <h6 class="size11">No Record Found!</h6>
                    </div>
                </div>
                <div class="col-md-9" ng-view>
                        
                </div>
            </div>
        </div>                
    </div>

    <script type="text/ng-template" id="newprofile">
      <div class="col-md-6 text-muted">
          <p><i class="far fa-user-plus"> </i> New User</p><hr>
          <form ng-submit="addUser()">
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="far fa-user"></i> Name</label>
                <div class="col-sm-6">
                  <input type="text"  class="form-control btn-sm size9" ng-model="$parent.name" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"> <i class="far fa-id-badge"></i> User ID</label>
                <div class="col-sm-6">
                  <input type="text"  class="form-control btn-sm size9" ng-model="$parent.id" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-key"></i> Password</label>
                <div class="col-sm-6">
                  <input type="text"  class="form-control btn-sm size9" ng-model="$parent.password" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-mobile"></i> Phone</label>
                <div class="col-sm-6">
                  <input type="tel"  class="form-control btn-sm size9" ng-model="$parent.phone" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-at"></i> E-mail</label>
                <div class="col-sm-6">
                  <input type="email"  class="form-control btn-sm size9" ng-model="$parent.mail" required>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-user-tag"></i> Role</label>
                <div class="col-sm-6">
                    <select class="custom-select custom-select-sm size9" ng-options="x.role_desc for x in userRoles" ng-model="$parent.role" required>
                        <option value="">Select Role</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label  class="col-sm-3 col-form-label"><i class="fal fa-user-clock"></i> Status</label>
                <div class="col-sm-6">
                    <div class="pretty p-switch ">
                        <input type="checkbox" ng-model="$parent.status" ng-init="$parent.status=true" />
                        <div class="state p-success">
                            <label></label>
                        </div>
                    </div>
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

    <script type="text/ng-template" id="editprofile">
      <div class="col-md-6 text-muted">
          <p><i class="far fa-user-edit"> </i> Edit profile</p><hr>
          <form ng-submit="editUser()">
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><i class="far fa-user"></i> Name</label>
                  <div class="col-sm-6">
                    <input type="text"  class="form-control-plaintext size10" ng-value="pickedUser.name" ng-model="$parent.editName" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><i class="far fa-id-badge"></i> User ID</label>
                  <div class="col-sm-6">
                    <input type="text"  class="form-control-plaintext size10" ng-value="pickedUser.username" ng-model="$parent.editID" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><i class="fal fa-key"></i> Password</label>
                  <div class="col-sm-6">
                    <input type="text"  class="form-control-plaintext size10" placeholder="*******" ng-model="$parent.editPassword" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><i class="fal fa-mobile"></i> Phone</label>
                  <div class="col-sm-6">
                    <input type="tel"  class="form-control-plaintext size10" ng-value="pickedUser.phone" ng-model="$parent.editPhone" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"> <i class="fal fa-at"></i> E-mail</label>
                  <div class="col-sm-6">
                    <input type="email"  class="form-control-plaintext size10" ng-value="pickedUser.email" ng-model="$parent.editMail" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"> <i class="fal fa-user-tag"></i> Role</label>
                  <div class="col-sm-6">
                      <select class="custom-select custom-select-sm btn-sm size9" ng-options="x.role_desc for x in userRoles" ng-model="$parent.editRole" required>
                          <option value="">Select Role</option>
                      </select>
                  </div>
              </div>
              <div class="form-group row">
                  <label  class="col-sm-3 col-form-label"><a href="#!profile"> <i class="far fa-long-arrow-left"></i> Back</a></label>
                  <div class="col-sm-6">
                    <button type="submit" class="btn btn-outline-secondary btn-sm size9 float-right"><i class="far fa-save"></i> Save</button>
                  </div>
              </div>
          </form>
      </div>
    </script>

    <script type="text/ng-template" id="profile">
        <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#profile"><i class="far fa-user"></i> Profile</a>
              </li>
              <li class="nav-item">
                {{-- <a class="nav-link" data-toggle="tab" href="#transfers"><i class="fa fa-exchange-alt"></i> Transfers</a> --}}
              </li>
              <li class="nav-item">
                {{-- <a class="nav-link" data-toggle="tab" href="#purchase"><i class="fa fa-truck"></i> Purchase</a> --}}
              </li>
              
              
        </ul><br>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade show active" id="profile">
                <ul class="list-group list-group-flush col-md-6">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <span><i class="far fa-user"></i> Name:</span>
                      <span>
                          <span>@{{pickedUser.name}} </span>
                          <span><a ng-if="pickedUser" href="#!editprofile"><i class="far fa-pen"></i> </a></span>
                      </span>                                      
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="far fa-id-badge"> </i> User ID:</span>
                          <span>@{{pickedUser.username}} </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="fal fa-mobile"></i> Phone No.:</span>
                          <span>@{{pickedUser.phone}} </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="fal fa-at"></i> E-Mail:</span>
                          <span>@{{pickedUser.email}} </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="fal fa-user-tag"></i> Role:</span>
                          <span class="badge badge-pill badge-secondary" >@{{pickedUser.user_role.role_desc}} </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span class="d-flex justify-content-start">
                              <span><i class="fal fa-user-clock"></i> Status:</span>
                          </span>
                          <span>
                              <span class="badge badge-pill badge-success" ng-if="pickedUser.status=='ACTIVE'">ACTIVE</span>   
                              <span class="badge badge-pill badge-danger" ng-if="pickedUser.status=='INACTIVE'">INACTIVE</span>
                              <div class="pretty p-switch ">
                                  <input type="checkbox" ng-model="$parent.statusChk"  ng-change="userStatus(pickedUser.user_id)" />
                                  <div class="state p-success">
                                      <label></label>
                                  </div>
                              </div>  
                          </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><i class="fal fa-calendar-check"></i> Date Added:</span>
                          <span>@{{pickedUser.created_at}} </span>
                    </li>
                </ul>
            </div>

            <div class="tab-pane fade" id="transfers">
                <p>Food truck fixie locavore, accusamus mcsweeney's marfa nulla single-origin coffee squid. Exercitation +1 labore velit, blog sartorial PBR leggings next level wes anderson artisan four loko farm-to-table craft beer twee. Qui photo booth letterpress, commodo enim craft beer mlkshk aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthetic magna delectus mollit.</p>
            </div>
        </div>
    </script>

    <script type="text/ng-template" id="home">
      <div class="text-center col-md-6 text-light"><i class="fal fa-user fa-9x"></i></div>
    </script>

@endsection