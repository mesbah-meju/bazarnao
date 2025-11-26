<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>Product</th>
            <th>From Warehouse</th>
            <th>To Warehouse</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
            $total = 0;
            $totalAmount = 0;
            $totalQty = 0;
        @endphp

        @foreach ($transfers as $key => $transfer)
            @php
                $total += $transfer->unit_price;
                $totalAmount += $transfer->amount;
                $totalQty += $transfer->qty;
                $fromWarehouse = \App\Models\Warehouse::find($transfer->from_wearhouse_id);
                $toWarehouse = \App\Models\Warehouse::find($transfer->to_wearhouse_id);
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $transfer->product->name }}</td>
                <td>{{ $fromWarehouse ? $fromWarehouse->name : 'N/A' }}</td>
                <td>{{ $toWarehouse ? $toWarehouse->name : 'N/A' }}</td>
                <td>{{ $transfer->qty }}</td>
                <td>{{ $transfer->unit_price ?? '-' }}</td>
                <td>{{ $transfer->amount ?? '-' }}</td>
                <td>{{ $transfer->date }}</td>
                <td>{{ $transfer->status }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="text-align: right" colspan="4"><b>Total</b></td>
            <td><b>{{ $totalQty }}</b></td>
            <td><b>{{ $total }}</b></td>
            <td><b>{{ $totalAmount }}</b></td>
        </tr>
    </tbody>
</table>
