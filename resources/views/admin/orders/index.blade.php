@extends('layouts.admin')

@section('title' , __('order.orders'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{  __('order.orders') }}
                <small>{{ $orders->count() }} {{  __('order.orders') }}</small>
            </h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li class="active">{{ __('order.orders') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                <div class="col-md-7">

                    <div class="box box-primary">

                        <div class="box-header">

                            <h3 class="box-title" style="margin-bottom: 10px">{{ __('order.orders') }}</h3>

                            {{-- <form action="" method="get"> --}}

                                {{-- <div class="row">

                                    <div class="col-md-8">
                                        <input type="text" name="search" id="search_order" class="form-control" placeholder="{{ __('user.search') }}" >
                                    </div>

                                </div><!-- end of row --> --}}

                            {{-- </form><!-- end of form --> --}}

                        </div><!-- end of box header -->

                        @if ($orders->count() > 0)

                            <p style="text-align:center; display:none;" id="orderSearch">{{__('user.searching')}}</p>
                            <h3 style="text-align:center; margin-top:50px; display:none;" id="not_found_order"></h3>

                            <div class="box-body table-responsive">

                                <table class="table table-hover" id="table_order">
                                    <thead>
                                        <tr>
                                            <th>{{ __('order.name') }}</th>
                                            <th>{{ __('order.price') }}</th>
                                            <th>{{ __('order.status') }}</th>
                                            <th>{{ __('order.created_at') }}</th>
                                            <th>{{ __('order.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_order">
                                        @foreach ($orders as $order)
                                            @if (isset($order->customer->name) && ! empty($order->customer->name))
                                                <tr id="row_order_{{ $order->id }}">
                                                    <td style="width: 8.5em">{{ $order->customer->name }}</td>
                                                    <td>{{ number_format($order->total_price, 2) }}</td>
                                                    @if ($order->status == '0')
                                                        <td>
                                                            <button order_id="{{ $order->id }}" class="order_status btn btn-warning" id="preparing_{{ $order->id }}"> {{ __('order.preparing') }} </button>
                                                            <button class="prepared btn btn-success disabled" id="prepared_{{ $order->id }}" style="width: 7em; display:none;"> {{ __('order.prepared') }} </button>
                                                        </td>
                                                    @else
                                                        <td><button class="prepared btn btn-success disabled" id="prepared_{{ $order->id }}" style="width: 7em"> {{ __('order.prepared') }} </button></td>
                                                    @endif

                                                    <td>{{ $order->created_at->diffForhumans() }}</td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm order_products"
                                                                data-url="{{ route('admin.all_order.show' , $order->id) }}"
                                                                data-method="get"
                                                        >
                                                            <i class="fa fa-list"></i>
                                                            {{ __('order.show') }}
                                                        </button>
                                                        @if (auth()->user()->hasPermission('orders-update'))
                                                            <a href="{{ route('admin.order.edit' , $order->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> {{ __('order.edit') }}</a>
                                                        @else
                                                            <button href="#" disabled class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> {{ __('order.edit') }}</button>
                                                        @endif

                                                        @if (auth()->user()->hasPermission('orders-delete'))
                                                            <button class="btn btn-danger btn-sm delete delete_order" id="{{ $order->id }}" customer_name="{{ $order->customer->name }}"><i class="fa fa-trash"></i> {{ __('order.delete') }}</button>
                                                        @else
                                                            <button href="#" class="btn btn-danger btn-sm" disabled><i class="fa fa-trash"></i> {{ __('order.delete') }}</button>
                                                        @endif

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table><!-- end of table -->

                                {{ $orders->links() }}
                                {{-- {{ $orders->appends(request()->query())->links() }} --}}

                            </div>

                        @else

                            <div class="box-body">
                                <h3>{{ __('order.not_found_orders') }}</h3>
                            </div>

                        @endif

                    </div><!-- end of box -->

                </div><!-- end of col -->

                <div class="col-md-5">

                    <div class="box box-primary">

                        <div class="box-header">
                            <h3 class="box-title" style="margin-bottom: 10px">{{ __('order.show_products') }}</h3>
                        </div><!-- end of box header -->

                        <div class="box-body">

                            <div style="display: none; flex-direction: column; align-items: center;" id="loading">
                                <div class="loader"></div>
                                <p style="margin-top: 10px">{{ __('order.loading') }}</p>
                            </div>

                            <div id="order-product-list">

                            </div><!-- end of order product list -->

                        </div><!-- end of box body -->

                    </div><!-- end of box -->

                </div><!-- end of col -->

            </div><!-- end of row -->

        </section><!-- end of content section -->

    </div><!-- end of content wrapper -->

@stop


@section('scripts')

    <script>
        $(document).on('click' , '.delete_order' , function(e){
            // var that = $(this)
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var id = $(this).attr('id') ;
            var customer_name = $(this).attr('customer_name') ;

            var n = new Noty({
                text: "{{__('order.confirm_delete_order')}} [ " + customer_name + " ]" ,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('order.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{route('admin.order.delete')}}" ,
                            data : {
                                '_token'                : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'id'        :   id ,
                            } , success : function ( data ) {
                                if (data.status == 'true') {
                                    $("#row_order_" + data.id).remove() ;
                                }
                            }
                        }) ;
                    }),

                    Noty.button("{{__('order.no')}}", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });

            n.show();
        }) ;



        $(document).on("click" , ".order_status" , function(e){
            e.preventDefault() ; // relode لكى تمنع الصفحه لعمل

            // الخاص بالزرار id قم بجلب
            var order_id = $(this).attr("order_id") ;

            $.ajax({
                type : "post" ,
                url  : "{{ route('admin.all_order.status') }}" ,
                data : {
                    "_token" : "{{csrf_token()}}" ,
                    "id" : order_id

                } , success : function (data)   {

                    if(data.status == '1') {

                        $("#preparing_" + data.id).hide() ;
                        $("#prepared_" + data.id).show() ;
                    }

                }
            }) ;
        });




        // $("#search_order").on("keyup" , function(){

        //     $("#orderSearch").show() ;  // رساله جارى البحث

        //     var value = $(this).val() ;  // Search جلب القيمه الموجوده فى حقل

        //     $.ajax({
        //         type : 'GET' ,
        //         dataType : "json" ,
        //         url  : "{{route('admin.all_order.search')}}" ,
        //         data : {
        //             '_token'     : "{{csrf_token()}}" ,  // تشفير البيانات
        //             'search'        :   value
        //         } , success : function ( data ) {

        //             $("#orderSearch").hide() ;    // إخفاء رساله جارى البحث

        //             // // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
        //             if (data.status == false) {

        //                 $("#table_order").hide() ;  // إخفاء الجدول

        //                 $("#not_found_order").show() ; // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
        //                 $("#not_found_order").text( "{{__('order.this_order_not_found')}} [ " + data.search + " ]") ;


        //             } else    {
        //                 $("#not_found_order").hide() ; // <h3></h3> إخفاء
        //                 $("#table_order").show() ;  // قم بإظهار الجدول

        //                 var tableRow = '' ;     // هذا المتغير الذى يحفظ الصفوف
        //                 $("#tbody_order").html('') ;  // إعطاء قيمه فارغه للجدول



        //                 // على البيانات loop عمل
        //                 $.each(data.data, function(index, value){
        //                     // $order->customer->name
        //                     if (value.customer) {

        //                         var order = value ; // order => خاصه بالمستخدم
        //                         var customer   = value.customer ;

        //                         var URLShowProduct = "{{ route('admin.all_order.show' , ':order_id') }}" ;
        //                         URLShowProduct = URLShowProduct.replace(':order_id', order.id);

        //                                             // route('admin.order.edit' , $order->id)
        //                         var URLEditOrder = "{{ route('admin.order.edit' , ':order_id') }}" ;
        //                         URLEditOrder = URLEditOrder.replace(':order_id', order.id);


        //                         // // حفظ البيانات فى الجدول
        //                         tableRow = '<tr id="row_order_'+ order.id +'">' +
        //                                         '<td>' + customer.name + '</td>'+
        //                                         '<td>' + order.total_price.toLocaleString('en-US') +'</td>';
        //                                         if(order.status == '0') {
        //                                             tableRow += '<td>'+
        //                                                             '<button order_id="'+ order.id +'" class="order_status btn btn-warning" id="preparing_'+ order.id+'"> {{ __('order.preparing') }} </button>' +
        //                                                             '<button class="prepared btn btn-success disabled" id="prepared_'+ order.id +'" style="width: 7em; display:none;"> {{ __('order.prepared') }} </button>' +
        //                                                         '</td>' ;
        //                                         }   else    {
        //                                             tableRow += '<td><button class="prepared btn btn-success disabled" id="prepared_'+ order.id +'" style="width: 7em"> {{ __('order.prepared') }} </button></td>' ;
        //                                         }

        //                                         tableRow += '<td>'+ order.DiffForHumans+'</td>' +
        //                                         '<td>' ;
        //                                             // '<button class="btn btn-primary btn-sm order-products" data-url="'+ URLShowProduct +'" data-method="get"> <i class="fa fa-list"></i> {{ __('order.show') }} </button>' ;
        //                                             tableRow += '<button class="btn btn-primary btn-sm order_products" data-url="'+ URLShowProduct +'" data-method="get" > <i class="fa fa-list"></i> {{ __('order.show') }} </button>' ;

        //                                             if ({{ Auth::user()->hasPermission('orders-update') }}) {
        //                                                 tableRow += '<a href="'+ URLEditOrder +'" class="btn btn-warning btn-sm" style="margin:0px 3px;"><i class="fa fa-pencil"></i> {{ __('order.edit') }}</a>' ;
        //                                             } else {
        //                                                 tableRow += '<button  disabled class="btn btn-warning btn-sm" style="margin:0px 3px;"><i class="fa fa-edit"></i> {{ __('order.edit') }}</button>' ;
        //                                             }

        //                                             if ({{ Auth::user()->hasPermission('orders-delete') }}) {
        //                                                 tableRow += '<button class="btn btn-danger btn-sm delete delete_order" id="'+ order.id+'" customer_name="'+ customer.name +'"><i class="fa fa-trash"></i> {{ __('order.delete') }}</button>' ;
        //                                             } else {
        //                                                 tableRow += '<button class="btn btn-danger btn-sm" disabled><i class="fa fa-trash"></i> {{ __('order.delete') }}</button>' ;
        //                                             }





        //                                         tableRow += '</td>'+

        //                                     '</tr>' ;


        //                         // tableRow += '</tr>' ;
        //                         $("#tbody_order").append(tableRow);
        //                     }
        //                 }) ;
        //             }
        //         }
        //     }) ;
        // }) ;

    </script>

@stop
