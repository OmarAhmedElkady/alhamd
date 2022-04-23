@extends('layouts.admin')

@section('title' , __('products.add_new_product'))

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __('products.add_new_product') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.product.index') }}"> {{ __('products.products') }}</a></li>
                <li class="active">{{ __('products.add') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{ __('products.add') }}</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    <form id="form_product" method="POST">
                        @csrf
                        @method('POST')


                        @if ($categories != null && $categories->count() > 0)

                            @if (isset($languages) && $languages->count() > 0)


                                <div class="form-group">
                                    <label>{{ __('categories.categories') }}</label>
                                    <select name="category_id" class="form-control" style="padding-top:0px;">
                                        <option value="">...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->translation_of }}" >{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="product_category_id_error"></span>
                                </div>

                                <input type="hidden" id="count_languages" value="{{ $languages->count() }}">
                                @foreach ($languages as $key => $lang)
                                    <div class="form-group">
                                        <label>{{ __('products.name_in_language') . $lang->name }}</label>
                                        <input type="text" name="product[{{ $key }}][name]" class="form-control" value="">
                                        <span class="text-danger" id="product_{{ $key }}_name_error"></span>
                                    </div>
                                @endforeach


                                <div class="form-group">
                                    <label>{{ __('products.image') }}</label>
                                    <input type="file" name="image" class="form-control image">
                                </div>

                                <div class="form-group">
                                    <img src="{{ URL::asset('assets\admin\img\product.JPG') }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                                    <span class="text-danger" id="product_image_error"></span>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('products.purchasing_price') }}</label>
                                    <input type="number" name="purchasing_price" step="0.01" class="form-control">
                                    <span class="text-danger" id="product_purchasing_price_error"></span>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('products.pharmacist_price') }}</label>
                                    <input type="number" name="pharmacist_price" step="0.01" class="form-control">
                                    <span class="text-danger" id="product_pharmacist_price_error"></span>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('products.selling_price') }}</label>
                                    <input type="number" name="selling_price" step="0.01" class="form-control">
                                    <span class="text-danger" id="product_selling_price_error"></span>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('products.store') }}</label>
                                    <input type="number" name="store" class="form-control">
                                    <span class="text-danger" id="product_store_error"></span>
                                </div>

                                <div class="form-group">
                                    <button id="add_product" class="btn btn-primary"><i class="fa fa-plus"></i>{{ __('products.add') }}</button>
                                </div>

                                <h3 id="please_wait_product" style="display:none; text-align:center">
                                    {{__('products.please_wait')}}
                                </h3>

                                <div class="alert alert-danger" role="alert" id="fail_message_add_new_product" style="display:none; text-align:center;">
                                    {{__('language.fail_add_language')}}
                                </div>

                            @else

                                @if (Auth::user()->hasRole('super_admin'))
                                    <h4>{{ __('categories.please_add_a_language_to_the_site_first') }} <br><br>
                                        <a href="{{route('admin.language.create')}}" >{{__('language.add_language')}}</a>
                                    </h4>

                                @else
                                    <h3 style="text-align:center;">{{ __("categories.wait_to_add_a_language") }}</h3>
                                @endif

                            @endif



                        @else
                            @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasPermission('categories-create'))
                                <h4>{{ __('categories.please_add_a_category_first') }} <br><br>
                                    <a href="{{route('admin.category.create')}}" >{{__('categories.add_new_category')}}</a>
                                </h4>
                            @else
                                <h3 style="text-align:center;">{{ __('categories.wait_to_add_a_category') }}</h3>
                            @endif
                        @endif



                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection


@section('scripts')

<script>
    $(document).on('click' , '#add_product' , function(e){

        e.preventDefault() ;

        $("#please_wait_product").show() ;
        $("#fail_message_add_new_product").hide() ;

        var count_languages = $("#count_languages").val() ;
        for ( var i = 0 ; i < count_languages ; i++ )   {
            $("#product_" + i + "_name_error" ).text("");
        }

        $("#product_category_id_error").text("");
        $("#product_image_error").text("");
        $("#product_purchasing_price_error").text("");
        $("#product_pharmacist_price_error").text("");
        $("#product_selling_price_error").text("");
        $("#product_store_error").text("");

        var form_product = new FormData($('#form_product')[0]) ;

        $.ajax({
            type : 'POST' ,
            enctype : "multipart/form-data" ,
            url  : "{{route('admin.product.store')}}" ,
            data : form_product ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait_product").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('products.success_add_product')}}",
                        timeout: 2000,
                        killer: true
                    }).show();

                }   else  {

                    $("#fail_message_add_new_product").show() ;
                }

            } , error : function ( errorMessages )    {
                $("#please_wait_product").hide() ;
                var messages = JSON.parse(errorMessages.responseText);

                $.each(messages.errors , function (key , val) {

                    key = key.replace(/[.]/g , '_') ;
                    $("#" + key + "_error").text(val);  // It shows product name errors

                    $("#product_" + key + "_error").text(val);
                });
            }
        }) ;
    }) ;
</script>


@stop
