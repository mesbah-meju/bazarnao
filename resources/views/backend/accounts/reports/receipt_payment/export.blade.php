<?php
    $gtotal = 0;
    $pgtotal = 0;
?>
<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center; font-weight: bold; font-size: 16px;">Receipt and Payment Report</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center; font-weight: bold; font-size: 14px;">Receipt and Payment From {{ date('d-m-Y', strtotime($dtpFromDate)) }} To {{ date('d-m-Y', strtotime($dtpToDate)) }}</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align: center;">Warehouse: {{ $warehouseName }}</th>
        </tr>
        <tr>
            <th colspan="2"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000;">Opening Balance</td>
        </tr>
        <?php if ($cashOpening != 0) { ?>
        <tr>
            <td style="border: 1px solid #000;">        Cash In Hand</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($cashOpening, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>
        <?php if ($bankOpening != 0) { ?>
        <tr>
            <td style="border: 1px solid #000;">        Cash At Bank</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($bankOpening, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>
        <?php if ($advOpening != 0) { ?>
        <tr>
            <td style="border: 1px solid #000;">        Advance</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($advOpening, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>

        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000;">Receipts</td>
        </tr>
        <?php foreach ($receiptitems as $receiptitem) { ?>
        <tr>
            <td style="border: 1px solid #000;">    {{ $receiptitem['headName'] ?? '' }}</td>
            <td style="border: 1px solid #000;"></td>
        </tr>
            <?php if (isset($receiptitem['innerHead']) && count($receiptitem['innerHead']) > 0) {
                foreach ($receiptitem['innerHead'] as $inner) {
                    if (($inner['credit'] ?? 0) != 0) {
            ?>
        <tr>
            <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($inner['credit'], 2, '.', ',') }}</td>
        </tr>
            <?php }
                }
            }
            $gtotal += $inner['credit'] ?? 0; ?>
        <?php } ?>
        <tr style="background-color: #E7E0EE;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency }} {{ number_format($gtotal, 2, '.', ',') }}</td>
        </tr>
        <tr style="background-color: #E7E0EE;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Grand Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency }} {{ number_format(($gtotal + $cashOpening + $bankOpening + $advOpening), 2, '.', ',') }}</td>
        </tr>

        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000;">Payments</td>
        </tr>
        <?php foreach ($paymentitems as $paymentitem) { ?>
        <tr>
            <td style="border: 1px solid #000;">    {{ $paymentitem['headName'] ?? '' }}</td>
            <td style="border: 1px solid #000;"></td>
        </tr>
            <?php if (isset($paymentitem['innerHead']) && count($paymentitem['innerHead']) > 0) {
                foreach ($paymentitem['innerHead'] as $inner) {
                    if (($inner['debit'] ?? 0) != 0) {
            ?>
        <tr>
            <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($inner['debit'], 2, '.', ',') }}</td>
        </tr>
            <?php }
                }
            }
            $pgtotal += $inner['debit'] ?? 0; ?>
        <?php } ?>
        <tr style="background-color: #E7E0EE;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency }} {{ number_format($pgtotal, 2, '.', ',') }}</td>
        </tr>

        <tr>
            <td colspan="2" style="font-weight: bold; border: 1px solid #000;">Closing Balance</td>
        </tr>
        <?php if ($cashClosing != 0) { ?>
        <tr>
            <td style="border: 1px solid #000;">        Cash In Hand</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($cashClosing, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>
        <?php if ($bankClosing != 0) { ?>
        <tr>
            <td style="border: 1px solid #000;">        Cash At Bank</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($bankClosing, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>
        <?php if ($advClosing != 0) { ?>
        <tr>
            <td style="border: 1px solid #000;">        Advance</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency }} {{ number_format($advClosing, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>
        <tr style="background-color: #E7E0EE;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Grand Total</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ $currency }} {{ number_format(($pgtotal + $advClosing + $bankClosing + $cashClosing), 2, '.', ',') }}</td>
        </tr>
    </tbody>
</table>

