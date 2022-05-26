@extends('layouts.admin')

@section('title' , __('dashboard.dashboard'))

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>{{ __('dashboard.dashboard') }}</h1>

            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> {{ __('dashboard.dashboard') }}</li>
            </ol>
        </section>

        <section class="content">

            <div class="row">

                {{-- categories--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{ $categories_count }}</h3>

                            <p>{{ __('dashboard.categories') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route('admin.category.index') }}" class="small-box-footer">{{ __('dashboard.read') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--products--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>{{ $products_count }}</h3>

                            <p>{{ __('dashboard.products') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ route('admin.product.index') }}" class="small-box-footer">{{ __('dashboard.read') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--clients--}}
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>{{ $customers_count }}</h3>

                            <p>{{ __('dashboard.customers') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <a href="{{ route('admin.customer.index') }}" class="small-box-footer">{{ __('dashboard.read') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{--users--}}
                 <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>{{ $users_count }}</h3>

                            <p>{{ __('dashboard.users') }}</p>
                        </div>
                        <div class="icon">

                            <i class="fa fa-user"></i>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">{{ __('dashboard.read') }} <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div><!-- end of row -->

            <div style="margin-top:100px">
                <h4 style="text-align: center;">{{ __('dashboard.luminous_graph') }}</h4>
                <div style="width:100%;">
                    {!! $chartjs->render() !!}
                </div>
            </div>

            {{-- paidAndUnpaidAmountStatistics --}}

            <div style="width:100%; margin-top:100px ; background-color:#aee; padding: 20px 0px;">
                <h4 style="text-align: center;">{{ __('dashboard.percentage_of_paid_and_unpaid_amount') }}</h4>
                <div style="width:80%; margin: auto;">
                    {!! $paidAndUnpaidAmountStatistics->render() !!}
                </div>
            </div>

            <div style="height: 1000px"></div>
        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


@endsection
