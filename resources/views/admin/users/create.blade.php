@extends('layouts.admin')

@section('title' , __('user.add_super_admin'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{__('user.super_admin')}}</h1>

            <ol class="breadcrumb">
                <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i>{{__('sidebar.dashboard')}}</a></li>
                <li><a href="{{route('admin.users.index')}}"> {{__('user.super_admin')}}</a></li>
                <li class="active">{{__('user.add')}}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{__('user.add')}}</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    @include('admin.partials._errors')

                    <form id="form_super_admin" method="post">
                        @csrf
                        @method("POST")

                        <div class="form-group">
                            <label>{{__('user.name')}}</label>
                            <input type="text" name="name" class="form-control" value="">
                            <span class="text-danger" id="name_error"></span>
                        </div>



                        <div class="form-group">
                            <label>{{__('user.email')}}</label>
                            <input type="email" name="email" class="form-control" value="">
                            <span class="text-danger" id="email_error"></span>
                        </div>

                        <div class="form-group">
                            <label>{{__('user.photo')}}</label>
                            <input type="file" name="photo" class="form-control image" accept="image/*">
                        </div>

                        <div class="form-group">
                            <img src="{{URL::asset('assets\admin\img\avatar5.png')}}"  style="width: 100px" class="img-thumbnail image-preview" alt="">
                            <span class="text-danger" id="photo_error"></span>
                        </div>

                        <div class="form-group">
                            <label>{{__('user.password')}}</label>
                            <input type="password" name="password" class="form-control">
                            <span class="text-danger" id="password_error"></span>
                        </div>

                        <div class="form-group">
                            <label>{{__('user.password_confirmation')}}</label>
                            <input type="password" name="password_confirmation" id="input_password_confirmation_error" class="form-control">
                            <span class="text-danger" id="password_confirmation_error"></span>
                        </div>

                        @if (Auth::user()->hasRole('super_admin'))
                            <div class="form-group">
                                @php
                                    $models = [ 'users' , 'categories' , 'products' , 'customers' , 'orders' , 'payment' ] ;
                                    $powers = [ 'create' , 'read' , 'update' , 'delete' ] ;
                                @endphp

                                <label>{{__('user.powers')}}</label>
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        @foreach ($models as $key => $model)
                                            <li class="{{ ( $key == 0 ) ? 'active' : '' }}"><a href="#{{$model}}" data-toggle="tab">{{__('user.'. $model)}}</a></li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content">
                                        @foreach ($models as $key => $model)
                                            <div class="tab-pane {{ ( $key == 0 ) ? 'active' : '' }}" id="{{$model}}" >
                                                @foreach ($powers as $power)
                                                    <label><input type="checkbox" name="{{$model}}[]" value="{{$model . '-' .$power}}"> {{__('user.' . $power)}}</label>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <button id="save_super_admin" class="btn btn-primary"><i class="fa fa-plus"></i> {{__('user.save')}} </button>
                        </div>


                        <h3 id="please_wait" style="display:none; text-align:center">
                            {{__('user.please_wait')}}
                        </h3>

                        <div class="alert alert-danger" role="alert" id="fail_message_add_new_admin" style="display:none; text-align:center;">
                            {{__('user.fail_add_super_admin')}}
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@stop


@section('scripts')

<script>
    $(document).on('click' , '#save_super_admin' , function(e){

        e.preventDefault() ;

        $("#please_wait").show() ;
        $("#name_error").text("") ;
        $("#email_error").text("") ;
        $("#photo_error").text("") ;
        $("#password_error").text("") ;
        $("#password_confirmation_error").text("") ;
        $("#fail_message_add_new_admin").hide() ;

        var form_super_admin = new FormData($('#form_super_admin')[0]) ;

        $.ajax({
            type : 'POST' ,
            enctype : "multipart/form-data" ,
            url  : "{{route('admin.users.store')}}" ,
            data : form_super_admin ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('user.success_add_super_admin')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else /*if( data.status == 'fail' )*/ {

                    $("#fail_message_add_new_admin").show() ;
                }
                //  else {
                //     $.each(data.errors , function (key , val) {
                //         if (key != 'status') {
                //             $("#" + key + "_error").text(val);
                //         }
                //         $("#input_password_confirmation_error").val("") ;
                //     });
                // }

            } , error : function ( errorMessages )    {
                $("#please_wait").hide() ;
                var messages = JSON.parse(errorMessages.responseText);

                $.each(messages.errors , function (key , val) {
                    $("#" + key + "_error").text(val[0]);
                });
            }
        }) ;
    }) ;
</script>


@stop
