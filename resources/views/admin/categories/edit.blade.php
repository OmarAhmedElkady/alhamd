@extends('layouts.admin')

@section('title' , __('categories.edit'))

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __('categories.edit') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.category.index') }}"> {{ __('categories.categories') }}</a></li>
                <li class="active">{{ __('categories.edit') }}</li>
            </ol>
        </section>

        @if ( isset($categories) && $categories->count() > 0)

            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">{{ __('categories.edit') }}</h3>
                    </div><!-- end of box header -->
                    <div class="box-body">

                        <form id="form_edit_category" method="POST">
                            @csrf
                            @method('POST')


                            <input type="hidden" id="count_languages" value="{{ $languages->count() }}">
                            @foreach ($languages as $key => $lang)
                                <input type="hidden" name="translation_of" value="{{ $categories[0]['translation_of'] }}">
                                <div class="form-group">
                                    <label>{{ __('categories.name_in_language') . $lang->name }}</label>
                                    @if ($key < $categories->count())
                                        <input type="text" name="category[{{ $key }}][name]" class="form-control" value="{{ $categories[$key]['name'] }}">
                                    @else
                                        <input type="text" name="category[{{ $key }}][name]" class="form-control" value="">
                                    @endif

                                    <span class="text-danger" id="category_{{ $key }}_name_error"></span>
                                </div>
                            @endforeach


                            <div class="form-group">
                                <button id="edit_category" class="btn btn-primary"><i class="fa fa-plus"></i>{{ __('categories.edit') }}</button>
                            </div>

                            <h3 id="please_wait_edit_category" style="display:none; text-align:center">
                                {{__('language.please_wait')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_message_edit_category" style="display:none; text-align:center;">
                                {{__('categories.fail_add_category')}}
                            </div>

                        </form><!-- end of form -->

                    </div><!-- end of box body -->

                </div><!-- end of box -->

            </section><!-- end of content -->

        @endif

    </div><!-- end of content wrapper -->

@endsection


@section('scripts')

    <script>
        $(document).on('click' , '#edit_category' , function(e){

            e.preventDefault() ;

            $("#please_wait_edit_category").show() ;
            $("#fail_message_edit_category").hide() ;

            var count_languages = $("#count_languages").val() ;
            for ( var i = 0 ; i < count_languages ; i++ )   {
                $("#category_" + i + "_name_error" ).text("");
            }

            var form_edit_category = new FormData($('#form_edit_category')[0]) ;

            $.ajax({
                type : 'POST' ,
                url  : "{{route('admin.category.update' , $id)}}" ,
                data : form_edit_category ,
                processData : false ,
                contentType : false ,
                cache : false ,
                success : function ( data ) {
                    $("#please_wait_edit_category").hide() ;
                    if (data.status == true) {
                        new Noty({
                            type: 'success',
                            layout: 'topRight',
                            text: "{{__('categories.success_edit_category')}}",
                            timeout: 2000,
                            killer: true
                        }).show();

                    }   else  {

                        $("#fail_message_edit_category").show() ;
                    }

                } , error : function ( errorMessages )    {
                    $("#please_wait_edit_category").hide() ;
                    var messages = JSON.parse(errorMessages.responseText);

                    $.each(messages.errors , function (key , val) {
                        key = key.replace(/[.]/g , '_') ;
                        $("#" + key + "_error").text(val);
                    });
                }
            }) ;
        }) ;
    </script>


@stop
