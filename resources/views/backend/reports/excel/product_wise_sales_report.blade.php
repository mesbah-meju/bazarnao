
<table class="table-bordered" style="width: 100%">
    <thead>
        <tr>
            <th style="width: 5%">SL</th>
            <th style="width: 30%">{{ translate('Product Name') }}</th>
            <th style="width: 20%">{{ translate('Category') }}</th>
            <th style="width: 10%">{{ translate('Qty') }}</th>
            <th style="width: 10%">{{ translate('Unit Rate') }}</th>
            <th style="width: 10%">{{ translate('Total Amount') }}</th>
            <th style="width: 10%">{{ translate('Num of Sales') }}</th>
            <th style="width: 10%">{{ translate('Profit') }}</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; $qty = 1; $total_profit = 0; @endphp
        @foreach ($products as $key => $product)
        @php
        $total += $product->price;
        $profit = $product->profit_loss;
        $total_profit += $profit;
        $qty = !empty($product->quantity) ? $product->quantity : 1;
        @endphp
        <tr>
            <td>{{ ($key + 1) }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->category_name }}</td>
            <td style="text-align: right;">{{ $product->quantity }}</td>
            <td style="text-align: right;">{{ single_price($product->price / $qty) }}</td>
            <td style="text-align: right;">{{ single_price($product->price) }}</td>
            <td style="text-align: center;">

                {{ $product->num_of_sale }}
            </td>

            <td style="text-align: center;">{{ $profit }}</td>
        </tr>
        @endforeach
    </tbody>
</table>