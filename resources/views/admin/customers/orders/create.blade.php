@extends('layouts.admin')

@section('title' , __('order.add_a_new_order'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{ $customer->name }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.customer.index') }}"> {{ __('customers.customers') }}</a></li>
                <li class="active">{{ __('order.add_a_new_order') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                <div class="col-md-6">

                    <div class="box box-primary">

                        <div class="box-header">

                            <h3 class="box-title" style="margin-bottom: 10px">{{ __('order.add_a_new_order') }}</h3>

                        </div><!-- end of box header -->

                        <div class="box-body">

                            @if (isset($categories) && $categories->count() > 0)

                                <p style="display: none" id="products_select"></p>
                                @foreach ($categories as $index => $category)


                                    <div class="panel-group">

                                        <div class="panel panel-info">

                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#{{ str_replace(' ', '-', $category->name) }}">{{ $category->name }}</a>
                                                </h4>
                                            </div>

                                            <div id="{{ str_replace(' ', '-', $category->name) }}" class="panel-collapse collapse">

                                                <div class="panel-body">

                                                    @if ($products[$index]->count() > 0)

                                                        <table class="table table-hover">

                                                            <input type="text" name="search" class="form-control search_in_category" id="{{ $category->translation_of }}" customer_id="{{ $customer->translation_of }}" placeholder="{{ __('user.search') }}" >

                                                            <p style="text-align:center; display:none;" id="searching_category_{{ $category->translation_of }}">{{__('user.searching')}}</p>


                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('order.name') }}</th>
                                                                    <th>{{ __('order.store') }}</th>
                                                                    <th>{{ __('order.price') }}</th>
                                                                    <th>{{ __('order.add') }}</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="tbody_category_{{ $category->translation_of }}">

                                                                @foreach ($products[$index] as $product)

                                                                    <tr>
                                                                        <td>{{ $product->name }}</td>
                                                                        <td>{{ $product->store }}</td>

                                                                        @if ($customer->client_permissions == 'pharmaceutical')
                                                                            <td>{{ number_format($product->pharmacist_price, 2) }}</td>
                                                                        @elseif ($customer->client_permissions == 'customer')
                                                                            <td>{{ number_format($product->selling_price, 2) }}</td>
                                                                        @else
                                                                        <td>{{ number_format($product->ProductPriceAccordingToCustomerType, 2) }}</td>
                                                                        @endif

                                                                        <td>
                                                                            <a href=""
                                                                            id="product-{{ $product->translation_of }}"
                                                                            data-name="{{ $product->name }}"
                                                                            data-id="{{ $product->translation_of }}"
                                                                            @if (isset($product->price) && $product->price > 0)
                                                                                    data-price="{{ $product->price }}"
                                                                                @else
                                                                                    data-price="{{ $product->ProductPriceAccordingToCustomerType }}"
                                                                                @endif
                                                                            class="btn btn-success btn-sm add-product-btn">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>

                                                                @endforeach
                                                            </tbody>



                                                        </table><!-- end of table -->
                                                        <p style="text-align:center; margin-top:20px; display:none;" id="not_found_product_{{ $category->translation_of }}"></p>
                                                    @else
                                                        <h5>{{ __('order.no_orders') }}</h5>
                                                    @endif

                                                </div><!-- end of panel body -->

                                            </div><!-- end of panel collapse -->

                                        </div><!-- end of panel primary -->

                                    </div><!-- end of panel group -->

                                @endforeach

                            @else
                                <h5>{{ __('order.no_categories') }}</h5>
                            @endif
                        </div><!-- end of box body -->

                    </div><!-- end of box -->

                </div><!-- end of col -->

                <div class="col-md-6">

                    <div class="box box-primary">

                        <div class="box-header">

                            <h3 class="box-title">{{ __('customers.'.$customer->client_permissions) }}</h3>

                        </div><!-- end of box header -->

                        <div class="box-body">

                            <form action="{{ route('admin.order.store') }}" method="post">
                                @csrf
                                @method("POST")

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <p style="text-align: center">{{ __('order.fail_add_category') }}</p>
                                    </div>
                                @endif

                                <input type="hidden" name="client_id" value="{{ $customer->translation_of }}">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>{{ __('order.product') }}</th>
                                        <th>{{ __('order.quantity') }}</th>
                                        <th>{{ __('order.price') }}</th>
                                    </tr>
                                    </thead>

                                    <tbody class="order-list">


                                    </tbody>

                                </table><!-- end of table -->

                                <h4>{{ __('order.total') }} : <span class="total-price">0</span></h4>

                                <button class="btn btn-primary btn-block disabled" id="add-order-form-btn"><i class="fa fa-plus"></i> {{ __('order.add_order') }}</button>

                            </form>

                        </div><!-- end of box body -->

                    </div><!-- end of box -->


                    @if ($orders->count() > 0)

                        <div class="box box-primary">

                            <div class="box-header">

                                <h3 class="box-title" style="margin-bottom: 10px">{{ __('order.previous_orders') }}
                                    <small>{{ $orders->count() }}</small>
                                </h3>

                            </div><!-- end of box header -->

                            <div class="box-body">


                                @foreach ($orders as $order)

                                    <div class="panel-group">

                                        <div class="panel panel-success">

                                            <div class="panel-heading">
                                                <h4 class="panel-title">                                                        {{-- $order->created_at->toFormattedDateString() --}}
                                                    <a data-toggle="collapse" href="#{{ $order->created_at->format('d-m-Y-s') }}">{{ $order->created_at->Format('d / m / Y') }}</a>
                                                </h4>
                                            </div>

                                            <div id="{{ $order->created_at->format('d-m-Y-s') }}" class="panel-collapse collapse">


                                                <div class="panel-body">


                                                    <ul class="list-group">
                                                        @foreach ($order->product_order as $product_order)
                                                            @if (isset($product_order->product[0]->name))
                                                                <li class="list-group-item">{{ $product_order->product[0]->name }}</li>
                                                            @endif

                                                        @endforeach
                                                    </ul>

                                                </div><!-- end of panel body -->

                                            </div><!-- end of panel collapse -->

                                        </div><!-- end of panel primary -->

                                    </div><!-- end of panel group -->

                                @endforeach

                                {{ $orders->links() }}

                            </div><!-- end of box body -->

                        </div><!-- end of box -->

                    @endif

                </div><!-- end of col -->

            </div><!-- end of row -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection


@section("scripts")

    <script>

        $(".search_in_category").on("keyup" , function(){

            // e.preventDefault() ;

            var category_id = $(this).attr('id') ;
            var customer_id = $(this).attr('customer_id') ;


            $("#searching_category_" + category_id).show() ;  // رساله جارى البحث

            var value = $(this).val() ;  // Search جلب القيمه الموجوده فى حقل

            $.ajax({
                type : 'GET' ,
                dataType : "json" ,
                url  : "{{ route('admin.order.search') }}" ,
                data : {
                    '_token'     : "{{csrf_token()}}" ,  // تشفير البيانات
                    'category_id'   : category_id ,
                    'customer_id'   : customer_id ,
                    'search'        :   value ,
                } , success : function ( data ) {


                    $("#searching_category_" + category_id).hide() ;    // إخفاء رساله جارى البحث

                    // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
                    if (data.status == false) {

                        $("#tbody_category_" + category_id).hide() ;

                        $("#not_found_product_" + category_id).show() ; // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
                        $("#not_found_product_" + category_id).text( "{{__('products.this_product')}} [ " + data.search + " ] {{ __('products.not_found') }}") ;


                    } else    {
                        $("#not_found_product_" + category_id).hide() ; // <h3></h3> إخفاء
                        $("#tbody_category_" + category_id).show() ;  // قم بإظهار الجدول

                        var tableRow = '' ;     // هذا المتغير الذى يحفظ الصفوف
                        $("#tbody_category_" + category_id).html('') ;  // إعطاء قيمه فارغه للجدول



                        var products_select = $("#products_select").text() ;
                        var products_select = products_select.split(" ") ;


                        // على البيانات loop عمل
                        $.each(data.data, function(index, value){

                            var price = 1 ;
                            if (data.client_permissions == "pharmaceutical") {
                                price = value.pharmacist_price ;
                            } else if (data.client_permissions == "customer") {
                                price = value.selling_price ;
                            }   else   {
                                price = value.ProductPriceAccordingToCustomerType ;
                            }

                            tableRow = '<tr>' +
                                            '<td>' + value.name + '</td>'+
                                            '<td>' + value.store + '</td>' +
                                            '<td>' + price.toLocaleString('en-US') +'</td>' ;

                                            if (products_select.indexOf(value.translation_of.toString() ) != -1) {
                                                tableRow += '<td>'  +
                                                                '<a ' +
                                                                'id="product-'+ value.translation_of+'" ' +
                                                                'data-name="'+ value.name +'" ' +
                                                                'data-id="'+ value.translation_of+'" ' +
                                                                'data-price="'+ price +'"' +
                                                                'class="btn btn-sm btn-default disabled add-product-btn "> ' +
                                                                '<i class="fa fa-plus"></i> ' +
                                                                '</a> ' +
                                                            '</td> ' ;
                                            } else {
                                                tableRow += '<td>'  +
                                                                '<a ' +
                                                                'id="product-'+ value.translation_of+'" ' +
                                                                'data-name="'+ value.name +'" ' +
                                                                'data-id="'+ value.translation_of+'" ' +
                                                                'data-price="'+ price +'"' +
                                                                'class="btn btn-sm btn-success add-product-btn "> ' +
                                                                '<i class="fa fa-plus"></i> ' +
                                                                '</a> ' +
                                                            '</td> ' ;
                                            }

                                        '</tr>' ;

                            $("#tbody_category_" + category_id).append(tableRow);

                        }) ;
                    }
                }
            }) ;
        }) ;

    </script>
@stop
