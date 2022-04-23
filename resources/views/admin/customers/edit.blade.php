@extends('layouts.admin')

@section('title' , __('customers.edit'))

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __('customers.edit') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.customer.index') }}"> {{ __('customers.customers') }}</a></li>
                <li class="active">{{ __('customers.edit') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{ __('customers.edit') }}</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    <form id="form_edit_customer" method="post">
                        @csrf
                        @method("POST")

                        {{-- /////////////////////////////////////////////////////////// --}}


                        @if ((isset($languages) && $languages->count() > 0) && (isset($customer) && $customer->count() > 0))

                            <div class="form-group">
                                <label>{{ __('customers.Pharmacist-client') }}</label>

                                <select name="client_permissions" class="form-control" style="padding-top:0px;">
                                    <option value="">...</option>

                                    <option value="pharmaceutical" {{ $customer[0]->client_permissions == "pharmaceutical" ? "selected" : "" }}>{{ __('customers.pharmaceutical') }}</option>
                                    <option value="special_customer" {{ $customer[0]->client_permissions == "special_customer" ? "selected" : "" }}>{{ __('customers.special_customer') }}</option>
                                    <option value="customer" {{ $customer[0]->client_permissions == "customer" ? "selected" : "" }}>{{ __('customers.customer') }}</option>

                                </select>
                                <span class="text-danger" id="edit_client_permissions_error"></span>
                            </div>



                            <input type="hidden" id="count_languages" value="{{ $languages->count() }}">
                            <input type="hidden" name="translation_of" value="{{ $customer[0]->translation_of }}">

                            @foreach ($languages as $key => $lang)

                                <div class="form-group">
                                    <label>{{ __('customers.name_in_language') . $lang->name }}</label>
                                    @if ( $key < $customer->count())
                                        <input type="text" name="customer[{{ $key }}][name]" class="form-control" value="{{ $customer[$key]->name }}" >
                                    @else
                                        <input type="text" name="customer[{{ $key }}][name]" class="form-control" >
                                    @endif
                                    <span class="text-danger" id="edit_customer_{{ $key }}_name_error"></span>
                                </div>

                            @endforeach

                            <div class="form-group">
                                <label>{{ __('customers.phone') }}</label>
                                <input type="text" name="phone" class="form-control" value="{{ $customer[0]->phone }}">
                                <span class="text-danger" id="edit_phone_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('customers.title') }}</label>
                                <textarea name="title" class="form-control">{{ $customer[0]->title }}</textarea>
                                <span class="text-danger" id="edit_title_error"></span>
                            </div>

                            <div class="form-group">
                                <button id="edit_customer" class="btn btn-primary"><i class="fa fa-plus"></i>{{ __('customers.edit') }}</button>
                            </div>

                            <h3 id="please_wait_customer" style="display:none; text-align:center">
                                {{__('language.please_wait')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_message_edit_customer" style="display:none; text-align:center;">
                                {{__('language.fail_add_language')}}
                            </div>

                        @endif

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@stop



@section('scripts')

<script>
    $(document).on('click' , '#edit_customer' , function(e){

        e.preventDefault() ;

        $("#please_wait_customer").show() ;
        $("#fail_message_edit_customer").hide() ;

        $("#edit_client_permissions_error").text("") ;

        var count_languages = $("#count_languages").val() ;
        for ( var i = 0 ; i < count_languages ; i++ )   {
            $("#edit_customer_" + i + "_name_error" ).text("");
        }


        var form_edit_customer = new FormData($('#form_edit_customer')[0]) ;

        $.ajax({
            type : 'POST' ,
            url  : "{{route('admin.customer.update')}}" ,
            data : form_edit_customer ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait_customer").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('customers.success_edit_customer')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else  {

                    $("#fail_message_edit_customer").show() ;
                }

            } , error : function ( errorMessages )    {

                $("#please_wait_customer").hide() ;

                var messages = JSON.parse(errorMessages.responseText);

                $.each(messages.errors , function (key , val) {

                    key = key.replace(/[.]/g , '_') ;
                    $("#edit_" + key + "_error").text(val);  // It shows product name errors
                });
            }
        }) ;
    }) ;
</script>


@stop
