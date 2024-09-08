@extends('layouts.login')

@section('content')
    <div class="card border-light">
        <div class="card-header size11 text-muted"><i class="far fa-sign-in-alt"> </i>  Login</div>
        <div class="card-body ">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->has('username'))
                    <div class="invalid-feedback col-md-12 text-center" style="display: block">
                        <small class="">{{ $errors->first('username') }}</small>
                    </div>
                @endif

                <div class="form-group row">
                    <div class="input-group mb-3 col-md-6 offset-md-3">
                        <div class="input-group-prepend ">
                        <span class="input-group-text size11"><i class="fal fa-user-circle"></i></span>
                        </div>
                        <input id="username" type="text" class=" btn-sm form-control {{ $errors->has('username') ? ' is-invalid' : '' }} size10" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="input-group mb-3 col-md-6 offset-md-3">
                        <div class="input-group-prepend ">
                        <span class="input-group-text size11"><i class="fal fa-key"></i></span>
                        </div>
                        <input id="password" type="password" class="btn-sm form-control{{ $errors->has('password') ? ' is-invalid' : '' }} size10" name="password" placeholder="****************" required>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-3">
                        <div class="col-md-6 offset-md-3">
                            <button type="submit" class="btn btn-sm btn-outline-primary size10"><i class="far fa-sign-in"> </i> 
                                    {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-3 text-center">
                        {{-- <div class="col-md-6 offset-md-3"> --}}
                                <a class="btn btn-link size10 " href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                </a>
                        {{-- </div> --}}
                    </div>
                </div>
                
            </form>
        </div>
    </div>
@endsection
