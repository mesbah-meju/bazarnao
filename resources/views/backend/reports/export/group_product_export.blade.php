<table class="">
    <thead>
        <tr>
            <th>{{ translate('SL') }}</th>
            <th>{{ translate('Group Product Name') }}</th>
            <th>{{ translate('Product Name') }}</th>
            <th>{{ translate('Product QTY') }}</th>
            <th>{{ translate('Product Price') }}</th>
            <th>{{ translate('Discount') }}</th>
            <th>{{ translate('Group Price') }}</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $i = 1;
        @endphp
        @foreach ($groupProducts as $groupProduct)
            @php 
                $details = $productDetails->get($groupProduct->id);
                $total_web_price = 0;
                $total_app_price = 0;
                $total_web_discount_amount = 0;
                $total_app_discount_amount = 0;
            @endphp
            @if ($details)
                @foreach ($details as $detail)
                    @php 
                    $total_web_price += $detail->price;
                    $total_app_price += $detail->app_price;
                    $total_web_discount_amount += $detail->discount_amount;
                    $total_app_discount_amount += $detail->app_discount_amount;
                    @endphp
                @endforeach
                <tr class="group-row">
                    <td>{{ $i++ }}</td>
                    <td><strong>{{ $groupProduct->name }}</strong></td>
                    <td colspan="1"></td>
                    <td colspan="1"></td>
                    <td colspan="1"></td>
                    <td><b>Web: </b>{{ single_price($total_web_discount_amount ?? 0) }}<br><b>App: </b>{{ single_price($total_app_discount_amount ?? 0) }}</td>
                    <td><b>Web: </b>{{ single_price($total_web_price ?? 0) }}<br><b>App: </b>{{ single_price($total_app_price ?? 0) }}</td>
                </tr>
                @foreach ($details as $detail)
                    <tr class="product-row">
                        <td></td>
                        <td></td>
                        <td>{{ $detail->product_name }}</td>
                        <td><b>Web: </b>{{ $detail->qty }} <br><b>App: </b>{{ $detail->app_qty ?? 0}}</td>
                        <td><b>Web: </b>{{ single_price($detail->price) }} <br><b>App: </b>{{ single_price($detail->app_price ?? 0) }}</td>
                        <td><b>Web: </b>{{ single_price($detail->discount_amount ?? 0) }} <br><b>App: </b>{{ single_price($detail->app_discount_amount ?? 0) }}</td>
                        <td></td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>