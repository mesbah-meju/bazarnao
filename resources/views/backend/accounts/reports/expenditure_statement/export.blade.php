<table>
    <thead>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold; font-size: 16px;">
                Expenditure Statement Report
            </th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold; font-size: 14px;">
                Expenditure Statement from {{ date('d-m-Y', strtotime($dtpFromDate)) }} to {{ date('d-m-Y', strtotime($dtpToDate)) }}
            </th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Amount</th>
            <th style="font-weight: bold; border: 1px solid #000;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expenses as $expense)
            <tr>
                <td style="border: 1px solid #000;">{{ $expense['head'] }}</td>
                <td colspan="2" style="border: 1px solid #000;"></td>
            </tr>
            @if (isset($expense['nextlevel']) && count($expense['nextlevel']) > 0)
                @foreach ($expense['nextlevel'] as $value)
                    @if (($value['subtotal'] ?? 0) != 0)
                        <tr>
                            <td style="padding-left: 40px; border: 1px solid #000;">{{ $value['headName'] }}</td>
                            <td style="border: 1px solid #000;"></td>
                            <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($value['subtotal'], 2, '.', ',') }}</td>
                        </tr>
                    @endif
                    @if (isset($value['innerHead']) && count($value['innerHead']) > 0)
                        @foreach ($value['innerHead'] as $inner)
                            @if (($inner['amount'] ?? 0) != 0)
                                <tr>
                                    <td style="padding-left: 80px; border: 1px solid #000;">{{ $inner['headName'] }}</td>
                                    <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($inner['amount'], 2, '.', ',') }}</td>
                                    <td style="border: 1px solid #000;"></td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        <tr>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total</td>
            <td colspan="2" style="text-align: right; font-weight: bold; border: 1px solid #000;">
                {{ $currency . ' ' . number_format($expenses[0]['gtotal'] ?? 0, 2, '.', ',') }}
            </td>
        </tr>
    </tbody>
</table>

