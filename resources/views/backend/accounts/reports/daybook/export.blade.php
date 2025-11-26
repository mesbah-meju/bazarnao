<table>
    <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-weight: bold; font-size: 16px;">
                Day Book Voucher
            </th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center;">
                From: {{ date('d-m-Y', strtotime($dtpFromDate)) }} To: {{ date('d-m-Y', strtotime($dtpToDate)) }}
            </th>
        </tr>
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
            <th style="font-weight: bold; border: 1px solid #000;">Account Head</th>
            <th style="font-weight: bold; border: 1px solid #000;">Party Name</th>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher</th>
            <th style="font-weight: bold; border: 1px solid #000;">Debit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Credit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Rev. Head</th>
        </tr>
    </thead>
    <tbody>
        @php
            $TotalCredit = 0;
            $TotalDebit = 0;
        @endphp
        @if($voucherInfo->isNotEmpty())
            @foreach($voucherInfo as $key => $row)
            <tr>
                <td style="border: 1px solid #000;">{{ $key+1 }}</td>
                <td style="border: 1px solid #000;">{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                <td style="border: 1px solid #000;">
                    {{ optional($row->coa)->head_name }}
                    @if($row->subcode != null)
                    ({{ optional($row->subcode)->name }})
                    @endif
                </td>
                <td style="border: 1px solid #000;">
                    @if($row->relvalue && $row->reltype)
                        {{ $row->relvalue->name }}({{ $row->reltype->name }})
                    @else
                        N/A
                    @endif
                </td>
                <td style="border: 1px solid #000;">{{ $row->ledger_comment }}</td>
                <td style="border: 1px solid #000;">{{ $row->voucher_no }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($row->debit, 2, '.', ',') }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ number_format($row->credit, 2, '.', ',') }}</td>
                <td style="border: 1px solid #000;">{{ optional($row->rev_coa)->head_name }}</td>
            </tr>
            @php
                $TotalDebit += $row->debit;
                $TotalCredit += $row->credit;
            @endphp
            @endforeach
        @else
            <tr>
                <td colspan="9" style="text-align: center; border: 1px solid #000;">No vouchers found</td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: right; border: 1px solid #000; font-weight: bold;">Total</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($TotalDebit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($TotalCredit, 2, '.', ',') }}</td>
            <td style="border: 1px solid #000;"></td>
        </tr>
    </tfoot>
</table>

