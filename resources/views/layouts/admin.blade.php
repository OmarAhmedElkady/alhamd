<!DOCTYPE html>
<html >
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title')</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets\admin\img\logo.jpg')}}">

        <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/skin-blue.min.css') }}">


        @if (app()->getLocale() == 'ar')
            <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/font-awesome-rtl.min.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/AdminLTE-rtl.min.css') }}">
            <link href="https://fonts.googleapis.com/css?family=Cairo:400,700" rel="stylesheet">
            <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/bootstrap-rtl.min.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/rtl.css') }}">

            <style>
                body, h1, h2, h3, h4, h5, h6 {
                    font-family: 'Cairo', sans-serif !important;
                }
            </style>
        @else
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
            <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/font-awesome.min.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/AdminLTE.min.css') }}">
        @endif



        <style>
            .mr-2{
                margin-right: 5px;
            }

            .loader {
                border: 5px solid #f3f3f3;
                border-radius: 50%;
                border-top: 5px solid #367FA9;
                width: 60px;
                height: 60px;
                -webkit-animation: spin 1s linear infinite; /* Safari */
                animation: spin 1s linear infinite;
            }

            /* Safari */
            @-webkit-keyframes spin {
                0% {
                    -webkit-transform: rotate(0deg);
                }
                100% {
                    -webkit-transform: rotate(360deg);
                }
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }

        </style>

        {{--<!-- jQuery 3 -->--}}
        <script src="{{ URL::asset('assets/admin/js/jquery.min.js') }}"></script>

        {{--noty--}}
        <link rel="stylesheet" href="{{ URL::asset('assets/admin/plugins/noty/noty.css') }}">
        <script src="{{ URL::asset('assets/admin/plugins/noty/noty.min.js') }}"></script>

        {{--morris--}}
        <link rel="stylesheet" href="{{ URL::asset('assets/admin/plugins/morris/morris.css') }}">

        {{--<!-- iCheck -->--}}
        <link rel="stylesheet" href="{{ URL::asset('assets/admin/plugins/icheck/all.css') }}">

        {{--html in  ie--}}
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    </head>
    <body class="hold-transition skin-blue sidebar-mini">

        <div class="wrapper">


            @include('admin.includes.header')

            @include('admin.includes.sidebar')

            @yield('content')

            @include('admin.partials._session')

            @include('admin.includes.footer')

        </div><!-- end of wrapper -->

        {{--<!-- Bootstrap 3.3.7 -->--}}
        <script src="{{ URL::asset('assets/admin/js/bootstrap.min.js') }}"></script>

        {{--icheck--}}
        <script src="{{ URL::asset('assets/admin/plugins/icheck/icheck.min.js') }}"></script>

        {{--<!-- FastClick -->--}}
        <script src="{{ URL::asset('assets/admin/js/fastclick.js') }}"></script>

        {{--<!-- AdminLTE App -->--}}
        <script src="{{ URL::asset('assets/admin/js/adminlte.min.js') }}"></script>

        {{--ckeditor standard--}}
        <script src="{{ URL::asset('assets/admin/plugins/ckeditor/ckeditor.js') }}"></script>

        {{--jquery number--}}
        <script src="{{ URL::asset('assets/admin/js/jquery.number.min.js') }}"></script>

        {{--print this--}}
        <script src="{{ URL::asset('assets/admin/js/printThis.js') }}"></script>

        {{--morris --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="{{ URL::asset('assets/admin/plugins/morris/morris.min.js') }}"></script>



        {{--custom js--}}
        <script src="{{ URL::asset('assets/admin/dashboard_files/js/custom/image_preview.js') }}"></script>
        <script src="{{ URL::asset('assets/admin/dashboard_files/js/custom/order.js') }}"></script>


        <script>
            $(document).ready(function () {

                $('.sidebar-menu').tree();

                //icheck
                $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue',
                    radioClass: 'iradio_minimal-blue'
                });

                //delete
                // $('.delete').click(function (e) {

                //     var that = $(this)

                //     e.preventDefault();

                //     var n = new Noty({
                //         text: "@lang('site.confirm_delete')",
                //         type: "warning",
                //         killer: true,
                //         buttons: [
                //             Noty.button("@lang('site.yes')", 'btn btn-success mr-2', function () {
                //                 that.closest('form').submit();
                //             }),

                //             Noty.button("@lang('site.no')", 'btn btn-primary mr-2', function () {
                //                 n.close();
                //             })
                //         ]
                //     });

                //     n.show();

                // });//end of delete

                // // image preview
                // $(".image").change(function () {
                //
                //     if (this.files && this.files[0]) {
                //         var reader = new FileReader();
                //
                //         reader.onload = function (e) {
                //             $('.image-preview').attr('src', e.target.result);
                //         }
                //
                //         reader.readAsDataURL(this.files[0]);
                //     }
                //
                // });

                CKEDITOR.config.language =  "{{ app()->getLocale() }}";

            });//end of ready

        </script>
        @stack('scripts')
        @yield('scripts')
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    </body>
</html>
