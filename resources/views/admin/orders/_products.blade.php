@if ( isset($order) && $order->count() > 0)

    <div id="print-area" class="table-responsive" >

        <div class="row" id="header" style="display: none">
            <div class="col-xs-4">
                <table class="table table-hover table-bordered">
                    <tr>
                        <td style="padding: 0px;">{{ __('order.date') }}</td>
                        <td style="padding: 0px;">{{ $order->created_at->format('Y/m/d') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 0px;">{{ __('order.client_name') }}</td>
                        <td style="padding: 0px;">{{ $order->customer->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 0px;">{{ __('order.address') }}</td>
                        <td style="padding: 0px;">{{ $order->customer->title }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 0px;">{{ __('order.phone') }}</td>
                        <td style="padding: 0px;">{{ $order->customer->phone }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-xs-4">
                <h3 style="text-align: center;" class="company_name">{{ __('order.alhamd_company') }}</h3>
                <h4 style="text-align: center;">{{ __('order.trading_and_distribution') }}</h4>
            </div>
            <div class="col-xs-4" style="text-align: center;">
                <h4 style="margin: 0px;">{{ __('order.connect_with_us') }}</h4>
                <p style="margin: 0px;">01154393483</p>
                <p style="margin: 0px;">01154393450</p>
                <p style="margin: 0px;">38480575</p>
            </div>
        </div>

        {{-- <div style="display: inline-block"><h4 style="display: inline-block">{{ __('order.name') }} :- </h4> <h4 style="display: inline-block">{{ $order->customer->name }}</h4></div> --}}

        <table class="table table-hover table-bordered" style="margin-bottom:3px">

            <thead>
            <tr>
                <th>#</th>
                <th>{{ __('order.name') }}</th>
                <th>{{ __('order.quantity') }}</th>
                @if ($order->customer->client_permissions == "pharmaceutical" || $order->customer->client_permissions == "special_customer")
                    <th>{{ __('order.purchasing_price') }}</th>
                    <th>{{ __('order.selling_price') }}</th>
                @else
                    <th>{{ __('order.price') }}</th>
                @endif
                <th>{{ __('order.total_product_price') }}</th>
            </tr>
            </thead>

            <tbody>

                @php
                    $total_price = 0 ;
                @endphp
            @foreach ($order->product_order as $index => $product_order)
                @if (isset($product_order->product[0]->name))
                    <tr>
                        <td>{{ ++$index }}</td>
                        <td>{{ $product_order->product[0]->name }}</td>
                        <td>{{ $product_order->quantity}}</td>

                        @if ($order->customer->client_permissions == "pharmaceutical")
                            <td>{{ number_format($product_order->product[0]->pharmacist_price , 2) }}</td>
                            <td>{{ number_format($product_order->product[0]->selling_price ,2 ) }}</td>

                            @php
                                $product_price = $product_order->product[0]->pharmacist_price * $product_order->quantity ;
                                $total_price += $product_price ;
                            @endphp

                        @elseif ($order->customer->client_permissions == "special_customer")
                            <td>{{ number_format($product_order->product[0]->ProductPriceAccordingToCustomerType , 2) }}</td>
                            <td>{{ number_format($product_order->product[0]->selling_price , 2 ) }}</td>

                            @php
                                $product_price = $product_order->product[0]->ProductPriceAccordingToCustomerType * $product_order->quantity ;
                                $total_price += $product_price ;
                            @endphp
                        @else
                            <td>{{ number_format($product_order->product[0]->selling_price , 2) }}</td>

                            @php
                                $product_price = $product_order->product[0]->selling_price * $product_order->quantity ;
                                $total_price += $product_price ;
                            @endphp
                        @endif

                        <td>{{ number_format($product_price ,2) }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
        <table class="table table-hover table-bordered" id="footer" style="display: none">
            <tbody >
                <tr>
                    <td>عدد الاصناف</td>
                    <td>{{ $order->product_order->count() }}</td>

                    <td>صافى الفاتوره</td>
                    <td>{{ number_format($order->total_price ,2) }}</td>

                    <td>الحساب السابق</td>
                    <td>{{ number_format($order->customer->previous_account - $order->total_price , 2) }}</td>

                    <td>الإجمالى</td>
                    <td>{{ number_format( $order->customer->previous_account  , 2) }}</td>
                </tr>
            </tbody>
        </table>

        <p id="Company_Address" style="display: none; text-align:left; color:#ada2a2"> {{ __('order.Company_Address') }}</p>

    </div>

    <button class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i>{{__('order.print')}}</button>

@endif
