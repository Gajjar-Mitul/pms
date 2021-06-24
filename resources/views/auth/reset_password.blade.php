@extends('auth.layout.app')

@section('meta')
@endsection

@section('title')
    Reset Password
@endsection

@section('styles')
@endsection

@section('content')
    <div class="content mt-5">
        <div class="brand">
            <a class="link" href="javascript:void(0);">{{ _site_title() }}</a>
        </div>

        <form id="form" action="{{ route('recover.password') }}" method="post">
            @csrf
            @method('post')
            <input type="hidden" name="token" value="{{ $string }}">

            <h2 class="login-title">Reset Password</h2>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="fa fa-envelope"></i></div>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" autocomplete="off" value="{{ $email }}">
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
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                    @error('password')
                        <div class="invalid-feedback" style="display: block;">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="fa fa-lock font-16"></i></div>
                    <input type="confirm_password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password">
                    @error('confirm_password')
                        <div class="invalid-feedback" style="display: block;">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-info btn-block" type="submit">Reset Password</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            // debugger;
            $("#form").validate({
                errorElement: "div", // contain the error msg in a span tag
                errorClass: 'invalid-feedback',
                errorPlacement: function (error, element) { // render error placement for each input type
                    // debugger;
                    error.insertAfter(element);
                    // for other inputs, just perform default behavior
                },
                ignore: "",
                rules: {
                    email: {
                        required: true,
                        email: true,
                        normalizer: function(value) {
                            this.value = $.trim(value);
                            return this.value;
                        }
                    },
                    password: {
                        required: true,
                        minlength : 7
                    },
                    confirm_password: {
                        required: true,
                        minlength : 7,
                        equalTo : "#password"
                    }
                },
                messages: {
                    email: {
                        required: 'please enter email',
                        email: 'please enter valid email'
                    },
                    password: {
                        required: 'Please enter new password.',
                        minlength: 'Password length should be 7 character'
                    },
                    confirm_password: {
                        required: 'Please enter confirm password.',
                        minlength: 'Password length should be 7 character',
                        equalTo: "confirm password must be same as new password"
                    }
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    // debugger;
                    //successHandler1.hide();
                    //errorHandler1.show();
                },
                highlight: function (element) {
                    // debugger;
                    $(element).closest('.help-block').removeClass('valid');
                    // display OK icon
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
                    // add the Bootstrap error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    // debugger;
                    $(element).closest('.form-group').removeClass('has-error');
                    // set error class to the control group
                },

                success: function (label, element) {
                    // debugger;
                    label.addClass('help-block valid');
                    // mark the current input as valid and display OK icon
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
                }
                // submitHandler: function (frmadd) {
                //     // debugger;
                //     successHandler1.show();
                //     errorHandler1.hide();
                // }
            });
        });
    </script>
@endsection
