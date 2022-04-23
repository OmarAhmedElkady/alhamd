@extends('layouts.admin')

@section('title' , __('language.add_language'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{__('language.language')}}</h1>

            <ol class="breadcrumb">
                <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i>{{__('sidebar.dashboard')}}</a></li>
                <li><a href="{{route('admin.language.index')}}"> {{__('language.language')}}</a></li>
                <li class="active">{{__('language.add')}}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{__('language.add')}}</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    <form id="form_language" method="post">
                        @csrf
                        {{-- @method("POST") --}}

                        <div class="form-group">
                            <label>{{__('language.name')}}</label>
                            <input type="text" name="name" class="form-control" value="">
                            <span class="text-danger" id="language_name_error"></span>
                        </div>

                        <div class="form-group">
                            <label>{{__('language.abbr')}}</label>
                            <input type="text" name="abbr" class="form-control" value="">
                            <span class="text-danger" id="language_abbr_error"></span>
                        </div>

                        <div class="form-group">
                            <button id="save_language" class="btn btn-primary"><i class="fa fa-plus"></i> {{__('language.add')}} </button>
                        </div>


                        <h3 id="please_wait" style="display:none; text-align:center">
                            {{__('language.please_wait')}}
                        </h3>

                        <div class="alert alert-danger" role="alert" id="fail_message_add_new_language" style="display:none; text-align:center;">
                            {{__('language.fail_add_language')}}
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@stop


@section('scripts')

<script>
    $(document).on('click' , '#save_language' , function(e){

        e.preventDefault() ;

        $("#please_wait").show() ;
        $("#fail_message_add_new_language").hide() ;
        $("#language_name_error").text("") ;
        $("#language_abbr_error").text("") ;


        var form_language = new FormData($('#form_language')[0]) ;

        $.ajax({
            type : 'POST' ,
            url  : "{{route('admin.language.store')}}" ,
            data : form_language ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('language.success_add_language')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else  {

                    $("#fail_message_add_new_language").show() ;
                }

            } , error : function ( errorMessages )    {
                $("#please_wait").hide() ;
                var messages = JSON.parse(errorMessages.responseText);

                $.each(messages.errors , function (key , val) {
                    $("#language_" + key + "_error").text(val[0]);
                });
            }
        }) ;
    }) ;
</script>


@stop
