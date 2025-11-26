<table>
    <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-weight: bold; font-size: 16px;">
                Sub Ledger Report
            </th>
        </tr>
        @if ($subLedger)
        <tr>
            <th colspan="9" style="text-align: center; font-weight: bold; font-size: 14px;">
                Sub Ledger of {{ $ledger->head_name }} ({{ $subLedger->name }}) on {{ date('d-m-Y', strtotime($dtpFromDate)) }} To {{ date('d-m-Y', strtotime($dtpToDate)) }}
            </th>
        </tr>
        @endif
        <tr>
            <th colspan="9" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">SL</th>
            <th style="font-weight: bold; border: 1px solid #000;">Date</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher Type</th>
            <th style="font-weight: bold; border: 1px solid #000;">Account Name</th>
            <th style="font-weight: bold; border: 1px solid #000;">Ledger Comment</th>
            <th style="font-weight: bold; border: 1px solid #000;">Debit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Credit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $TotalCredit = 0;
            $TotalDebit = 0;
            $CurBalance = $prebalance;
            $openid = 1;
        ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $openid }}</td>
            <td style="border: 1px solid #000;">{{ date('d-m-Y', strtotime($dtpFromDate)) }}</td>
            <td colspan="4" style="text-align: right; border: 1px solid #000; font-weight: bold;">Opening Balance</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($prebalance, 2, '.', ',') }}</td>
        </tr>
        @foreach($HeadName2 as $key => $data)
        @php
            // Voucher type translation
            $voucherType = 'N/A';
            if($data->voucher_type=='DV') {
                $voucherType = 'Debit';
            } elseif($data->voucher_type=='CV') {
                $voucherType = 'Credit';
            } elseif($data->voucher_type=='JV') {
                $voucherType = 'Journal';
            } else {
                $voucherType = 'Contra';
            }
        @endphp
        <tr>
            <td style="border: 1px solid #000;">{{ ++$key + $openid }}</td>
            <td style="border: 1px solid #000;">{{ date('d-m-Y', strtotime($data->voucher_date)) }}</td>
            <td style="border: 1px solid #000;">{{ $data->voucher_no }}</td>
            <td style="border: 1px solid #000;">{{ $voucherType }}</td>
            <td style="border: 1px solid #000;">{{ $data->coa->head_name }}</td>
            <td style="border: 1px solid #000;">{{ $data->ledger_comment }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($data->debit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($data->credit, 2, '.', ',') }}</td>
            @php 
                $TotalDebit += $data->debit;
                $TotalCredit += $data->credit;
                if($ledger->head_type == 'A' || $ledger->head_type == 'E') {
                    if($data->debit > 0) {
                        $CurBalance += $data->debit;
                    }
                    if($data->credit > 0) {
                        $CurBalance -= $data->credit;
                    }                          
                } else {                       
                    if($data->debit > 0) {
                        $CurBalance -= $data->debit;
                    }                          
                    if($data->credit > 0) {
                        $CurBalance += $data->credit;
                    }
                }
            @endphp
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($CurBalance, 2, '.', ',') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: right; border: 1px solid #000; font-weight: bold;">Total</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($TotalDebit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($TotalCredit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($CurBalance, 2, '.', ',') }}</td>
        </tr>
    </tfoot>
</table>

