<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" ng-app="retailOffice" >
<head >
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}" ng-model="csrf">
<title>Retail Office</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="#">
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/custom.css")}} />
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/jquery.skeleton.css")}} />
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/animate.css")}} />
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/bootstrap.min.css")}} />
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/sb-admin.css")}}>       
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/select2.css")}} /> 
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/datatables.min.css")}}>       
<link rel="stylesheet" type="text/css" media="screen" href={{url("/css/pretty-checkbox.min.css")}} /> 
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-177459834-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-177459834-1');
</script>
<script src={{url("/js/jquery-1.12.3.js")}} ></script>
<script src={{url("/js/angular.min.js")}} ></script>
<script src={{url("/js/lodash.js")}} ></script>

</head>

<body class="fixed-nav sticky-footer bg-light" >
   

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top p-0" id="mainNav">
    <a class="navbar-brand text-center" href="#"><img src={{url("img/logo.svg")}}  style="width:50px; height:50px" class="rounded-circle img-thumbnail mb-2" ></a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse size10" id="navbarResponsive">

            @yield('nav')

            <ul class="navbar-nav sidenav-toggler" style="font-size:initial">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                            <i class="fa fa-fw fa-angle-left "></i>
                    </a>
                </li>
            </ul>
    </div>
</nav>

    
<div class="content-wrapper">

    @yield('contentWrapper')

    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center size12 text-muted">
                Retail Office &copy; <?php echo date('Y') ?>                   
            </div> 
        </div>
    </footer>
</div>
                       
  
    <!-- Logout Modal-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title size13" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body size12">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer ">
            <button class="btn btn-outline-secondary btn-sm size11 " type="button" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
            <a class="btn btn-outline-danger btn-sm size11" href="{{route("logout")}} "> <i class="fa fa-sign-out-alt"></i> Logout</a>
        </div>
        </div>
    </div>
</div>


    <!--Notification Modal Templates-->
<div id="modalNotify" class="modal modal-message modal-info fade " style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                        <div class="col-md-6 offset-md-3"><i class="far fa-bell"></i></div>
            </div>
            <div class="modal-body size11" id="modalMsg"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info form-control btn-sm size9" data-dismiss="modal"><i class="far fa-thumbs-up"></i> OK</button>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<!--End Notification Modal Templates-->


<!--Processing Modal Templates-->
<div id="modalP" class="modal modal-message modal-info fade " style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header "></div>
            <div class="modal-body size11" >
                <div class="alert alert-light" id="modalMsgP"></div>
            </div>
        </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
</div>
<!--End Notification Modal Templates-->


@include('layouts.notification')
<script src={{url("/js/jquery.scheletrone.js")}} ></script> 
<script src={{url("/js/Chart.bundle.min.js")}} ></script> 
<script src={{url("/js/angular-animate.min.js")}} ></script> 
<script src={{url("/js/angular-datatables.min.js")}} ></script> 
<script src={{url("/js/datatables.min.js")}} ></script>
<script src={{url("/js/ui-bootstrap-tpls-2.5.0.js")}} ></script> 
<script src={{url("/js/select2.js")}} ></script> 
<script src={{url("/js/angular-ui-select2.js")}} ></script> 
<script  src={{url("/js/popper.js")}} ></script>       
<script src={{url("/js/bootstrap.min.js")}} ></script>         
<script src={{url("/js/jquery.easing.min.js")}} ></script>
<script src={{url("/js/fontawesome-all.min.js")}} ></script>        
<script src={{url("/js/sb-admin.min.js")}} ></script>
<script  src={{url("/js/ajax.js")}}  ></script>
<script src={{url("/js/angular-route.min.js")}} ></script>
<script  src={{url("/js/ngapp.js")}} ></script>

</body>
</html>