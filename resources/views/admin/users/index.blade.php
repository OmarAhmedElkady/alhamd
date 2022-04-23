@extends('layouts.admin')

@section('title' , __('user.super_admin'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{__('user.super_admin')}}</h1>

            <ol class="breadcrumb">
                <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-dashboard"></i> {{__('sidebar.dashboard')}}</a></li>
                <li class="active">{{__('user.super_admin')}}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                {{-- <div class="box-header with-border">

                </div><!-- end of box header --> --}}

                @if (isset($users) && $users->count() > 0)

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">{{__('user.super_admin')}}<small></small></h3>
                        <form action="" method="">

                            <div class="row">

                                <div class="col-md-4">
                                    <input type="text" name="search_admin" id="search_admin" class="form-control" placeholder="{{__('user.search')}}" value="">
                                </div>

                                <div class="col-md-4">
                                    {{-- <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {{__('user.search')}}</button> --}}

                                    @if (auth::user()->hasRole('super_admin') || auth::user()->hasPermission('users-create'))
                                        <a href="{{route('admin.users.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i>{{__('user.add')}}</a>
                                    @else
                                        <button class="btn btn-primary disabled"><i class="fa fa-plus"></i> {{__('user.add')}}</button>
                                    @endif
                                </div>

                            </div>
                        </form><!-- end of form -->
                    </div>
                    <p style="text-align:center; display:none;" id="searching">{{__('user.searching')}}</p>
                    <h3 style="text-align:center; margin-top:50px; display:none;" id="not_found_name"></h3>
                    <div class="box-body">
                        <div class="table-responsive justify-content-center">
                            <table class="table table-hover" id="table_admin">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('user.name')}}</th>
                                    <th>{{__('user.email')}}</th>
                                    <th>{{__('user.photo')}}</th>
                                    <th>{{__('user.action')}}</th>
                                </tr>
                                </thead>

                                <tbody id="tbody">
                                @foreach ($users as $index=>$user)
                                    <tr id="row_user_{{$user->id}}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><img src="{{$user->photo}}" style="width:5em;  max-width:5em; " class="img-thumbnail" alt=""></td>
                                        <td>
                                            @if ((Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('users-update')) && $user->hasRole('admin') || $user->id == Auth::id())
                                                <a href="{{route('admin.users.edit' , $user->id)}}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> {{__('user.edit')}}</a>
                                            @else
                                                <button class="btn btn-info btn-sm disabled"><i class="fa fa-edit"></i> {{__('user.edit')}}</button>
                                            @endif

                                            @if ((Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('users-delete')) && $user->hasRole('admin') && $user->id != Auth::id())
                                                <a href="" class="btn btn-danger btn-sm delete_user" user_id="{{$user->id}}" user_name="{{$user->name}}"><i class="fa fa-trash"></i> {{__('user.delete')}}</a>
                                            @else
                                                <button class="btn btn-danger btn-sm disabled"><i class="fa fa-trash"></i> {{__('user.delete')}}</button>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>

                            </table><!-- end of table -->

                        </div>
                            {{$users->links()}}
                            {{-- {{ $users->appends(request()->query())->links() }} --}}

                    </div><!-- end of box body -->
                @else
                    <h4 style="padding:30px 5px ; font-size:1.4em"><p>{{__('user.no_super_admin')}}</p>
                        @if (Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('users-create'))
                            <a href="{{route('admin.users.create')}}" >{{__('user.add_super_admin')}}</a>
                        @endif
                    </h4>
                @endif

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@stop

