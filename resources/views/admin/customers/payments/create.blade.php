@extends('layouts.admin')

@section('title' , __('customers.pay'))

@section('content')

    <div class="content-wrapper">

        @if (isset($customer) && $customer->count() > 0)


            <section class="content-header">

                <h1 style="display: inline-block"> {{ $customer->name }} </h1> -
                <h4 style="display: inline-block">{{ __('customers.'.$customer->client_permissions) }}</h4>

                <ol class="breadcrumb">
                    <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i>{{__('sidebar.dashboard')}}</a></li>
                    <li><a href="{{route('admin.customer.index')}}"> {{__('customers.customers')}}</a></li>
                    <li class="active">{{__('customers.pay')}}</li>
                </ol>
            </section>

            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">{{__('customers.pay')}}</h3>
                    </div><!-- end of box header -->

                    <div class="content-header">
                        <h1 class="box-title" style="font: bolder" >{{__('customers.total_account')}} <span id="total-acount">{{ number_format($customer->previous_account , 2) }}</span></h1>
                    </div><!-- end of box header -->
                    <br>
                    <div class="box-body">

                        <form id="form_pay" method="post">
                            @csrf
                            {{-- @method("POST") --}}

                            <input type="hidden" name="translation_of" value="{{ $customer->translation_of }}">
                            <div class="form-group">
                                <label>{{__('customers.the_amount')}}</label>
                                <input type="number" name="amount" class="form-control" min="1" value="">
                                <span class="text-danger" id="amount_error"></span>
                            </div>

                            <div class="form-group">
                                <button id="add_amount" class="btn btn-primary"><i class="fa fa-plus"></i> {{__('customers.payment')}} </button>
                            </div>


                            <h3 id="payment_in_progress" style="display:none; text-align:center">
                                {{__('customers.payment_in_progress')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_payment" style="display:none; text-align:center;">
                                {{__('customers.Payment_failed')}}
                            </div>

                        </form><!-- end of form -->

                    </div><!-- end of box body -->

                    @if (isset($payments) && isset($payments[0]) )

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
                                @foreach ($payments as $index => $payment)
                                    <tr id="row_payment_{{$payment->id}}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $payment->payment }}</td>

                                        <td>
                                            {{-- @if (Auth::user()->hasrole('super_admin')) --}}
                                                <a href="{{route('admin.payments.edit' , $payment->id)}}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> {{__('customers.edit')}}</a>
                                                {{-- @if ($language->abbr == get_default_language())
                                                    <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> {{__('language.delete')}}</button>
                                                @else --}}
                                                    <a href="" class="btn btn-danger btn-sm delete_payment" payment_id="{{$payment->id}}" ><i class="fa fa-trash"></i> {{__('customers.delete')}}</a>
                                                {{-- @endif --}}


                                            {{-- @else
                                                <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{__('language.edit')}}</button>
                                                <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> {{__('language.delete')}}</button>
                                            @endif --}}

                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{ $payments->links() }}

                    @else
                    <div class="alert alert-success" role="alert" style="background-color:#43b380; text-align:center">
                            {{ __("customers.not_payment") }}
                        </div>
                    @endif

                </div><!-- end of box -->

            </section><!-- end of content -->
        @endif
    </div><!-- end of content wrapper -->

@stop


@section('scripts')

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


    $(document).on('click' , '.delete_payment' , function(e){
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var payment_id = $(this).attr('payment_id') ;

            var n = new Noty({
                text: "{{__('customers.confirm_delete_payment')}}",
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('customers.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{ route('admin.payments.delete') }}" ,
                            data : {
                                '_token'     : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'id'        :   payment_id
                            } , success : function ( data ) {
                                if (data.status == true) {
                                    $("#row_payment_" + data.id).remove() ;
                                    $("#total-acount").text($.number(data.payment ,2)) ;
                                }
                            }
                        }) ;
                    }),

                    Noty.button("{{__('customers.no')}}", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });

            n.show();
        }) ;

</script>


@stop
