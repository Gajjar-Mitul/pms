@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Project
@endsection

@section('styles')
    <link href="{{ asset('/assets/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">View Project</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ $data->title ?? '' }}" disabled />

                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="client_name">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" name="client_name" id="client_name" class="form-control digits" placeholder="Plese enter Client Name" value="{{ $data->client_name ?? '' }}" disabled="" />
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea type="text" name="description" id="description" class="form-control" placeholder="Plese enter description" disabled="">{{ $data->description ??'' }}</textarea>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="budget">Budget <span class="text-danger">*</span></label>
                                    <input type="text" name="budget" id="budget" class="form-control" placeholder="Plese enter budget" value="{{ $data->budget ?? '' }}" disabled="" />

                                </div>
                                <div class="form-group col-sm-6" id="date_1">
                                    <label for="deadline">DeadLine <span class="text-danger">*</span></label>
                                    <div class="input-group date">
                                        <span class="input-group-addon bg-white"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" name="deadline" type="text" value="{{ $data->deadline ?? '' }}" disabled="">
                                    </div>
                                    <span class="kt-form__help error deadline"></span>
                                </div>
                              
                            </div>
                            <div class="form-group">
                                <a href="{{ route('projects') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.budget').keyup(function(e){
                if (/\D/g.test(this.value)){
                    this.value = this.value.replace(/\D/g, '');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            var form = $('#form');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(json){
                        return true;
                    },
                    error: function(json){
                        if(json.status === 422) {
                            e.preventDefault();
                            var errors_ = json.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

