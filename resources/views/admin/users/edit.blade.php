@extends('layouts.admin')

@section('title' , __('user.edit'))

@section('content')

    <div class="content-wrapper">
        @if (isset($user) && $user->count() > 0)

            <section class="content-header">

                <h1>{{__('user.super_admin')}}</h1>

                <ol class="breadcrumb">
                    <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i>{{__('sidebar.dashboard')}}</a></li>
                    <li><a href="{{route('admin.users.index')}}"> {{__('user.super_admin')}}</a></li>
                    <li class="active">{{__('user.edit')}}</li>
                </ol>
            </section>

            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">{{__('user.edit')}}</h3>
                    </div><!-- end of box header -->

                    <div class="box-body">

                        {{-- @include('admin.partials._errors') --}}

                        <form id="edit_form_super_admin" method="post">
                            @csrf
                            @method("POST")

                            <input type="hidden" name="id" value="{{$user->id}}">
                            <div class="form-group">
                                <label>{{__('user.name')}}</label>
                                <input type="text" name="name" class="form-control" value="{{$user->name}}">
                                <span class="text-danger" id="edit_name_error"></span>
                            </div>



                            <div class="form-group">
                                <label>{{__('user.email')}}</label>
                                <input type="email" name="email" class="form-control" value="{{$user->email}}">
                                <span class="text-danger" id="edit_email_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{__('user.photo')}}</label>
                                <input type="file" name="photo" class="form-control image" accept="image/*">
                            </div>

                            <div class="form-group">
                                <img src="{{$user->photo}}"  style="width: 100px" class="img-thumbnail image-preview" alt="">
                                <span class="text-danger" id="edit_photo_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{__('user.password')}}</label>
                                <input type="password" name="password" class="form-control">
                                <span class="text-danger" id="edit_password_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{__('user.password_confirmation')}}</label>
                                <input type="password" name="password_confirmation" id="input_password_confirmation_error" class="form-control">
                                <span class="text-danger" id="edit_password_confirmation_error"></span>
                            </div>

                            @if ( Auth::user()->hasrole('super_admin') && ! $user->hasrole('super_admin'))
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
                                                    @foreach ($powers as $key => $power)
                                                        {{-- {{$user->permissions->count()}} - {{$key}} --}}
                                                        <label><input type="checkbox" name="{{$model}}[]" value="{{ $model . '-' .$power}}"
                                                                {{-- @if ($user->permissions->count() > 0 )
                                                                    @foreach ($user->permissions as $item)
                                                                        @if ($item->name == $model . '-' . $power)
                                                                            checked
                                                                        @endif
                                                                    @endforeach
                                                                @endif --}}
                                                                @if ( $user->permissions->count() > 0 && $user->hasPermission($model . '-' . $power))
                                                                    checked
                                                                @endif
                                                            > {{__('user.' . $power)}}</label>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                            {{-- <div class="tab-pane " id="super_admin">
                                                <label><input type="checkbox" name="user[]" value="add"> اضافه</label>
                                                <label><input type="checkbox" name="user[]" value="update"> تعديل</label>
                                                <label><input type="checkbox" name="user[]" value="delete"> حذف</label>
                                            </div>
                                            <div class="tab-pane " id="category">
                                                <label><input type="checkbox" name="category[]" value="add"> اضافه</label>
                                                <label><input type="checkbox" name="category[]" value="update"> تعديل</label>
                                                <label><input type="checkbox" name="category[]" value="delete"> حذف</label>
                                            </div> --}}
                                        </div>

                                    </div>
                                </div>
                            @endif
                            {{-- <div class="form-group">
                                <label>@lang('site.permissions')</label>
                                <div class="nav-tabs-custom">

                                    @php
                                        $models = ['users', 'categories', 'products', 'clients', 'orders'];
                                        $maps = ['create', 'read', 'update', 'delete'];
                                    @endphp

                                    <ul class="nav nav-tabs">
                                        @foreach ($models as $index=>$model)
                                            <li class="{{ $index == 0 ? 'active' : '' }}"><a href="#{{ $model }}" data-toggle="tab">@lang('site.' . $model)</a></li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content">

                                        @foreach ($models as $index=>$model)

                                            <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="{{ $model }}">

                                                @foreach ($maps as $map)
                                                    <label><input type="checkbox" name="permissions[]" value="{{ $map . '_' . $model }}"> @lang('site.' . $map)</label>
                                                @endforeach

                                            </div>

                                        @endforeach

                                    </div><!-- end of tab content -->

                                </div><!-- end of nav tabs -->

                            </div> --}}

                            <div class="form-group">
                                <button id="edit_super_admin" class="btn btn-primary"><i class="fa fa-edit"></i> {{__('user.edit')}} </button>
                            </div>


                            <h3 id="please_wait" style="display:none; text-align:center">
                                {{__('user.please_wait')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_message_edit_admin" style="display:none; text-align:center;">
                                {{__('user.fail_add_super_admin')}}
                            </div>

                        </form><!-- end of form -->

                    </div><!-- end of box body -->

                </div><!-- end of box -->

            </section><!-- end of content -->
        @endif

    </div><!-- end of content wrapper -->

@stop


@section('scripts')

<script>
    $(document).on('click' , '#edit_super_admin' , function(e){

        e.preventDefault() ;

        $("#please_wait").show() ;
        $("#edit_name_error").text("") ;
        $("#edit_email_error").text("") ;
        $("#edit_photo_error").text("") ;
        $("#edit_password_error").text("") ;
        $("#edit_password_confirmation_error").text("") ;
        $("#fail_message_edit_admin").hide() ;

        var edit_form_super_admin = new FormData($('#edit_form_super_admin')[0]) ;

        $.ajax({
            type : 'POST' ,
            enctype : "multipart/form-data" ,
            url  : "{{route('admin.users.update' , $user->id)}}" ,
            data : edit_form_super_admin ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        enctype : "multipart/form-data" ,
                        layout: 'topRight',
                        text: "{{__('user.success_edit_super_admin')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else {

                    $("#fail_message_edit_admin").show() ;
                }

            } , error : function ( errorMessages )    {
                $("#please_wait").hide() ;
                var messages = JSON.parse(errorMessages.responseText);

                $.each(messages.errors , function (key , val) {
                    $("#edit_" + key + "_error").text(val[0]);
                });
            }
        }) ;
    }) ;
</script>


@stop
