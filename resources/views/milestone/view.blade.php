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
                        <form name="form" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" placeholder="Please Enter Name" class="form-control" value="{{ $data->name ??'' }}" disabled>
                                    </select>
                                    <span class="kt-form__help error name"></span>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea ype="text" name="description" id="description" class="form-control" placeholder="Plese enter description" disabled>{{ $data->description??'' }}</textarea>
                                    <span class="kt-form__help error description"></span>
                                </div>
                                
                                <div class="form-group col-sm-6">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                    <input type="text" name="amount" id="amount" class="form-control digit" placeholder="Plese enter amount" value="{{ $data->amount ??'' }}" disabled/>
                                    <span class="kt-form__help error amount"></span>
                                </div>
                                
                                <div class="form-group col-sm-6" id="date_1">
                                    <label for="deadline">DeadLine <span class="text-danger">*</span></label>
                                    <div class="input-group date">
                                        <span class="input-group-addon bg-white"><i class="fa fa-calendar"></i></span>
                                        <input class="form-control" id="deadline" name="deadline" type="text" value="{{ $data->deadline ??Date('Y-m-d') }}" disabled>
                                    </div>
                                    <span class="kt-form__help error deadline"></span>
                                </div>
                                
                            </div>
                                
                            <div class="form-group">
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
@endsection

