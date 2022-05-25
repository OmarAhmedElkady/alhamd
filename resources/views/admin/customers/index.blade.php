@extends('layouts.admin')

@section('title' , __('customers.customers'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{ __('customers.customers') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.customer.index') }}"> {{ __('customers.customers') }}</a></li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                @if (isset($customers) && $customers->count() > 0)

                    <div class="box-header with-border">

                        <h3 class="box-title" style="margin-bottom: 15px">{{ __('customers.customers') }} <small>{{ $customers->count() }}</small></h3>

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" id="search_customer" placeholder="{{ __('user.search') }}" >
                            </div>

                            <div class="col-md-4">
                                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('customers-create'))
                                    <a href="{{ route('admin.customer.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('customers.add') }}</a>

                                @else
                                    <button class="btn btn-primary disabled"><i class="fa fa-plus"></i> {{ __('customers.add') }}</button>
                                @endif
                            </div>

                        </div>

                    </div><!-- end of box header -->

                    <p style="text-align:center; display:none;" id="customerSearch">{{__('user.searching')}}</p>
                    <h3 style="text-align:center; margin-top:50px; display:none;" id="not_found_customer"></h3>

                    <div class="box-body table-responsive">

                        <table class="table table-hover" id="table_customer">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('customers.name') }}</th>
                                <th>{{ __('customers.phone') }}</th>
                                <th>{{ __('customers.title') }}</th>
                                <th>{{ __('customers.client') }}</th>
                                <th>{{ __('customers.previous_account') }}</th>
                                <th>{{ __('customers.pay') }}</th>
                                <th>{{ __('customers.add_order') }}</th>
                                <th>{{ __('customers.action') }}</th>
                            </tr>
                            </thead>

                            <tbody id="tbody_customers">
                            @foreach ($customers as $index => $customer)
                                <tr id="row_customer_{{ $customer->translation_of }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->title }}</td>
                                    <td>{{ __('customers.'.$customer->client_permissions) }}</td>
                                    <td>{{ number_format($customer->previous_account , 2) }}</td>

                                    <td>
                                        @if ($customer->previous_account > 0)
                                            <a href="{{ route('admin.payments.create' , $customer->translation_of) }}" class="btn btn-success">{{ __('customers.pay_sum') }}</a>
                                        @else
                                            <button class="btn btn-success disabled">{{ __('customers.pay_sum') }}</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if (auth()->user()->hasPermission('orders-create'))
                                            <a href="{{ route('admin.order.create' , $customer->translation_of) }}" class="btn btn-primary btn-sm">{{ __('customers.add_order') }}</a>
                                        @else
                                            <button class="btn btn-primary btn-sm disabled">{{ __('customers.add_order') }}</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if ( Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('customers-update'))
                                            <a href="{{ route('admin.customer.edit' , $customer->translation_of) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> {{ __('customers.edit') }}</a>
                                        @else
                                            <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('customers.edit') }}</button>
                                        @endif
                                        @if ( Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('customers-delete'))
                                            <button class="btn btn-danger delete btn-sm delete_customer" translation_of="{{ $customer->translation_of }}" customer_name="{{ $customer->name }}"><i class="fa fa-trash"></i> {{ __('customers.delete') }}</button>
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> {{ __('customers.delete') }}</button>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{$customers->links()}}
                        {{-- {{ $customers->appends(request()->query())->links() }} --}}


                    </div><!-- end of box body -->
                @else

                    <h2 style="padding: 10px 5px 40px;">
                        {{ __('customers.not_found_customers') }}
                        @if (Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('customers-create'))
                            <a href="{{ route('admin.customer.create') }}">{{ __('customers.add_a_new_customer') }}</a>
                        @endif

                    </h2>

                @endif

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@stop



