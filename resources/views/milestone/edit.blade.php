@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create MileStones
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
                        <div class="ibox-title">Create MileStones</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('milestones.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            <input type="hidden" name="project_id" value="{{ $data->project_id }}">
                            <div class="row">
                                
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" placeholder="Please Enter Name" class="form-control" value="{{ $data->name ??'' }}">
                                    </select>
                                    <span class="kt-form__help error name"></span>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea ype="text" name="description" id="description" class="form-control" placeholder="Plese enter description">{{ $data->description??'' }}</textarea>
                                    <span class="kt-form__help error description"></span>
                                </div>
                                
                                <div class="form-group col-sm-6">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                    <input type="text" name="amount" id="amount" class="form-control digit" placeholder="Plese enter amount" value="{{ $data->amount ??'' }}"/>
                                    <span class="kt-form__help error amount"></span>
                                </div>
                                
                                <div class="form-group col-sm-6" id="date_1">
                                    <label for="deadline">DeadLine <span class="text-danger">*</span></label>
                                    <div class="input-group date">
                                        <span class="input-group-addon bg-white"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="deadline" name="deadline" type="text" value="{{ $data->deadline ??Date('Y-m-d') }}">
                                    </div>
                                    <span class="kt-form__help error deadline"></span>
                                </div>
                                
                            </div>
                                
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('milestones' ,['id' => base64_encode($data->project_id)]) }}" class="btn btn-default">Back</a>
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
        // Bootstrap datepicker
            $('#date_1 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: 'yyyy-mm-dd'
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

