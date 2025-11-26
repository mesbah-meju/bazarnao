<table class="table aiz-table mb-0" style="width:100%;">
    <thead>
        <tr>
            <th style="width: 5%">SL</th>
            <th style="width: 30%">{{ translate('Product Name') }}</th>
            <th style="width: 10%">{{ translate('Category') }}</th>
            <th style="width: 10%">{{ translate('Selling Qty') }}</th>
            {{-- <th style="width: 10%">{{ translate('Selling Unit Price') }}</th> --}}
            <th style="width: 10%">{{ translate('Selling Amount') }}</th>
            <th style="width: 10%">{{ translate('Purchase Unit Price') }}</th>
            <th style="width: 10%">{{ translate('Purchase Price') }}</th>
            <th style="width: 15%">{{ translate('Profit') }}</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $total = 0;
            $total_qty = 0;
            $total_profit = 0;
            $total_purchase = 0;
        @endphp
        @foreach ($products as $key => $product)
        @php 
            $total += $product->total_order_price;
            $total_qty += $product->total_order_qty;
            $total_purchase += $product->purchase_price * $product->total_order_qty;
            // $total_profit += $product->profit;
            $profit = $product->profit_loss;
            $total_profit += $profit;
        @endphp                 
        <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->category_name }}</td>
            <td style="text-align: center;">{{ $product->total_order_qty }}</td>
            {{-- <td style="text-align: right;">{{ single_price($product->sale_price) }}</td> --}}
            <td style="text-align: right;">{{ single_price($product->total_order_price) }}</td>
            <td style="text-align: right;">{{ single_price($product->purchase_price) }}</td>
            <td style="text-align: right;">{{ single_price($product->purchase_price * $product->total_order_qty) }}</td>
            <td style="text-align: center;">{{ single_price($product->profit_loss) }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="text-align: right;" colspan="5"><b>Total</b></td>
            <td style="text-align: right;"><b>{{ single_price($total) }}</b></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;"><b>{{ single_price($total_purchase) }}</b></td>
            <td style="text-align: center;"><b>{{ single_price($total_profit) }}</b></td>
        </tr>
    </tbody>
</table>