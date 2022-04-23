@if ( isset($order) && $order->count() > 0)

    <div id="print-area" class="table-responsive">

        <h3 style="text-align: center;" class="company_name">{{ __('order.company_name') }}</h3>
        <div style="display: inline-block"><h4 style="display: inline-block">{{ __('order.name') }} :- </h4> <h4 style="display: inline-block">{{ $order->customer->name }}</h4></div>

        <table class="table table-hover table-bordered">

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
        <h3>{{ __('order.total') }} <span>{{ number_format($total_price ,2) }}</span></h3>

    </div>

    <button class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i>{{__('order.print')}}</button>

@endif
