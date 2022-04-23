<header class="main-header">

    {{--<!-- Logo -->--}}
    <a href="{{ URL::asset('dashboard') }}/index2.html" class="logo">
        {{--<!-- mini logo for sidebar mini 50x50 pixels -->--}}
        <span class="logo-mini"><b>ALM</b></span>
        <span class="logo-lg"><b>ALHAMD</b></span>
    </a>

    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->
                {{-- <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">4</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 4 messages</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- start message -->
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="{{ URL::asset('assets/admin/img/avatar2.png') }}" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                            Support Team
                                            <small>
                                                <i class="fa fa-clock-o"></i> 5 mins
                                            </small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">See All Messages</a>
                        </li>
                    </ul>
                </li> --}}

                {{--<!-- Notifications: style can be found in dropdown.less -->--}}
                {{-- <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">10</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 10 notifications</li>
                        <li>
                            <ul class="menu">
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all</a>
                        </li>
                    </ul>
                </li> --}}

                {{--<!-- Tasks: style can be found in dropdown.less -->--}}
                <li class="dropdown tasks-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-flag-o"></i></a>
                    <ul class="dropdown-menu">
                        <li>
                            {{--<!-- inner menu: contains the actual data -->--}}
                            <ul class="menu">
                                {{-- @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    <li>
                                        <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                            {{ $properties['native'] }}
                                        </a>
                                    </li>
                                @endforeach --}}
                                <li>
                                    <a  href="{{route('admin.language.selectLanguage' , 'ar')}}" style="color: #337ab7;"> العربيه </a>
                                </li>
                                <li>
                                    <a  href="{{route('admin.language.selectLanguage' , 'en')}}" style="color: #337ab7;"> English </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                {{--<!-- User Account: style can be found in dropdown.less -->--}}
                <li class="dropdown user user-menu">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ (Auth::id()) ? Auth::user()->photo : URL::asset('assets/admin/img/user/avatar5.png') }}" class="user-image" alt="User Image">
                    </a>
                    <ul class="dropdown-menu">

                        {{--<!-- User image -->--}}
                        <li class="user-header">
                            <img src="{{ (Auth::id()) ? Auth::user()->photo : URL::asset('assets/admin/img/user/avatar5.png') }}" class="img-circle" alt="User Image" style="margin-top:10px">

                            <p>
                                <small>@auth {{Auth::user()->name}} @endauth</small>
                            </p>
                        </li>

                        {{--<!-- Menu Footer-->--}}
                        <li class="user-footer">


                            <a href="{{route('users.log_out')}}" class="btn btn-default btn-flat">{{__('sidebar.log_out')}}</a>

                            {{-- <form id="logout-form" action="" method="POST" style="display: none;">
                                @csrf
                            </form> --}}

                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

</header>
