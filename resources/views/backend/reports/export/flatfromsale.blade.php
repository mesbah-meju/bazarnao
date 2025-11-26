<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>Date</th>
            <th>Order Code</th>
            <th>Warehouse</th>
            <th>Order From</th>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Customer Type</th>
            <th>Phone</th>
            <th>Area</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i = 0;
        $total = 0;
        @endphp

        @foreach ($orders as $order)
        @php
        $i++;
        $total += $order->grand_total;
        $warehouse = \App\Models\Warehouse::find($order->warehouse);
        @endphp
        <tr>
            <td>{{ $i }}</td>
            <td>{{ date('Y-m-d', $order->date) }}</td>
            <td>{{ $order->code }}</td>
            <td>{{ $warehouse ? $warehouse->name : 'N/A' }}</td>
            <td>{{ $order->order_from ?: 'Undefined' }}</td>
            <td>{{ $order->user_id ?: 'Guest' }}</td>
            <td>{{ $order->user ? $order->user->name : 'Guest' }}</td>
            <td>{{ $order->user->customer->customer_type ?: 'Guest' }}</td>
            <td>{{ $order->user ? $order->user->phone : 'Guest' }}</td>
            <td>{{ $order->user ? get_customer_area_name($order->user_id) : (json_decode($order->shipping_address)->area ?? 'N/A') }}</td>
            <td>{{ single_price($order->grand_total) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="11"><b>Total</b></td>
            <td><b>{{ single_price($total) }}</b></td>
        </tr>
    </tbody>
</table>
