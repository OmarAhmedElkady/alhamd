@extends('layouts.admin')

@section('title' , __('customers.pay'))

@section('content')

    <div class="content-wrapper">

        @if (isset($payment) && $payment->count() > 0)


            <section class="content-header">

                <h1 style="display: inline-block"> {{ $payment->customer->name }} </h1> -
                <h4 style="display: inline-block">{{ __('customers.'.$payment->customer->client_permissions) }}</h4>

                <ol class="breadcrumb">
                    <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i>{{__('sidebar.dashboard')}}</a></li>
                    <li><a href="{{route('admin.customer.index')}}"> {{__('customers.customers')}}</a></li>
                    <li class="active">{{__('customers.edit_payment')}}</li>
                </ol>
            </section>

            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">{{__('customers.edit_payment')}}</h3>
                    </div><!-- end of box header -->

                    <div class="content-header">
                        <h1 class="box-title" style="font: bolder" >{{__('customers.amount_to_be_modified')}} :-  <span id="total-acount">{{ number_format($payment->payment , 2) }}</span></h1>
                    </div><!-- end of box header -->
                    <br>
                    <div class="box-body">

                        <form action="{{ route('admin.payments.update') }}" method="POST">
                            @csrf
                            @method("POST")

                            <input type="hidden" name="id" value="{{ $payment->id }}">
                            <div class="form-group">
                                <label>{{__('customers.the_amount')}}</label>
                                <input type="number" name="amount" class="form-control" min="1" value="{{ $payment->payment }}">
                                @error('amount')
                                    <span class="text-danger" id="amount_error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class="form-group">
                                <button id="add_amount" class="btn btn-primary"><i class="fa fa-plus"></i> {{__('customers.payment')}} </button>
                            </div> --}}

                            <input type="submit" value="{{ __('customers.edit') }}">

                            {{-- <h3 id="payment_in_progress" style="display:none; text-align:center">
                                {{__('customers.payment_in_progress')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_payment" style="display:none; text-align:center;">
                                {{__('customers.Payment_failed')}}
                            </div> --}}

                        </form><!-- end of form -->

                    </div><!-- end of box body -->


                    {{-- @if (isset($customer->payments) && $customer->payments->count() > 0)

                        <h3 style="text-align: center;">{{ __("customers.previous_payments") }}</h3>
                        <table class="table table-hover" id="table_admin">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('customers.payment_date')}}</th>
                                <th>{{__('customers.payment_')}}</th>
                                <th>{{ __('language.action') }}</th>
                            </tr>
                            </thead>

                            <tbody id="tbody">
                                @foreach ($customer->payments as $index => $payment)
                                    <tr id="row_payment_{{$payment->id}}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $payment->payment }}</td>

                                        <td>
                                            @if (Auth::user()->hasrole('super_admin'))
                                            <a href="{{route('admin.language.edit' , $language->id)}}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> {{__('language.edit')}}</a>
                                                @if ($language->abbr == get_default_language())
                                                    <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> {{__('language.delete')}}</button>
                                                @else
                                                    <a href="" class="btn btn-danger btn-sm delete_language" language_id="{{$language->id}}" language_name="{{$language->name}}"><i class="fa fa-trash"></i> {{__('language.delete')}}</a>
                                                @endif


                                            @else
                                                <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{__('language.edit')}}</button>
                                                <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> {{__('language.delete')}}</button>
                                            @endif

                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table><!-- end of table -->

                    @else --}}
                    {{-- <div class="alert alert-success" role="alert" style="background-color:#43b380; text-align:center">
                            {{ __("customers.not_payment") }}
                        </div>
                    @endif --}}

                </div><!-- end of box -->

            </section><!-- end of content -->
        @endif
    </div><!-- end of content wrapper -->

@stop


{{-- @section('scripts')

<script>
    $(document).on('click' , '#add_amount' , function(e){

        e.preventDefault() ;

        $("#payment_in_progress").show() ;
        $("#fail_payment").hide() ;
        $("#amount_error").text("") ;

        var form_pay = new FormData($('#form_pay')[0]) ;

        $.ajax({
            type : 'POST' ,
            url  : "{{route('admin.payments.store')}}" ,
            data : form_pay ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#payment_in_progress").hide() ;

                if (data.status == 1) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('customers.payment_completed_successfully')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                    $("#total-acount").text($.number(data.newAccount,2)) ;

                }   else if ( data.status == 0)  {

                    $("#fail_payment").text(data.amountBig) ;
                    $("#fail_payment").show() ;

                }   else   {
                    $("#fail_payment").show() ;
                }

            } , error : function ( errorMessages )    {
                $("#payment_in_progress").hide() ;
                var messages = JSON.parse(errorMessages.responseText);

                // alert(messages.errors.amount) ;
                $("#amount_error").text(messages.errors.amount);
                // $.each(messages.errors , function (key , val) {
                //     $("#amount_error" + key + "_error").text(val[0]);
                // });
            }
        }) ;
    }) ;
</script>


@stop --}}
