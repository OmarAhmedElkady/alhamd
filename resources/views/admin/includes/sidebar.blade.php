<aside class="main-sidebar">

    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{Auth::user()->photo}}" class="img-circle" alt="User Image" style="width:35px ; max-width:35px ; height:35px; max-height:35px; margin-top:7px ;">
            </div>
            <div class="pull-left info" style="padding-right:5px">
                <p>{{Auth::user()->name}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> {{__('sidebar.online')}}</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">

            @if (auth()->user()->hasRole('super_admin'))
                <li><a href="{{route('admin.Dashboard.index')}}"><i class="fa fa-th"></i><span>{{__('sidebar.dashboard')}}</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('categories-read') || auth()->user()->hasRole('super_admin'))
                <li><a href="{{route('admin.category.index')}}"><i class="ion ion-bag"></i><span>{{__('user.categories')}}</span></a></li>
            @endif

            @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasPermission('products-read'))
                <li><a href="{{route('admin.product.index')}}"><i class="ion ion-stats-bars"></i><span>{{__('products.products')}}</span></a></li>
            @endif

            @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasPermission('customers-read'))
                <li><a href="{{route('admin.customer.index')}}"><i class="fa fa-users"></i><span>{{__('customers.customers')}}</span></a></li>
            @endif

            @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasPermission('orders-read'))
                <li><a href="{{route('admin.all_order.index')}}"><i class="fa fa-bell-o"></i><span>{{__('order.orders')}}</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('users-read') || auth()->user()->hasRole('super_admin'))
                <li><a href="{{route('admin.users.index')}}"><i class="fa fa-user"></i>{{__('user.super_admin')}}</span></a></li>
            @endif

            @if (auth()->user()->hasRole('super_admin'))
            {{-- <i class="fa-solid fa-bookmark"></i> --}}
                <li><a href="{{route('admin.language.index')}}"><i class="fa fa-flag-o"></i><span>{{__('language.language')}}</span></a></li>
            @endif

            {{-- @auth
            <li><a href="{{route('users.log_out')}}"><i class="fa fa-th"></i><span>{{__('sidebar.log_out')}}</span></a></li>
            @endauth --}}




            {{-- @if (auth()->user()->hasPermission('read_categories')) --}}
                {{-- <li><a href=""><i class="fa fa-th"></i><span>@lang('site.categories')</span></a></li> --}}
            {{-- @endif --}}

            {{-- @if (auth()->user()->hasPermission('read_products')) --}}
                {{-- <li><a href=""><i class="fa fa-th"></i><span>@lang('site.products')</span></a></li> --}}
            {{-- @endif --}}

            {{-- @if (auth()->user()->hasPermission('read_clients')) --}}
                {{-- <li><a href=""><i class="fa fa-th"></i><span>@lang('site.clients')</span></a></li> --}}
            {{-- @endif --}}

            {{-- @if (auth()->user()->hasPermission('read_orders')) --}}
                {{-- <li><a href=""><i class="fa fa-th"></i><span>@lang('site.orders')</span></a></li> --}}
            {{-- @endif --}}

            {{-- @if (auth()->user()->hasPermission('read_users')) --}}
                {{-- <li><a href=""><i class="fa fa-th"></i><span>@lang('site.users')</span></a></li> --}}
            {{-- @endif --}}

            {{--<li><a href="{{ route('dashboard.categories.index') }}"><i class="fa fa-book"></i><span>@lang('site.categories')</span></a></li>--}}
            {{----}}
            {{----}}
            {{--<li><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-users"></i><span>@lang('site.users')</span></a></li>--}}

            {{--<li class="treeview">--}}
            {{--<a href="#">--}}
            {{--<i class="fa fa-pie-chart"></i>--}}
            {{--<span>الخرائط</span>--}}
            {{--<span class="pull-right-container">--}}
            {{--<i class="fa fa-angle-left pull-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="treeview-menu">--}}
            {{--<li>--}}
            {{--<a href="../charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="../charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="../charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="../charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
        </ul>

    </section>

</aside>
