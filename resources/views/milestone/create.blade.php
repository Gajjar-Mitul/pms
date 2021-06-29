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
                        <form name="form" action="{{ route('milestones.insert') }}" id="form" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ base64_decode($id) }}">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" placeholder="Please Enter Name" class="form-control">
                                    </select>
                                    <span class="kt-form__help error name"></span>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                    <input type="text" name="amount" id="amount" class="form-control digit" placeholder="Plese enter amount" />
                                    <span class="kt-form__help error amount"></span>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="deadline">Deadline <span class="text-danger">*</span></label>
                                    <input type="text" name="deadline" id="deadline" class="form-control" placeholder="Plese enter deadline" />
                                    <span class="kt-form__help error deadline"></span>
                                </div>
                                <div class="form-group col-sm-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-md btn-primary mt-4" id="add_product">Add MileStone</button>
                                </div>
                            </div>
                            <div class="row" id="table" style="display:none">
                                <div class="col-sm-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Sr. No</th>
                                                <th style="width:30%">Name</th>
                                                <th style="width:25%">Amount</th>
                                                <th style="width:25%">Deadline</th>
                                                <th style="width:10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('milestones' ,['id' => base64_encode($id)]) }}" class="btn btn-default">Back</a>
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
            $('.digit').keyup(function(e){
                if (/\D/g.test(this.value)){
                    this.value = this.value.replace(/\D/g, '');
                }
            });
        });

        let name = '';
        let amount = '';
        let deadline = '';

         $('#add_product').click(function(){
            $('#table').css('display', 'block');

            name = $('#name').val();
            amount = $('#amount').val();
            deadline = $('#deadline').val();

            $('#name').val('');
            $('#amount').val('');
            $('#deadline').val('');

            var regex = /^(.+?)(\d+)$/i;
            var cloneIndex = $("#table tbody tr").length;

            if(cloneIndex !== 0){
                let num = parseInt(cloneIndex) + 1;

                var clone = clone_div(num);
                $("#table tbody").append(clone);
            }else{
                var clone = clone_div(1);
                $("#table tbody").append(clone);
            }
        });

         function clone_div(id){
            return '<tr class="clone" id="clone_'+id+'">'+
                    '<th style="width:10%">'+id+'</th>'+
                    '<th style="width:30%">'+name+
                        '<input type="hidden" name="name[]" id="name'+id+'" value="'+name+'">'+
                    '</th>'+
                    '<th style="width:25%">'+
                        '<input type="text" name="amount[]" id="quantity_'+id+'" value="'+amount+'" class="form-control digit" required>'+
                    '</th>'+
                    '<th style="width:25%">'+
                        '<input type="text" name="deadline[]" id="price_'+id+'" value="'+deadline+'" class="form-control" required>'+
                    '</th>'+
                    '<th style="width:10%">'+
                        '<button type="button" class="btn btn-danger delete" data-id="'+id+'">Remove</button>'+
                    '</th>'+
                '</tr>';
        }

        function clone_div(id){
            return '<tr class="clone" id="clone_'+id+'">'+
                    '<th style="width:10%">'+id+'</th>'+
                    '<th style="width:30%">'+name+
                        '<input type="hidden" name="name[]" id="name_'+id+'" value="'+name+'">'+
                    '</th>'+
                    '<th style="width:25%">'+
                        '<input type="text" name="amount[]" id="amount_'+id+'" value="'+amount+'" class="form-control digit" required>'+
                    '</th>'+
                    '<th style="width:25%">'+
                        '<input type="text" name="deadline[]" id="deadline_'+id+'" value="'+deadline+'" class="form-control" required>'+
                    '</th>'+
                    '<th style="width:10%">'+
                        '<button type="button" class="btn btn-danger delete" data-id="'+id+'">Remove</button>'+
                    '</th>'+
                '</tr>';
        }

        $(document).on('click', ".delete", function () {
            let id = $(this).data('id');

            let con = confirm('Are you sure to delete?');
            if (con) {
                $('#clone_'+id).remove();
            }
        })
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

