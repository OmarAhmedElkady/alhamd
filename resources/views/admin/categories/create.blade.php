@extends('layouts.admin')

@section('title' , __('categories.add_new_category'))

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __('categories.add_new_category') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.category.index') }}"> {{ __('categories.categories') }}</a></li>
                <li class="active">{{ __('categories.add') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{ __('categories.add') }}</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    <form id="form_category" method="POST">
                        @csrf
                        @method('POST')

                        @if (isset($languages) && $languages->count() > 0)
                            <input type="hidden" id="count_languages" value="{{ $languages->count() }}">
                            @foreach ($languages as $key => $lang)
                                <div class="form-group">
                                    <label>{{ __('categories.name_in_language') . $lang->name }}</label>
                                    <input type="text" name="category[{{ $key }}][name]" class="form-control" value="">
                                    <input type="hidden" name="category[{{ $key }}][abbr]" value="{{ $lang->abbr }}">
                                    <span class="text-danger" id="category_{{ $key }}_name_error"></span>
                                </div>
                            @endforeach

                            <div class="form-group">
                                <button id="add_category" class="btn btn-primary"><i class="fa fa-plus"></i>{{ __('categories.add') }}</button>
                            </div>

                            <h3 id="please_wait_category" style="display:none; text-align:center">
                                {{__('language.please_wait')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_message_add_new_category" style="display:none; text-align:center;">
                                {{__('language.fail_add_language')}}
                            </div>

                        @else

                            @if (Auth::user()->hasRole('super_admin'))
                                <h4>{{ __('categories.please_add_a_language_to_the_site_first') }} <br><br>
                                    <a href="{{route('admin.language.create')}}" >{{__('language.add_language')}}</a>
                                </h4>

                            @else
                                <h3 style="text-align:center;">{{ __("categories.wait_to_add_a_language") }}</h3>
                            @endif

                        @endif







                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection


@section('scripts')

<script>
    $(document).on('click' , '#add_category' , function(e){

        e.preventDefault() ;

        $("#please_wait_category").show() ;
        $("#fail_message_add_new_category").hide() ;

        var count_languages = $("#count_languages").val() ;
        for ( var i = 0 ; i < count_languages ; i++ )   {
            $("#category_" + i + "_name_error" ).text("");
        }

        var form_category = new FormData($('#form_category')[0]) ;

        $.ajax({
            type : 'POST' ,
            url  : "{{route('admin.category.store')}}" ,
            data : form_category ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait_category").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('categories.success_add_category')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else  {

                    $("#fail_message_add_new_category").show() ;
                }

            } , error : function ( errorMessages )    {
                $("#please_wait_category").hide() ;
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
