@extends('layouts.admin')

@section('title' , __('categories.categories'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{ __('categories.categories') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li class="active">{{ __('categories.categories') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                @if (isset($categories) && $categories->count() > 0)

                    <div class="box-header with-border">

                        <h3 class="box-title" style="margin-bottom: 15px">{{ __('categories.categories') }} <small>{{ $categories->total() }}</small></h3>

                        {{-- <form action="" method="get"> --}}
                            <div class="row">

                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" id="search_category" placeholder="{{ __('user.search') }}" >
                                </div>

                                <div class="col-md-4">
                                    {{-- <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {{ __('user.search') }}</button> --}}
                                    @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('categories-create'))
                                        <a href="{{ route('admin.category.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> {{ __('categories.add') }}</a>

                                    @else
                                        <button class="btn btn-primary disabled"><i class="fa fa-plus"></i> {{ __('categories.add') }}</button>
                                    @endif
                                </div>

                            </div>
                        {{-- </form><!-- end of form --> --}}

                    </div><!-- end of box header -->

                    <p style="text-align:center; display:none;" id="categorySearch">{{__('user.searching')}}</p>
                    <h3 style="text-align:center; margin-top:50px; display:none;" id="not_found_category"></h3>

                    <div class="box-body table-responsive">

                        <table class="table table-hover" id="table_category">

                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('categories.categories') }}</th>
                                <th>{{ __('categories.products_count') }}</th>
                                <th>{{ __('categories.view_products') }}</th>
                                <th>{{ __('categories.action') }}</th>
                            </tr>
                            </thead>

                            <tbody id="tbody_category">
                            @foreach ($categories as $index=>$category)
                                <tr id="row_category_{{ $category->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->products->count() }}</td>
                                    <td>
                                        @if ( (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-read')) && $category->products->count() > 0 )
                                            <a href="{{ route('admin.product.show' , $category->translation_of) }}" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> {{ __('categories.view_products') }}</a>
                                        @else
                                            {{-- <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('categories.edit') }}</a> --}}
                                            <button class="btn btn-info btn-sm disabled"><i class="fa fa-eye"></i> {{ __('categories.view_products') }}</button>
                                        @endif
                                    </td>
                                    {{-- <td><a href="" class="btn btn-info btn-sm">@lang('site.related_products')</a></td> --}}
                                    <td>
                                        @if ( Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('categories-update') )
                                            <a href="{{ route('admin.category.edit' , $category->translation_of) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> {{ __('categories.edit') }}</a>
                                        @else
                                            {{-- <a href="#" class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('categories.edit') }}</a> --}}
                                            <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('categories.edit') }}</button>
                                        @endif
                                        @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('categories-delete'))
                                            {{-- <form action="" method="post" style="display: inline-block"> --}}
                                                {{-- {{ csrf_field() }} --}}
                                                {{-- {{ method_field('delete') }} --}}
                                                <button type="submit" class="btn btn-danger delete btn-sm delete_category" translation_of="{{ $category->translation_of }}" category_name="{{ $category->name }}"><i class="fa fa-trash"></i>{{ __('categories.delete') }}</button>
                                            {{-- </form><!-- end of form --> --}}
                                        @else
                                            <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i>{{ __('categories.delete') }}</button>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{$categories->links()}}
                        {{-- {{ $categories->appends(request()->query())->links() }} --}}

                    </div><!-- end of box body -->

                @else
                    <h2 style="padding: 10px 5px 40px;">
                        {{ __('categories.not_found_categories') }}
                        @if (Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('categories-create'))
                            <a href="{{ route('admin.category.create') }}">{{ __('categories.add_new_category') }}</a>
                        @endif

                    </h2>
                @endif

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@stop


