<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Retail Office</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/custom.css")}} />
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/animate.css")}} />
    <link rel="stylesheet" type="text/css" media="screen" href={{asset("/css/bootstrap.min.css")}} />
</head>
<body style="background-image: url({{asset('/img/background.jpg')}} )">
    <div class="container-fluid ">
        <div class="row justify-content-center ">
            <div class="col-md-4 mt-5">
                <div class="text-center"><img src={{asset("img/logo.svg")}}  style="width:50px; height:50px" class="rounded-circle img-thumbnail mb-2" ></div>
                @yield('content')
            </div>
        </div>


    <div class="row">
            <nav class="navbar fixed-bottom navbar-expand navbar-light bg-light justify-content-between">                
                    <div class="col-md-6 offset-md-5 size12 text-muted">
                            Retail Office &copy; <?php echo date('Y') ?>                   
                    </div>                      
            </nav>
    </div>     

    
    <script src={{asset("/js/jquery-1.12.3.js")}} ></script>    
    <script src={{asset("/js/bootstrap.min.js")}} ></script> 
    <script src={{asset("/js/fontawesome-all.min.js")}} ></script>
</body>
</html>

