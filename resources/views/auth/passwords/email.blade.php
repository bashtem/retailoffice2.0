@extends('layouts.login')

@section('content')
<div class="card border-light">
        <div class="card-header size11 text-muted"><i class="far fa-redo"> </i>  {{ __('Reset Password') }} </div>
        <div class="card-body ">
            <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                @if ($errors->has('email'))
                    <div class="invalid-feedback col-md-12 text-center" style="display: block">
                        <small class="">{{ $errors->first('email') }}</small>
                    </div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success text-center size11">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="form-group row">
                    <div class="input-group mb-3 col-md-12 ">
                        <div class="input-group-prepend ">
                        <span class="input-group-text size11"><i class="fal fa-envelope"></i></span>
                        </div>
                        <input id="email" type="email" placeholder="E-Mail Address" class="btn-sm form-control{{ $errors->has('email') ? ' is-invalid' : '' }} size11" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-3">
                        {{-- <div class="col-md-6 offset-md-3"> --}}
                            <button type="submit" class="btn btn-sm btn-outline-primary size10"><i class="far fa-redo"> </i> 
                                    {{ __('Send Password Reset Link') }}
                            </button>
                        {{-- </div> --}}
                    </div>
                </div>
                
                
            </form>
        </div>
</div>
@endsection
