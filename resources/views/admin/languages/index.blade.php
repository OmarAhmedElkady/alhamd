@extends('layouts.admin')

@section('title' , __('language.language'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{__('language.language')}}</h1>

            <ol class="breadcrumb">
                <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i> {{__('sidebar.dashboard')}}</a></li>
                <li class="active">{{__('language.language')}}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                {{-- <div class="box-header with-border">

                </div><!-- end of box header --> --}}

                @if (isset($languages) && $languages->count() > 0)

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">{{__('language.language')}}<small></small></h3>
                        <form action="" method="">

                            <div class="row">

                                {{-- <div class="col-md-4">
                                    <input type="text" name="search_language" id="search_language" class="form-control" placeholder="{{__('user.search')}}" value="">
                                </div> --}}

                                <div class="col-md-4">
                                    {{-- <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {{__('user.search')}}</button> --}}

                                    @if (auth::user()->hasRole('super_admin'))
                                        <a href="{{route('admin.language.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i>{{__('user.add')}}</a>
                                    @endif
                                </div>

                            </div>
                        </form><!-- end of form -->
                    </div>
                    {{-- <p style="text-align:center; display:none;" id="searching">{{__('user.searching')}}</p> --}}
                    {{-- <h3 style="text-align:center; margin-top:50px; display:none;" id="not_found_name"></h3> --}}
                    <div class="box-body">
                        <div class="table-responsive justify-content-center">
                            <table class="table table-hover" id="table_admin">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('language.name')}}</th>
                                    <th>{{__('language.abbr')}}</th>
                                    <th>{{ __('language.action') }}</th>
                                </tr>
                                </thead>

                                <tbody id="tbody">
                                @foreach ($languages as $index=>$language)
                                    <tr id="row_language_{{$language->id}}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $language->name }}</td>
                                        <td>{{ $language->abbr }}</td>

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

                        </div>
                            {{$languages->links()}}
                            {{-- {{ $users->appends(request()->query())->links() }} --}}

                    </div><!-- end of box body -->
                @else
                    <h4 style="padding:30px 5px ; font-size:1.4em"><p>{{__('language.no_language')}}</p>
                        @if (Auth::user()->hasrole('super_admin'))
                            <a href="{{route('admin.language.create')}}" >{{__('language.add_language')}}</a>
                        @endif
                    </h4>
                @endif

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@stop

@section('scripts')
    <script>
        $(document).on('click' , '.delete_language' , function(e){
            // var that = $(this)
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var language_id = $(this).attr('language_id') ;
            var language_name = $(this).attr('language_name') ;

            var n = new Noty({
                text: "{{__('language.confirm_delete_language')}} [ " + language_name + " ] <br>{{ __('language.very_important_note') }} <br> {{ __('language.product_deletion_message') }} [ " + language_name + " ]",
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('language.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{ route('admin.language.delete') }}" ,
                            data : {
                                '_token'     : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'id'        :   language_id
                            } , success : function ( data ) {
                                if (data.status == 'true') {
                                    $("#row_language_" + data.id).remove() ;
                                }
                            }
                        }) ;
                    }),

                    Noty.button("{{__('language.no')}}", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });

            n.show();
        }) ;

    </script>
@stop
