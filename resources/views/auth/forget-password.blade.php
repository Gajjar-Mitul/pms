@extends('auth.layout.app')

@section('meta')
@endsection

@section('title')
    Forget Password
@endsection

@section('styles')
@endsection

@section('content')
    <div class="content mt-5">
        <div class="brand">
            <a class="link" href="javascript:void(0);">{{ _site_title() }}</a>
        </div>
        <form id="forgot-form" action="{{ route('password.forget') }}" method="post">
            @csrf
            <h3 class="m-t-10 m-b-10">Forgot password</h3>
            <p class="m-b-20">Enter your email address below and we'll send you password reset instructions.</p>
            <div class="form-group">
                <input class="form-control" type="email" name="email" placeholder="Email" autocomplete="off">
            </div>
            <div class="form-group">
                <button class="btn btn-info btn-block" type="submit">Submit</button>
                <p class="mt-2 mb-3 text-center">- OR -</p>
                <a href="{{ route('login') }}" class="btn btn-info btn-block">Login</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#forgot-form').validate({
                errorClass: "help-block",
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                },
                highlight: function(e) {
                    $(e).closest(".form-group").addClass("has-error")
                },
                unhighlight: function(e) {
                    $(e).closest(".form-group").removeClass("has-error")
                },
            });
        });
    </script>
@endsection