@section('scripts')
    <script>
        $(document).on('click' , '.delete_category' , function(e){
            // var that = $(this)
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var translation_of = $(this).attr('translation_of') ;
            var category_name = $(this).attr('category_name') ;

            var n = new Noty({
                text: "{{__('categories.confirm_delete_category')}} [ " + category_name + " ] <br>{{ __('categories.very_important_note') }} <br> {{ __('categories.product_deletion_message') }}" ,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('categories.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{route('admin.category.delete')}}" ,
                            data : {
                                '_token'                : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'translation_of'        :   translation_of
                            } , success : function ( data ) {
                                if (data.status == 'true') {
                                    $("#row_category_" + data.id).remove() ;
                                }
                            } , error : function ( err )    {

                            }
                        }) ;
                    }),

                    Noty.button("{{__('categories.no')}}", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });

            n.show();
        }) ;






        $("#search_category").on("keyup" , function(){

            $("#categorySearch").show() ;  // رساله جارى البحث

            var value = $(this).val() ;  // Search جلب القيمه الموجوده فى حقل

            $.ajax({
                type : 'GET' ,
                dataType : "json" ,
                url  : "{{route('admin.category.index')}}" ,
                data : {
                    '_token'     : "{{csrf_token()}}" ,  // تشفير البيانات
                    'search'        :   value
                } , success : function ( data ) {

                    $("#categorySearch").hide() ;    // إخفاء رساله جارى البحث

                    // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
                    if (data.status == false) {

                        $("#table_category").hide() ;  // إخفاء الجدول

                        $("#not_found_category").show() ; // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
                        $("#not_found_category").text( "{{__('categories.this_category')}} [ " + data.search + " ] {{ __('categories.not_found') }}") ;


                    }   else    {

                        $("#not_found_category").hide() ; // <h3></h3> إخفاء
                        $("#table_category").show() ;  // قم بإظهار الجدول

                        var tableRow = '' ;     // هذا المتغير الذى يحفظ الصفوف
                        $("#tbody_category").html('') ;  // إعطاء قيمه فارغه للجدول



                        // على البيانات loop عمل
                        $.each(data.data, function(index, value){

                            var translation_of = value.translation_of ; // translation_of => خاصه بالمستخدم
                            var nameCategory   = value.name ;
                            // console.log(translation_of) ;
                            // خاص لكل مستخدم Route() السطر القادم يقوم بعمل
                            var URLedit = "{{ route('admin.category.edit' , ':translation_of') }}" ;
                            URLedit = URLedit.replace(':translation_of', translation_of);

                            var URLshow = "{{ route('admin.product.show' , ':translation_of') }}" ;
                            URLshow = URLshow.replace(':translation_of', translation_of);

                            // حفظ البيانات فى الجدول
                            tableRow = '<tr id="row_category_' + value.id +'">' +
                                            '<td>' + ++index + '</td>'+
                                            '<td>' + value.name +'</td>'+
                                            '<td>' + value.products.length +'</td>'+
                                            '<td>' ;
                                                if( "{{ (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('products-read'))  }}" && value.products.length > 0 )    {
                                                    // tableRow += '<a href="    }}" class="btn btn-info btn-sm" ><i class="fa fa-eye"></i> {{ __('categories.view_products') }}</a>';
                                                    tableRow += '<a href='+URLshow+' class="btn btn-info btn-sm" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-eye"></i> {{ __('categories.view_products') }}</a>' ;

                                                }   else    {
                                                              // <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{ __('categories.view_products') }}</button>
                                                    tableRow += '<button class="btn btn-info btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-eye"></i> {{__('categories.view_products')}}</button>' ;
                                                }
                                            tableRow += '</td>'+
                                            '<td>' ;

                                                // var roles = value.roles[0] ; // ام لا category يقوم بمعرفه هل هذا الشخص
                                                var CheckPermissionEdit   = "{{ Auth::user()->hasrole('super_category') || Auth::user()->hasPermission('categories-update')  }}" ;
                                                var CheckPermissionDelete = "{{ Auth::user()->hasrole('super_category') || Auth::user()->hasPermission('categories-delete') }}" ;

                                                if( CheckPermissionEdit == 1 )    {
                                                    // tableRow += '<a href='+URLedit+' class="btn btn-info btn-sm" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{__('user.edit')}} </a>';
                                                    tableRow += '<a href='+URLedit+' class="btn btn-info btn-sm" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{ __('categories.edit') }}</a>' ;

                                                }   else    {
                                                    tableRow += '<button class="btn btn-info btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{__('user.edit')}}</button>' ;
                                                }


                                                if( CheckPermissionDelete == 1 )    {
                                                    tableRow += '<button type="submit" class="btn btn-danger delete btn-sm delete_category" translation_of="'+translation_of+'" category_name="'+nameCategory+'"><i class="fa fa-trash"></i>{{ __('categories.delete') }}</button>'
                                                    // tableRow += '<a href="" class="btn btn-danger btn-sm delete_user" user_id="' + value.id + '" user_name="'+ value.name + '" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i> {{__('user.delete')}}</a>' ;

                                                }   else    {
                                                    tableRow += '<button class="btn btn-danger btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i> {{__('user.delete')}}</button>' ;
                                                }

                                            tableRow += '</td>'+

                                        '</tr>' ;


                            $("#tbody_category").append(tableRow);
                        }) ;
                    }
                } , error : function ( urll )    {

                }
            }) ;
        }) ;

    </script>
@stop
