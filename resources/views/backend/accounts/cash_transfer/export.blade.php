<table>
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold; font-size: 16px;">Cash Transfer Report</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 12px;">
                @if($from_warehouse_name)
                    From Warehouse: {{ $from_warehouse_name }}
                @endif
                @if($to_warehouse_name)
                    @if($from_warehouse_name) | @endif
                    To Warehouse: {{ $to_warehouse_name }}
                @endif
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 12px;">
                @if($from_date || $to_date)
                    Date Range: 
                    @if($from_date) {{ date('d-m-Y', strtotime($from_date)) }} @else Start @endif
                    to 
                    @if($to_date) {{ date('d-m-Y', strtotime($to_date)) }} @else End @endif
                @endif
            </th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">#</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher Date</th>
            <th style="font-weight: bold; border: 1px solid #000;">From Warehouse</th>
            <th style="font-weight: bold; border: 1px solid #000;">To Warehouse</th>
            <th style="font-weight: bold; border: 1px solid #000;">Remark</th>
            <th style="font-weight: bold; border: 1px solid #000;">Amount</th>
            <th style="font-weight: bold; border: 1px solid #000;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $totalAmount = 0; @endphp
        @foreach ($transfers as $key => $transfer)
            <tr>
                <td style="border: 1px solid #000;">{{ $key + 1 }}</td>
                <td style="border: 1px solid #000;">{{ $transfer->voucher_no }}</td>
                <td style="border: 1px solid #000;">{{ date('d-m-Y', strtotime($transfer->voucher_date)) }}</td>
                <td style="border: 1px solid #000;">{{ getWearhouseName($transfer->from_warehouse_id) }}</td>
                <td style="border: 1px solid #000;">{{ getWearhouseName($transfer->to_warehouse_id) }}</td>
                <td style="border: 1px solid #000;">{{ $transfer->remarks }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($transfer->amount, 2, '.', ',') }}</td>
                <td style="border: 1px solid #000;">{{ $transfer->status == '0' ? 'Pending' : 'Approved' }}</td>
            </tr>
            @php $totalAmount += $transfer->amount; @endphp
        @endforeach
        <tr style="background-color: #f0f0f0;">
            <td colspan="6" style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Amount:</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($totalAmount, 2, '.', ',') }}</td>
            <td style="border: 1px solid #000;"></td>
        </tr>
    </tbody>
</table>

