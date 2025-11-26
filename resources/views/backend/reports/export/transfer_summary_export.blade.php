<table>
    <thead>
        <tr>
            <th>SL</th>
            <th>From Warehouse</th>
            <th>To Warehouse</th>
            <th>Qty</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1;
        @endphp

        @foreach ($transfers as $transfer)
            @php
                $fromWarehouse = \App\Models\Warehouse::find($transfer->from_wearhouse_id);
                $toWarehouse = \App\Models\Warehouse::find($transfer->to_wearhouse_id);
            @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $fromWarehouse ? $fromWarehouse->name : 'N/A' }}</td>
                <td>{{ $toWarehouse ? $toWarehouse->name : 'N/A' }}</td>
                <td>{{ $transfer->total_qty }}</td>
                <td>{{ number_format($transfer->total_amount, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" style="text-align: right;"><b>Total</b></td>
            <td><b>{{ $totalQty }}</b></td>
            <td><b>{{ number_format($totalAmount, 2) }}</b></td>
        </tr>
    </tbody>
</table>
