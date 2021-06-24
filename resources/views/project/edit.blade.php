@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Product
@endsection

@section('styles')
@endsection

@section('content')
    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Edit Product</div>
                    </div>
                    <div class="ibox-body">
                        <form name="form" action="{{ route('products.update') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="id" value="{{ $data->id }}">
                            
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ @old('name', $data->name) }}" placeholder="Plese enter name" />
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="text" name="quantity" id="quantity" class="form-control digits" value="{{ @old('quantity', $data->quantity) }}" placeholder="Plese enter quantity" />
                                    <span class="kt-form__help error quantity"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="unit">Unit <span class="text-danger">*</span></label>
                                    <input type="text" name="unit" id="unit" class="form-control" value="{{ @old('unit', $data->unit) }}" placeholder="Plese enter unit" />
                                    <span class="kt-form__help error unit"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="color">Color <span class="text-danger">*</span></label>
                                    <input type="text" name="color" id="color" class="form-control" value="{{ @old('color', $data->color) }}" placeholder="Plese enter color" />
                                    <span class="kt-form__help error color"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <input type="text" name="price" id="price" class="form-control digits" value="{{ @old('price', $data->price) }}" placeholder="Plese enter price" />
                                    <span class="kt-form__help error price"></span>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="note">Note <span class="text-danger"></span></label>
                                    <input type="text" name="note" id="note" class="form-control" value="{{ @old('note', $data->note) }}" placeholder="Plese enter note" />
                                    <span class="kt-form__help error note"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('products') }}" class="btn btn-default">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.digits').keyup(function(e){
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

