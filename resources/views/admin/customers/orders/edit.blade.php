@extends('layouts.admin')

@section('title', __('order.edit'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{ $customer->name }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i>
                        {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.all_order.index') }}"> {{ __('order.orders') }}</a></li>
                <li class="active">{{ __('order.edit') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                <div class="col-md-6">

                    <div class="box box-primary">

                        <div class="box-header">

                            <h3 class="box-title" style="margin-bottom: 10px">{{ __('order.edit') }}</h3>

                        </div><!-- end of box header -->

                        <div class="box-body">

                            @if (isset($categories) && $categories->count() > 0)


                                @foreach ($categories as $index => $category)
                                    <div class="panel-group">

                                        <div class="panel panel-info">

                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse"
                                                        href="#{{ str_replace(' ', '-', $category->name) }}">{{ $category->name }}</a>
                                                </h4>
                                            </div>

                                            <div id="{{ str_replace(' ', '-', $category->name) }}"
                                                class="panel-collapse collapse">

                                                <div class="panel-body">

                                                    @if ($products[$index]->count() > 0)
                                                        <table class="table table-hover">
                                                            <input type="text" name="search"
                                                                class="form-control search_in_category"
                                                                id="{{ $category->translation_of }}"
                                                                customer_id="{{ $customer->translation_of }}"
                                                                placeholder="{{ __('user.search') }}">
                                                            <p style="text-align:center; display:none;"
                                                                id="searching_category_{{ $category->translation_of }}">
                                                                {{ __('user.searching') }}</p>

                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('order.name') }}</th>
                                                                    <th>{{ __('order.store') }}</th>
                                                                    <th>{{ __('order.price') }}</th>
                                                                    <th>{{ __('order.add') }}</th>
                                                                </tr>
                                                            </thead>

                                                            @php
                                                                $all_order_products = [];
                                                                foreach ($order->product_order as $key => $product_order) {
                                                                    $all_order_products[] = $product_order->product_id;
                                                                }

                                                            @endphp

                                                            <tbody id="tbody_category_{{ $category->translation_of }}">
                                                                @foreach ($products[$index] as $index => $product)
                                                                    <p>
                                                                        <tr>
                                                                            <td>{{ $product->name }}</td>
                                                                            <td>{{ $product->store }}</td>

                                                                            @if ($customer->client_permissions == 'pharmaceutical')
                                                                                <td>{{ number_format($product->pharmacist_price, 2) }}
                                                                                </td>
                                                                            @elseif ($customer->client_permissions == 'customer')
                                                                                <td>{{ number_format($product->selling_price, 2) }}
                                                                                </td>
                                                                            @else
                                                                                <td>{{ number_format($product->ProductPriceAccordingToCustomerType, 2) }}
                                                                                </td>
                                                                            @endif
                                                                            <td>
                                                                                <a href=""
                                                                                    id="product-{{ $product->translation_of }}"
                                                                                    data-name="{{ $product->name }}"
                                                                                    data-id="{{ $product->translation_of }}"
                                                                                    @if (isset($product->price) && $product->price > 0) data-price="{{ $product->price }}"
                                                                            @else
                                                                                data-price="{{ $product->ProductPriceAccordingToCustomerType }}" @endif
                                                                                    class="btn btn-sm add-product-btn {{ in_array($product->translation_of, $all_order_products) ? 'btn-default disabled' : 'btn-success add-product-btn' }}">
                                                                                    <i class="fa fa-plus"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                @endforeach
                                                            </tbody>

                                                            <p style="display: none" id="products_select">
                                                                {{ implode(' ', $all_order_products) }}</p>

                                                        </table><!-- end of table -->

                                                        <p style="text-align:center; margin-top:20px; display:none;"
                                                            id="not_found_product_{{ $category->translation_of }}"></p>
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

                            <h3 class="box-title">{{ __('customers.' . $customer->client_permissions) }}</h3>

                        </div><!-- end of box header -->

                        <div class="box-body">

                            <form action="{{ route('admin.order.update') }}" method="post">
                                @csrf
                                @method('POST')

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <p style="text-align: center">{{ __('order.fail_add_category') }}</p>
                                    </div>
                                @endif

                                <input type="hidden" name="client_id" value="{{ $customer->translation_of }}">
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('order.product') }}</th>
                                            <th>{{ __('order.quantity') }}</th>
                                            <th>{{ __('order.price') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody class="order-list">

                                        @php
                                            $total_price = 0;
                                            $price = 1;
                                        @endphp
                                        @if (isset($order->product_order) && $order->product_order->count() > 0)
                                            @foreach ($order->product_order as $product_order)

                                                @if (isset($product_order->product[0]))

                                                    @php

                                                        $id = $product_order->product[0]->translation_of;

                                                        if ($customer->client_permissions == 'pharmaceutical') {
                                                            $price  = $product_order->product[0]->pharmacist_price ;
                                                        }   elseif ($customer->client_permissions == 'customer') {
                                                            $price  = $product_order->product[0]->selling_price ;
                                                        }   else    {
                                                            $price  = $product_order->product[0]->ProductPriceAccordingToCustomerType ;
                                                        }

                                                        $total_price += $price * $product_order->quantity ;

                                                    @endphp
                                                    <tr>
                                                        <td>{{ $product_order->product[0]->name }}</td>
                                                        <td>
                                                            <input type="number"
                                                                name="products[{{ $id }}][quantity]"
                                                                data-price="{{ $price }}}"
                                                                class="form-control input-sm product-quantity" min="1"
                                                                value="{{ $product_order->quantity }}">
                                                        </td>
                                                        <td class="product-price">
                                                            {{ number_format($price * $product_order->quantity, 2) }}
                                                        </td>
                                                        <td><button class="btn btn-danger btn-sm remove-product-btn"
                                                                data-id="{{ $id }}"><span
                                                                    class="fa fa-trash"></span></button></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif

                                    </tbody>

                                </table><!-- end of table -->

                                <h4>{{ __('order.total') }} : <span
                                        class="total-price">{{ number_format($total_price, 2) }}</span></h4>

                                <button class="btn btn-primary btn-block" id="add-order-form-btn">
                                    <i class="fa fa-edit"></i> {{ __('order.edit_order') }}</button>

                            </form>

                        </div><!-- end of box body -->

                    </div><!-- end of box -->

                </div><!-- end of col -->

            </div><!-- end of row -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection



@section('scripts')

    <script>
        $(".search_in_category").on("keyup", function() {

            // e.preventDefault() ;

            var category_id = $(this).attr('id');
            var customer_id = $(this).attr('customer_id');


            $("#searching_category_" + category_id).show(); // رساله جارى البحث

            var value = $(this).val(); // Search جلب القيمه الموجوده فى حقل

            $.ajax({
                type: 'GET',
                dataType: "json",
                url: "{{ route('admin.order.search') }}",
                data: {
                    '_token': "{{ csrf_token() }}", // تشفير البيانات
                    'category_id': category_id,
                    'customer_id': customer_id,
                    'search': value,
                },
                success: function(data) {


                    $("#searching_category_" + category_id).hide(); // إخفاء رساله جارى البحث

                    // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
                    if (data.status == false) {

                        $("#tbody_category_" + category_id).hide();

                        $("#not_found_product_" + category_id)
                    .show(); // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
                        $("#not_found_product_" + category_id).text(
                            "{{ __('products.this_product') }} [ " + data.search +
                            " ] {{ __('products.not_found') }}");


                    } else {
                        $("#not_found_product_" + category_id).hide(); // <h3></h3> إخفاء
                        $("#tbody_category_" + category_id).show(); // قم بإظهار الجدول

                        var tableRow = ''; // هذا المتغير الذى يحفظ الصفوف
                        $("#tbody_category_" + category_id).html(''); // إعطاء قيمه فارغه للجدول



                        var products_select = $("#products_select").text();
                        var products_select = products_select.split(" ");


                        // على البيانات loop عمل
                        $.each(data.data, function(index, value) {

                            var price = 1;
                            if (data.client_permissions == "pharmaceutical") {
                                price = value.pharmacist_price;
                            } else if (data.client_permissions == "customer") {
                                price = value.selling_price;
                            } else {
                                price = value.ProductPriceAccordingToCustomerType;
                            }

                            tableRow = '<tr>' +
                                '<td>' + value.name + '</td>' +
                                '<td>' + value.store + '</td>' +
                                '<td>' + price.toLocaleString('en-US') + '</td>';

                            if (products_select.indexOf(value.translation_of.toString()) >= 0) {
                                tableRow += '<td>' +
                                    '<a ' +
                                    'id="product-' + value.translation_of + '" ' +
                                    'data-name="' + value.name + '" ' +
                                    'data-id="' + value.translation_of + '" ' +
                                    'data-price="' + price + '"' +
                                    'class="btn btn-sm btn-default disabled add-product-btn "> ' +
                                    '<i class="fa fa-plus"></i> ' +
                                    '</a> ' +
                                    '</td> ';
                            } else {
                                tableRow += '<td>' +
                                    '<a ' +
                                    'id="product-' + value.translation_of + '" ' +
                                    'data-name="' + value.name + '" ' +
                                    'data-id="' + value.translation_of + '" ' +
                                    'data-price="' + price + '"' +
                                    'class="btn btn-sm btn-success add-product-btn "> ' +
                                    '<i class="fa fa-plus"></i> ' +
                                    '</a> ' +
                                    '</td> ';
                            }

                            '</tr>';

                            $("#tbody_category_" + category_id).append(tableRow);
                        });
                    }
                }
            });
        });
    </script>
@stop