@section('scripts')
    <script>
        $(document).on('click' , '.delete_user' , function(e){
            // var that = $(this)
            e.preventDefault() ; // للصفحه relode لكى تمنع عمل

            var user_id = $(this).attr('user_id') ;
            var user_name = $(this).attr('user_name') ;

            var n = new Noty({
                text: "{{__('user.confirm_delete_user')}}" + user_name ,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("{{__('user.yes')}}", 'btn btn-success mr-2', function () {
                        n.close();
                        $.ajax({
                            type : 'POST' ,
                            url  : "{{route('admin.users.delete')}}" ,
                            data : {
                                '_token'     : "{{csrf_token()}}" , // لكى تقوم بحمايه البيانات
                                'id'        :   user_id
                            } , success : function ( data ) {
                                if (data.status == 'true') {
                                    $("#row_user_" + data.id).remove() ;
                                }
                            } , error : function ( err )    {

                            }
                        }) ;
                    }),

                    Noty.button("{{__('user.no')}}", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });

            n.show();
        }) ;



        /////////////////////////////////////////////Search/////////////////////////////////////
        $("#search_admin").on("keyup" , function(){

            $("#searching").show() ;  // رساله جارى البحث

            var value = $(this).val() ;  // Search جلب القيمه الموجوده فى حقل

            $.ajax({
                type : 'GET' ,
                dataType : "json" ,
                url  : "{{route('admin.users.index')}}" ,
                data : {
                    '_token'     : "{{csrf_token()}}" ,  // تشفير البيانات
                    'search'        :   value
                } , success : function ( data ) {

                    $("#searching").hide() ;    // إخفاء رساله جارى البحث

                    // فهذا يعنى أنه لا يوجد بياناتfalse = إذا كانت القيمه الراجعه
                    if (data.status == false) {

                        $("#table_admin").hide() ;  // إخفاء الجدول

                        $("#not_found_name").show() ; // الذى نطبع بداخله رساله لا يوجد بيانات <h3></h3> إظهار
                        $("#not_found_name").text( "{{__('user.not_found_admin')}}" + data.search) ;

                    }   else    {
                        $("#not_found_name").hide() ; // <h3></h3> إخفاء
                        $("#table_admin").show() ;  // قم بإظهار الجدول

                        var tableRow = '' ;     // هذا المتغير الذى يحفظ الصفوف
                        $("#tbody").html('') ;  // إعطاء قيمه فارغه للجدول


                        // على البيانات loop عمل
                        $.each(data.data, function(index, value){

                            var id = value.id ; // id => خاصه بالمستخدم

                            // خاص لكل مستخدم Route() السطر القادم يقوم بعمل
                            var URLedit = "{{route('admin.users.edit' , ':id')}}" ;
                            URLedit = URLedit.replace(':id', id);

                            // حفظ البيانات فى الجدول
                            tableRow = '<tr id="row_user_' + value.id +'">' +
                                            '<td>' + ++index + '</td>'+
                                            '<td>' + value.name +'</td>'+
                                            '<td>' + value.email +'</td>'+
                                            '<td><img src=" ' + value.photo + '" style="width:5em; height:5em; max-width:5em; max-height:em; " class="img-thumbnail" alt=""></td>'+
                                            '<td>' ;

                                                var roles = value.roles[0] ; // ام لا Admin يقوم بمعرفه هل هذا الشخص
                                                var CheckPermissionEdit   = "{{ Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('users-update')  }}" ;
                                                var CheckPermissionDelete = "{{ Auth::user()->hasrole('super_admin') || Auth::user()->hasPermission('users-delete') }}" ;

                                                if( (CheckPermissionEdit == 1 && roles.name == 'admin') || id == {{Auth::id()}} )    {
                                                    tableRow += '<a href='+URLedit+' class="btn btn-info btn-sm" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{__('user.edit')}} </a>';

                                                }   else    {
                                                    tableRow += '<button class="btn btn-info btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-edit"></i> {{__('user.edit')}}</button>' ;
                                                }


                                                if( (CheckPermissionDelete == 1 && roles.name == 'admin') && id != {{Auth::id()}} )    {
                                                    tableRow += '<a href="" class="btn btn-danger btn-sm delete_user" user_id="' + value.id + '" user_name="'+ value.name + '" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i> {{__('user.delete')}}</a>' ;

                                                }   else    {
                                                    tableRow += '<button class="btn btn-danger btn-sm disabled" style="margin-left: 2px; margin-right:2px;"><i class="fa fa-trash"></i> {{__('user.delete')}}</button>' ;
                                                }

                                            tableRow += '</td>'+

                                        '</tr>' ;

                            $("#tbody").append(tableRow);
                        }) ;
                    }
                } , error : function ( urll )    {

                }
            }) ;
        }) ;


    </script>
@stop
