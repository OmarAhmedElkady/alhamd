@extends('layouts.admin')

@section('title' , __('products.edit'))

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __('products.edit') }}</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.Dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('sidebar.dashboard') }}</a></li>
                <li><a href="{{ route('admin.product.index') }}"> {{ __('products.products') }}</a></li>
                <li class="active">{{ __('products.edit') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{ __('products.edit') }}</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    <form id="form_edit_product" method="POST">
                        @csrf
                        @method('POST')


                        @if (($product != null && $product->count() > 0) && ($categories != null && $categories->count() > 0) )

                            <div class="form-group">
                                <label>{{ __('categories.categories') }}</label>
                                <select name="category_id" class="form-control" style="padding-top:0px;">
                                    <option value="">...</option>
                                    @foreach ($categories as $key => $category)
                                         <option value="{{ $category->translation_of }}" {{ ($category->translation_of == $product[0]->category_id) ? "selected" : "" }} >{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="edit_product_category_id_error"></span>
                            </div>
                            <input type="hidden" name="translation_of" id="translation_of" value="{{ $product[0]->translation_of }}">
                            <input type="hidden" id="count_languages" value="{{ $languages->count() }}">
                            @foreach ($languages as $key => $lang)
                                <div class="form-group">
                                    <label>{{ __('products.name_in_language') . $lang->name }}</label>
                                    @if ($key < $product->count())
                                        <input type="text" name="product[{{ $key }}][name]" class="form-control" value="{{ $product[$key]->name }}">
                                    @else
                                        <input type="text" name="product[{{ $key }}][name]" class="form-control" value="">
                                    @endif
                                    {{-- <input type="hidden" name="product[{{ $key }}][abbr]" value="{{ $lang->abbr }}"> --}}
                                    <span class="text-danger" id="edit_product_{{ $key }}_name_error"></span>
                                </div>
                            @endforeach


                            <div class="form-group">
                                <label>{{ __('products.image') }}</label>
                                <input type="file" name="image" class="form-control image">
                            </div>

                            <div class="form-group">
                                <img src="{{ $product[0]->image }}" style="width: 100px" class="img-thumbnail image-preview" alt="">
                                <span class="text-danger" id="edit_product_image_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('products.purchasing_price') }}</label>
                                <input type="number" name="purchasing_price" value="{{ $product[0]->purchasing_price }}" step="0.01" class="form-control">
                                <span class="text-danger" id="edit_product_purchasing_price_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('products.pharmacist_price') }}</label>
                                <input type="number" name="pharmacist_price" value="{{ $product[0]->pharmacist_price }}" step="0.01" class="form-control">
                                <span class="text-danger" id="edit_product_pharmacist_price_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('products.selling_price') }}</label>
                                <input type="number" name="selling_price" value="{{ $product[0]->selling_price }}" step="0.01" class="form-control">
                                <span class="text-danger" id="edit_product_selling_price_error"></span>
                            </div>

                            <div class="form-group">
                                <label>{{ __('products.store') }}</label>
                                <input type="number" name="store" value="{{ $product[0]->store }}" class="form-control">
                                <span class="text-danger" id="edit_product_store_error"></span>
                            </div>

                            <div class="form-group">
                                <button id="edit_product" class="btn btn-primary"><i class="fa fa-plus"></i>{{ __('products.edit') }}</button>
                            </div>

                            <h3 id="please_wait_product" style="display:none; text-align:center">
                                {{__('products.please_wait')}}
                            </h3>

                            <div class="alert alert-danger" role="alert" id="fail_message_add_new_product" style="display:none; text-align:center;">
                                {{__('language.fail_add_language')}}
                            </div>



                        @endif



                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection


@section('scripts')

<script>
    $(document).on('click' , '#edit_product' , function(e){

        e.preventDefault() ;

        $("#please_wait_product").show() ;
        $("#fail_message_add_new_product").hide() ;

        var translation_of = $("#translation_of").val() ;
        var count_languages = $("#count_languages").val() ;
        for ( var i = 0 ; i < count_languages ; i++ )   {
            $("#edit_product_" + i + "_name_error" ).text("");
        }

        $("#edit_product_category_id_error").text("");
        $("#edit_product_image_error").text("");
        $("#edit_product_purchasing_price_error").text("");
        $("#edit_product_pharmacist_price_error").text("");
        $("#edit_product_selling_price_error").text("");
        $("#edit_product_store_error").text("");

        var form_edit_product = new FormData($('#form_edit_product')[0]) ;

        $.ajax({
            type : 'POST' ,
            enctype : "multipart/form-data" ,
            url  : "{{route('admin.product.update')}}" ,
            data : form_edit_product ,
            processData : false ,
            contentType : false ,
            cache : false ,
            success : function ( data ) {
                $("#please_wait_product").hide() ;
                if (data.status == true) {
                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: "{{__('products.success_edit_product')}}",
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
                    $("#edit_" + key + "_error").text(val);  // It shows product name errors

                    $("#edit_product_" + key + "_error").text(val);
                });
            }
        }) ;
    }) ;
</script>


@stop
