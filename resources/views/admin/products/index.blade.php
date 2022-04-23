@extends('layouts.admin')

@section('title' , __('products.products'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{ __('products.products') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li class="active">{{ __('products.products') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                @if (isset($products) && $products->count() > 0)

                    <div class="box-header with-border">

                        <h3 class="box-title" style="margin-bottom: 15px">{{ __('products.products') }} <small>{{ $products->total() }}</small></h3>

                        <div class="row">

                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" id="search_product" placeholder="{{ __('user.search') }}" >
                            </div>

                            <div class="col-md-4">
                                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-create'))
                                    <a href="{{ route('admin.product.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('products.add') }}</a>

                                @else
                                    <button class="btn btn-primary disabled"><i class="fa fa-plus"></i> {{ __('products.add') }}</button>
                                @endif
                            </div>

                        </div>

                    </div><!-- end of box header -->

                    <p style="text-align:center; display:none;" id="productSearch">{{__('user.searching')}}</p>
                    <h3 style="text-align:center; margin-top:50px; display:none;" id="not_found_product"></h3>

                    <div class="box-body table-responsive">

                        <table class="table table-hover " id="table_product">

                            <thead style="white-space:nowrap;" >
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('products.name') }}</th>
                                    <th>{{ __('products.image') }}</th>
                                    <th>{{ __('products.category') }}</th>
                                    <th>{{ __('products.store') }}</th>
                                    <th>{{ __('products.purchasing_price') }}</th>
                                    <th>{{ __('products.pharmacist_price') }}</th>
                                    <th>{{ __('products.selling_price') }}</th>
                                    @if (Auth::user()->hasRole('super_admin'))
                                        <th>{{ __('products.a_pharmacist') }}</th>
                                        <th>{{ __('products.the_audience') }}</th>
                                        <th>{{ __('products.total_profit_from_the_pharmacist') }}</th>
                                        <th>{{ __('products.total_profit_from_the_audience') }}</th>
                                    @endif
                                    <th>{{ __('products.action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="tbody_product" style="white-space:nowrap;">
                            @foreach ($products as $index=>$product)
                                <tr id="row_product_{{ $product->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td >{{ $product->name }}</td>
                                    <td><img src="{{$product->image}}" style="width:5em;  max-width:5em; " class="img-thumbnail" alt=""></td>
                                    {{-- <td><img src="{{ URL::asset('$product->image') }}" alt=""></td> --}}
                                    <td>@if ( isset($product->category->name) && !empty($product->category->name) )  {{ $product->category->name }}  @endif</td>
                                    <td>{{ $product->store }}</td>
                                    <td>{{ $product->purchasing_price }}</td>
                                    <td>{{ $product->pharmacist_price }}</td>
                                    <td>{{ $product->selling_price }}</td>
                                    @if (Auth::user()->hasRole('super_admin'))
                                        <td>{{ $product->pharmacistProfitRatio }} %</td>
                                        <td>{{ $product->profitRateFromTheAudience }} %</td>
                                        <td>{{ $product->totalProfitFromThePharmacist }}</td>
                                        <td>{{ $product->totalProfitFromTheAudience }}</td>
                                    @endif
                                    <td>
                                        @if ( Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-update') )
                                            <a href="{{ route('admin.product.edit' , $product->translation_of) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> {{ __('products.edit') }}</a>
                                        @else
                                            {{-- <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('products.edit') }}</a> --}}
                                            <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('products.edit') }}</button>
                                        @endif
                                        @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-delete') )
                                            <button type="submit" class="btn btn-danger delete btn-sm delete_product" translation_of="{{ $product->translation_of }}" product_name="{{ $product->name }}"><i class="fa fa-trash"></i>{{ __('products.delete') }}</button>
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i>{{ __('products.delete') }}</button>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{$products->links()}}
                        {{-- {{ $products->appends(request()->query())->links() }} --}}

                    </div><!-- end of box body -->

                @else
                    <h2 style="padding: 10px 5px 40px;">
                        {{ __('products.not_found_products') }}
                        @if (Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('products-create') )
                            <a href="{{ route('admin.product.create') }}">{{ __('products.add_new_product') }}</a>
                        @endif

                    </h2>
                @endif

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@stop


@section('scripts')
    <script>
        $(document).on('click' , '.delete_product' , function(e){
            // var that = $(this)
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var translation_of = $(this).attr('translation_of') ;
            var product_name = $(this).attr('product_name') ;

            var n = new Noty({
                text: "{{__('products.confirm_delete_product')}} [ " + product_name + " ] " ,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('products.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{route('admin.product.delete')}}" ,
                            data : {
                                '_token'                : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'translation_of'        :   translation_of
                            } , success : function ( data ) {
                                if (data.status == 'true') {
                                    $("#row_product_" + data.id).remove() ;
                                }
                            }
                        }) ;
                    }),

                    Noty.button("{{__('products.no')}}", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });

            n.show();
        }) ;






        $("#search_product").on("keyup" , function(){

            $("#productSearch").show() ;  // رساله جارى البحث

            var value = $(this).val() ;  // Search جلب القيمه الموجوده فى حقل

            $.ajax({
                type : 'GET' ,
                dataType : "json" ,
                url  : "{{route('admin.product.search')}}" ,
                data : {
                    '_token'     : "{{csrf_token()}}" ,  // تشفير البيانات
                    'search'        :   value
                } , success : function ( data ) {

                    $("#productSearch").hide() ;    // إخفاء رساله جارى البحث

                    // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
                    if (data.status == false) {

                        $("#table_product").hide() ;  // إخفاء الجدول

                        $("#not_found_product").show() ; // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
                        $("#not_found_product").text( "{{__('products.this_product')}} [ " + data.search + " ] {{ __('products.not_found') }}") ;


                    }   else    {
                        $("#not_found_product").hide() ; // <h3></h3> إخفاء
                        $("#table_product").show() ;  // قم بإظهار الجدول

                        var tableRow = '' ;     // هذا المتغير الذى يحفظ الصفوف
                        $("#tbody_product").html('') ;  // إعطاء قيمه فارغه للجدول



                        // على البيانات loop عمل
                        $.each(data.data, function(index, value){

                            var translation_of = value.translation_of ; // translation_of => خاصه بالمستخدم
                            var nameproduct   = value.name ;
                            // خاص لكل مستخدم Route() السطر القادم يقوم بعمل
                            var URLedit = "{{ route('admin.product.edit' , ':translation_of') }}" ;
                            URLedit = URLedit.replace(':translation_of', translation_of);

                            // حفظ البيانات فى الجدول
                            tableRow = '<tr id="row_product_' + value.id +'">' +
                                            '<td>' + ++index + '</td>'+
                                            '<td>' + value.name +'</td>'+
                                            '<td><img src="'+ value.image +'" style="width:5em;  max-width:5em; " class="img-thumbnail" alt=""></td>';


                                            if (typeof value.category !== 'undefined' && value.category != null) {
                                                tableRow += '<td>' + value.category.name +'</td>' ;
                                            } else {
                                                tableRow += '<td></td>' ;
                                            }


                                            tableRow += '<td>' + value.store +'</td>'+
                                            '<td>' + value.purchasing_price +'</td>'+
                                            '<td>' + value.pharmacist_price +'</td>'+
                                            '<td>' + value.selling_price +'</td>' ;
                                            if ( "{{ Auth::user()->hasRole('super_admin') }}") {
                                                tableRow += '<td>%' + value.pharmacistProfitRatio +'</td>' +
                                                    '<td>%' + value.profitRateFromTheAudience  +'</td>' +
                                                    '<td>' + value.totalProfitFromThePharmacist +'</td>' +
                                                    '<td>' + value.totalProfitFromTheAudience +'</td>' ;
                                            }
                                            tableRow += '<td>' ;

                                                // var roles = value.roles[0] ; // ام لا product يقوم بمعرفه هل هذا الشخص
                                                var CheckPermissionEdit   = "{{ Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-update')  }}" ;
                                                var CheckPermissionDelete = "{{ Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-delete') }}" ;

                                                if( CheckPermissionEdit == 1 )    {
                                                    tableRow += '<a href='+URLedit+' class="btn btn-info btn-sm" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{ __('products.edit') }}</a>' ;
                                                }   else    {
                                                    tableRow += '<button class="btn btn-info btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{__('user.edit')}}</button>' ;
                                                }


                                                if( CheckPermissionDelete == 1 )    {
                                                    tableRow += '<button type="submit" class="btn btn-danger delete btn-sm delete_product" translation_of="'+translation_of+'" product_name="'+nameproduct+'"><i class="fa fa-trash"></i>{{ __('products.delete') }}</button>'

                                                }   else    {
                                                    tableRow += '<button class="btn btn-danger btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i> {{__('user.delete')}}</button>' ;
                                                }

                                            tableRow += '</td>'+

                                        '</tr>' ;


                            $("#tbody_product").append(tableRow);
                        }) ;
                    }
                } , error : function ( urll )    {

                }
            }) ;
        }) ;

    </script>
@stop
