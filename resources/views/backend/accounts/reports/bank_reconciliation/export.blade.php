<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 16px;">Bank Reconciliation Report</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 14px;">Bank Reconciliation Report From {{ date('d-m-Y', strtotime($dtpFromDate)) }} To {{ date('d-m-Y', strtotime($dtpToDate)) }}</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center;">Warehouse: {{ $warehouseName }}</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center;">Bank: {{ $bankName }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="6" style="background-color:#151B8D; color: #fff; font-weight: bold; border: 1px solid #000;">Approved</th>
            <th colspan="5" style="background-color:#5d61af; color: #fff; font-weight: bold; border: 1px solid #000;">Unapproved</th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">SL No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Check No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Date</th>
            <th style="font-weight: bold; border: 1px solid #000;">Amount</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Check No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Date</th>
            <th style="font-weight: bold; border: 1px solid #000;">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($vouchers) {
            $sl = 1;
            $hsum = 0;
            $nsum = 0;
            foreach ($vouchers as $appr) { ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $sl }}</td>
            <?php if ($appr->is_honour == 1) {
                $hsum += $appr->debit; ?>
                <td style="border: 1px solid #000;">{{ $appr->voucher_no }}</td>
                <td style="border: 1px solid #000;">{{ $appr->account_name }}</td>
                <td style="border: 1px solid #000;">{{ $appr->cheque_no }}</td>
                <td style="border: 1px solid #000;">{{ $appr->cheque_date }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($appr->debit, 2, '.', ',') }}</td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
            <?php } else {
                $nsum += $appr->debit; ?>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000;">{{ $appr->voucher_no }}</td>
                <td style="border: 1px solid #000;">{{ $appr->account_name }}</td>
                <td style="border: 1px solid #000;">{{ $appr->cheque_no }}</td>
                <td style="border: 1px solid #000;">{{ $appr->cheque_date }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($appr->debit, 2, '.', ',') }}</td>
            <?php } ?>
        </tr>
        <?php $sl++; } ?>
        <tr style="background-color: #f0f0f0;">
            <td style="border: 1px solid #000;"></td>
            <td colspan="4" style="text-align: right; font-weight: bold; border: 1px solid #000;">Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency }} {{ number_format($hsum, 2, '.', ',') }}</td>
            <td colspan="4" style="text-align: right; font-weight: bold; border: 1px solid #000;">Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency }} {{ number_format($nsum, 2, '.', ',') }}</td>
        </tr>
        <?php } else { ?>
        <tr>
            <td colspan="11" style="text-align: center; border: 1px solid #000;">No Result Found</td>
        </tr>
        <?php } ?>
    </tbody>
</table>