@section('scripts')
    <script>
        $(document).on('click' , '.delete_customer' , function(e){
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var translation_of = $(this).attr('translation_of') ;
            var customer_name = $(this).attr('customer_name') ;

            var n = new Noty({
                text: "{{__('customers.confirm_delete_customer')}} [ " + customer_name + " ]" ,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('customers.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{route('admin.customer.delete')}}" ,
                            data : {
                                '_token'                : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'translation_of'        :   translation_of
                            } , success : function ( data ) {
                                if (data.status == 'true') {
                                    $("#row_customer_" + data.translation_of).remove() ;
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






        $("#search_customer").on("keyup" , function(){

            $("#customerSearch").show() ;  // رساله جارى البحث

            var value = $(this).val() ;  // Search جلب القيمه الموجوده فى حقل

            $.ajax({
                type : 'GET' ,
                dataType : "json" ,
                url  : "{{route('admin.customer.search')}}" ,
                data : {
                    '_token'     : "{{csrf_token()}}" ,  // تشفير البيانات
                    'search'        :   value
                } , success : function ( data ) {

                    $("#customerSearch").hide() ;    // إخفاء رساله جارى البحث

                    // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
                    if (data.status == false) {

                        $("#table_customer").hide() ;  // إخفاء الجدول

                        $("#not_found_customer").show() ; // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
                        $("#not_found_customer").text( "{{__('customers.this_customer')}} [ " + data.search + " ] {{ __('customers.not_found') }}") ;


                    }   else    {
                        $("#not_found_customer").hide() ; // <h3></h3> إخفاء
                        $("#table_customer").show() ;  // قم بإظهار الجدول

                        var tableRow = '' ;     // هذا المتغير الذى يحفظ الصفوف
                        $("#tbody_customers").html('') ;  // إعطاء قيمه فارغه للجدول



                        // على البيانات loop عمل
                        $.each(data.data, function(index, value){

                            var translation_of = value.translation_of ; // translation_of => خاصه بالمستخدم
                            var customerName   = value.name ;

                            // خاص لكل مستخدم Route() السطر القادم يقوم بعمل
                            // route('admin.order.create' , $customer->translation_of)
                            var URLCreate = "{{ route('admin.order.create' , ':translation_of') }}" ;
                            URLCreate = URLCreate.replace(':translation_of', translation_of);

                            var URLedit = "{{ route('admin.customer.edit' , ':translation_of') }}" ;
                            URLedit = URLedit.replace(':translation_of', translation_of);

                            var URLprevious_account = "{{ route('admin.payments.create' , ':translation_of') }}" ;
                            URLprevious_account = URLprevious_account.replace(':translation_of', translation_of);

                            // حفظ البيانات فى الجدول
                            tableRow = '<tr id="row_customer_' + value.translation_of +'">' +
                                            '<td>' + ++index + '</td>'+
                                            '<td>' + value.name +'</td>';
                                            if(value.phone != null) {
                                                tableRow += '<td>'+value.phone+'</td>' ;
                                            }   else    {
                                                tableRow += '<td></td>' ;
                                            }

                                            if(value.title != null) {
                                                tableRow += '<td>'+value.title+'</td>' ;
                                            }   else    {
                                                tableRow += '<td></td>' ;
                                            }

                                            if (value.client_permissions == "pharmaceutical") {
                                                tableRow += '<td>{{ __('customers.pharmaceutical') }}</td>' ;
                                            } else if (value.client_permissions == "special_customer") {
                                                tableRow += '<td>{{ __('customers.special_customer') }}</td>' ;
                                            }   else    {
                                                tableRow += '<td>{{ __('customers.customer') }}</td>' ;
                                            }

                                            tableRow += '<td>'+ $.number( value.previous_account , 2)+'</td>' ;

                                            if (value.previous_account > 0) {
                                                tableRow += '<td><a href="' + URLprevious_account + '" class="btn btn-success">{{ __('customers.pay_sum') }}</a></td>' ;
                                            }  else    {
                                                tableRow += '<td><button class="btn btn-success disabled">{{ __('customers.pay_sum') }}</button></td>' ;
                                            }

                                            var ordersCreate   = "{{ auth()->user()->hasPermission('orders-create') }}" ;
                                            if (ordersCreate) {
                                                tableRow += '<td><a href="'+URLCreate+'" class="btn btn-primary btn-sm">{{ __('customers.add_order') }}</a></td>' ;
                                            } else {
                                                tableRow +=  '<td><button class="btn btn-primary btn-sm disabled">{{ __('customers.add_order') }}</button></td>' ;
                                            }

                                            tableRow += '<td>' ;
                                                var CheckPermissionEdit   = "{{ Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('customers-update')  }}" ;
                                                var CheckPermissionDelete = "{{ Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('customers-delete') }}" ;

                                                if( CheckPermissionEdit == 1 )    {
                                                    tableRow += '<a href='+URLedit+' class="btn btn-info btn-sm" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{ __('customers.edit') }}</a>' ;
                                                }   else    {
                                                    tableRow += '<button class="btn btn-info btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{__('customers.edit')}}</button>' ;
                                                }


                                                if( CheckPermissionDelete == 1 )    {
                                                    tableRow += '<button class="btn btn-danger delete btn-sm delete_customer" translation_of="'+translation_of+'" customer_name="'+customerName+'" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i>{{ __('customers.delete') }}</button>'

                                                }   else    {
                                                    tableRow += '<button class="btn btn-danger btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i> {{__('customers.delete')}}</button>' ;
                                                }

                                            tableRow += '</td>'+

                                        '</tr>' ;


                            $("#tbody_customers").append(tableRow);
                        }) ;
                    }
                }
            }) ;
        }) ;

    </script>
@stop
