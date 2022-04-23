@extends('layouts.admin')

@section('title' , __('customers.add_a_new_customer'))

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __('customers.add_a_new_customer') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.customer.index') }}"> {{ __('customers.customers') }}</a></li>
                <li class="active">{{ __('customers.add') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{ __('customers.add') }}</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    <form id="form_customer" method="post">
                        @csrf
                        @method("POST")

                        {{-- /////////////////////////////////////////////////////////// --}}


                        @if (isset($languages) && $languages->count() > 0)

                            <div class="form-group">
                                <label>{{ __('customers.Pharmacist-client') }}</label>

                                <select name="client_permissions" class="form-control" style="padding-top:0px;">
                                    <option value="">...</option>

                                    <option value="pharmaceutical" >{{ __('customers.pharmaceutical') }}</option>
                                    <option value="special_customer" >{{ __('customers.special_customer') }}</option>
                                    <option value="customer" >{{ __('customers.customer') }}</option>

                                </select>
                                <span class="text-danger" id="client_permissions_error"></span>
                            </div>



                            <input type="hidden" id="count_languages" value="{{ $languages->count() }}">

                            @foreach ($languages as $key => $lang)

                                <div class="form-group">
                                    <label>{{ __('customers.name_in_language') . $lang->name }}</label>
                                    <input type="text" name="customer[{{ $key }}][name]" class="form-control" >
                                    <span class="text-danger" id="customer_{{ $key }}_name_error"></span>
                                </div>

                            @endforeach


                            <div class="form-group">
                                <label>{{ __('customers.phone') }}</label>
                                <input type="text" name="phone" class="form-control">
                                <span class="text-danger" id="phone_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('customers.title') }}</label>
                                <textarea name="title" class="form-control"></textarea>
                                <span class="text-danger" id="title_error"></span>
                            </div>

                            <div class="form-group">
                                <button id="add_customer" class="btn btn-primary"><i class="fa fa-plus"></i>{{ __('customers.add') }}</button>
                            </div>

                            <h3 id="please_wait_customer" style="display:none; text-align:center">
                                {{__('language.please_wait')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_message_add_new_customer" style="display:none; text-align:center;">
                                {{__('language.fail_add_language')}}
                            </div>

                        @else

                            @if (Auth::user()->hasRole('super_admin'))
                                <h4>{{ __('customers.please_add_a_language_to_the_site_first') }} <br><br>
                                    <a href="{{route('admin.language.create')}}" >{{__('language.add_language')}}</a>
                                </h4>

                            @else
                                <h3 style="text-align:center;">{{ __("customers.wait_to_add_a_language") }}</h3>
                            @endif

                        @endif


                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@stop



@section('scripts')

<script>
    $(document).on('click' , '#add_customer' , function(e){

        e.preventDefault() ;

        $("#please_wait_customer").show() ;
        $("#fail_message_add_new_customer").hide() ;

        $("#client_permissions_error").text("") ;

        var count_languages = $("#count_languages").val() ;
        for ( var i = 0 ; i < count_languages ; i++ )   {
            $("#customer_" + i + "_name_error" ).text("");
        }


        var form_customer = new FormData($('#form_customer')[0]) ;

        $.ajax({
            type : 'POST' ,
            url  : "{{route('admin.customer.store')}}" ,
            data : form_customer ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait_customer").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('customers.success_add_customer')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else  {

                    $("#fail_message_add_new_customer").show() ;
                }

            } , error : function ( errorMessages )    {

                $("#please_wait_customer").hide() ;

                var messages = JSON.parse(errorMessages.responseText);

                $.each(messages.errors , function (key , val) {

                    key = key.replace(/[.]/g , '_') ;
                    $("#" + key + "_error").text(val);  // It shows product name errors
                });
            }
        }) ;
    }) ;
</script>


@stop
