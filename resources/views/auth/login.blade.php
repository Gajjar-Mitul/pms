@extends('auth.layout.app')

@section('meta')
@endsection

@section('title')
    Login
@endsection

@section('styles')
@endsection

@section('content')
    <div class="brand">
        <a class="link" href="{{ route('dashboard') }}">{{ _site_title() }}</a>
    </div>
    <form id="form" action="{{ route('signin') }}" method="post">
        @csrf
        @method('post')

        <h2 class="login-title">Log in</h2>
        <div class="form-group">
            <div class="input-group-icon right">
                <div class="input-icon"><i class="fa fa-envelope"></i></div>
                <input type="text" name="email" class="form-control" placeholder="Email Or Phone">
                @error('email')
                    <div class="invalid-feedback" style="display: block;">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <div class="input-group-icon right">
                <div class="input-icon"><i class="fa fa-lock font-16"></i></div>
                <input type="password" name="password" class="form-control" placeholder="Password">
                @error('password')
                    <div class="invalid-feedback" style="display: block;">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group d-flex justify-content-between">
            <a href="{{ route('forget.password') }}">Forgot password?</a>
        </div>
        <div class="form-group">
            <button class="btn btn-info btn-block" type="submit">Login</button>
        </div>
    </form>
@endsection

@section('scripts')
@endsection