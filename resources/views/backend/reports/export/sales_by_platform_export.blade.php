<table class="table table-bordered">
    <thead>
        <tr>
            <th>Platform</th>
            <th>Order Qty</th>
            <th>Customer Qty</th>
            <th>Sales Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($platformData as $platform => $data)
            <tr>
                <td>{{ $platform }}</td>
                <td>{{ $data['total_orders'] }}</td>
                <td>{{ $data['total_customers'] }}</td>
                <td>{{ single_price($data['total_order_price']) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="text-align:right;" colspan="3"><b>Total:</b></td>
            <td style="text-align:right;"><b>
                @php
                    $grandTotal = 0;
                    foreach ($platformData as $data) {
                        $grandTotal += $data['total_order_price'];
                    }
                @endphp
                {{ single_price($grandTotal) }}
            </b></td>
        </tr>
    </tbody>
</table>