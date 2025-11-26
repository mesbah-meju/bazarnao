<?php
    $startmonth = date('n', strtotime($curentYear->start_date));
?>
<table>
    <thead>
        <tr>
            <th colspan="13" style="text-align: center; font-weight: bold; font-size: 16px;">Income Statement Report</th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center; font-weight: bold; font-size: 14px;">Income Statement for {{ $curentYear->year_name }}</th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center;">Warehouse: {{ $warehouseName }}</th>
        </tr>
        <tr>
            <th colspan="13"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <?php if ($startmonth == 1) { ?>
                <th style="font-weight: bold; border: 1px solid #000;">January</th>
                <th style="font-weight: bold; border: 1px solid #000;">February</th>
                <th style="font-weight: bold; border: 1px solid #000;">March</th>
                <th style="font-weight: bold; border: 1px solid #000;">April</th>
                <th style="font-weight: bold; border: 1px solid #000;">May</th>
                <th style="font-weight: bold; border: 1px solid #000;">June</th>
                <th style="font-weight: bold; border: 1px solid #000;">July</th>
                <th style="font-weight: bold; border: 1px solid #000;">August</th>
                <th style="font-weight: bold; border: 1px solid #000;">September</th>
                <th style="font-weight: bold; border: 1px solid #000;">October</th>
                <th style="font-weight: bold; border: 1px solid #000;">November</th>
                <th style="font-weight: bold; border: 1px solid #000;">December</th>
            <?php } else { ?>
                <th style="font-weight: bold; border: 1px solid #000;">July</th>
                <th style="font-weight: bold; border: 1px solid #000;">August</th>
                <th style="font-weight: bold; border: 1px solid #000;">September</th>
                <th style="font-weight: bold; border: 1px solid #000;">October</th>
                <th style="font-weight: bold; border: 1px solid #000;">November</th>
                <th style="font-weight: bold; border: 1px solid #000;">December</th>
                <th style="font-weight: bold; border: 1px solid #000;">January</th>
                <th style="font-weight: bold; border: 1px solid #000;">February</th>
                <th style="font-weight: bold; border: 1px solid #000;">March</th>
                <th style="font-weight: bold; border: 1px solid #000;">April</th>
                <th style="font-weight: bold; border: 1px solid #000;">May</th>
                <th style="font-weight: bold; border: 1px solid #000;">June</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <!-- INCOME SECTION -->
        <?php if (count($incomes) > 0) {
            foreach ($incomes as $income) { ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $income['head'] ?? '' }}</td>
            <td colspan="12" style="border: 1px solid #000;"></td>
        </tr>
            <?php if (isset($income['nextlevel']) && count($income['nextlevel']) > 0) {
                foreach ($income['nextlevel'] as $value) { ?>
        <tr>
            <td style="border: 1px solid #000;">    {{ $value['headName'] ?? '' }}</td>
            <td colspan="12" style="border: 1px solid #000;"></td>
        </tr>
                    <?php if (isset($value['innerHead']) && count($value['innerHead']) > 0) {
                        foreach ($value['innerHead'] as $inner) {
                            if ($startmonth == 1) { ?>
        <tr>
            <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount6'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount12'] ?? 0, 2, '.', ',') }}</td>
        </tr>
                            <?php } else { ?>
        <tr>
            <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount12'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount6'] ?? 0, 2, '.', ',') }}</td>
        </tr>
                            <?php }
                        }
                    }
                }
            }
        }
        
        if ($startmonth == 1) { ?>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Income</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal6'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal12'] ?? 0, 2, '.', ',') }}</td>
        </tr>
        <?php } else { ?>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Income</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal12'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($incomes[0]['gtotal6'] ?? 0, 2, '.', ',') }}</td>
        </tr>
        <?php } ?>

        <tr><td colspan="13"></td></tr>

        <!-- COST OF GOODS SOLD SECTION -->
        <?php if (count($costofgoodsolds) > 0) {
            foreach ($costofgoodsolds as $cogs) { ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $cogs['headName'] ?? '' }}</td>
            <td colspan="12" style="border: 1px solid #000;"></td>
        </tr>
                <?php if (isset($cogs['innerHead']) && count($cogs['innerHead']) > 0) {
                    foreach ($cogs['innerHead'] as $inner) {
                        if ($startmonth == 1) { ?>
        <tr>
            <td style="border: 1px solid #000;">    {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount6'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount12'] ?? 0, 2, '.', ',') }}</td>
        </tr>
                        <?php } else { ?>
        <tr>
            <td style="border: 1px solid #000;">    {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount12'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount6'] ?? 0, 2, '.', ',') }}</td>
        </tr>
                        <?php }
                    }
                }
            }
        }
        
        if ($startmonth == 1) { ?>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Cost of Goods Sold</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota6'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota12'] ?? 0, 2, '.', ',') }}</td>
        </tr>
        <tr style="background-color: #e8e8e8;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Gross Profit</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal1'] ?? 0) - ($costofgoodsolds[0]['subtota1'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal2'] ?? 0) - ($costofgoodsolds[0]['subtota2'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal3'] ?? 0) - ($costofgoodsolds[0]['subtota3'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal4'] ?? 0) - ($costofgoodsolds[0]['subtota4'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal5'] ?? 0) - ($costofgoodsolds[0]['subtota5'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal6'] ?? 0) - ($costofgoodsolds[0]['subtota6'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal7'] ?? 0) - ($costofgoodsolds[0]['subtota7'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal8'] ?? 0) - ($costofgoodsolds[0]['subtota8'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal9'] ?? 0) - ($costofgoodsolds[0]['subtota9'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal10'] ?? 0) - ($costofgoodsolds[0]['subtota10'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal11'] ?? 0) - ($costofgoodsolds[0]['subtota11'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal12'] ?? 0) - ($costofgoodsolds[0]['subtota12'] ?? 0), 2, '.', ',') }}</td>
        </tr>
        <?php } else { ?>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Cost of Goods Sold</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota12'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($costofgoodsolds[0]['subtota6'] ?? 0, 2, '.', ',') }}</td>
        </tr>
        <tr style="background-color: #e8e8e8;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Gross Profit</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal7'] ?? 0) - ($costofgoodsolds[0]['subtota7'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal8'] ?? 0) - ($costofgoodsolds[0]['subtota8'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal9'] ?? 0) - ($costofgoodsolds[0]['subtota9'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal10'] ?? 0) - ($costofgoodsolds[0]['subtota10'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal11'] ?? 0) - ($costofgoodsolds[0]['subtota11'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal12'] ?? 0) - ($costofgoodsolds[0]['subtota12'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal1'] ?? 0) - ($costofgoodsolds[0]['subtota1'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal2'] ?? 0) - ($costofgoodsolds[0]['subtota2'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal3'] ?? 0) - ($costofgoodsolds[0]['subtota3'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal4'] ?? 0) - ($costofgoodsolds[0]['subtota4'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal5'] ?? 0) - ($costofgoodsolds[0]['subtota5'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format(($incomes[0]['gtotal6'] ?? 0) - ($costofgoodsolds[0]['subtota6'] ?? 0), 2, '.', ',') }}</td>
        </tr>
        <?php } } ?>

        <tr><td colspan="13"></td></tr>

        <!-- EXPENSES SECTION -->
        <?php if (count($expenses) > 0) {
            foreach ($expenses as $expense) { ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $expense['head'] ?? '' }}</td>
            <td colspan="12" style="border: 1px solid #000;"></td>
        </tr>
                <?php if (isset($expense['nextlevel']) && count($expense['nextlevel']) > 0) {
                    foreach ($expense['nextlevel'] as $value) { ?>
        <tr>
            <td style="border: 1px solid #000;">    {{ $value['headName'] ?? '' }}</td>
            <td colspan="12" style="border: 1px solid #000;"></td>
        </tr>
                        <?php if (isset($value['innerHead']) && count($value['innerHead']) > 0) {
                            foreach ($value['innerHead'] as $inner) {
                                if ($startmonth == 1) { ?>
        <tr>
            <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount6'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount12'] ?? 0, 2, '.', ',') }}</td>
        </tr>
                                <?php } else { ?>
        <tr>
            <td style="border: 1px solid #000;">        {{ $inner['headName'] ?? '' }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount12'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($inner['amount6'] ?? 0, 2, '.', ',') }}</td>
        </tr>
                                <?php }
                            }
                        }
                    }
                }
            }
        
        if ($startmonth == 1) { ?>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Operating Expenses</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal6'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal12'] ?? 0, 2, '.', ',') }}</td>
        </tr>
        <tr style="background-color: #d0d0d0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">NET PROFIT</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal1'] ?? 0) - ($costofgoodsolds[0]['subtota1'] ?? 0)) - ($expenses[0]['gtotal1'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal2'] ?? 0) - ($costofgoodsolds[0]['subtota2'] ?? 0)) - ($expenses[0]['gtotal2'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal3'] ?? 0) - ($costofgoodsolds[0]['subtota3'] ?? 0)) - ($expenses[0]['gtotal3'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal4'] ?? 0) - ($costofgoodsolds[0]['subtota4'] ?? 0)) - ($expenses[0]['gtotal4'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal5'] ?? 0) - ($costofgoodsolds[0]['subtota5'] ?? 0)) - ($expenses[0]['gtotal5'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal6'] ?? 0) - ($costofgoodsolds[0]['subtota6'] ?? 0)) - ($expenses[0]['gtotal6'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal7'] ?? 0) - ($costofgoodsolds[0]['subtota7'] ?? 0)) - ($expenses[0]['gtotal7'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal8'] ?? 0) - ($costofgoodsolds[0]['subtota8'] ?? 0)) - ($expenses[0]['gtotal8'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal9'] ?? 0) - ($costofgoodsolds[0]['subtota9'] ?? 0)) - ($expenses[0]['gtotal9'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal10'] ?? 0) - ($costofgoodsolds[0]['subtota10'] ?? 0)) - ($expenses[0]['gtotal10'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal11'] ?? 0) - ($costofgoodsolds[0]['subtota11'] ?? 0)) - ($expenses[0]['gtotal11'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal12'] ?? 0) - ($costofgoodsolds[0]['subtota12'] ?? 0)) - ($expenses[0]['gtotal12'] ?? 0), 2, '.', ',') }}</td>
        </tr>
        <?php } else { ?>
        <tr style="background-color: #f0f0f0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">Total Operating Expenses</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal7'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal8'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal9'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal10'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal11'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal12'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal1'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal2'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal3'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal4'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal5'] ?? 0, 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format($expenses[0]['gtotal6'] ?? 0, 2, '.', ',') }}</td>
        </tr>
        <tr style="background-color: #d0d0d0;">
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">NET PROFIT</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal7'] ?? 0) - ($costofgoodsolds[0]['subtota7'] ?? 0)) - ($expenses[0]['gtotal7'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal8'] ?? 0) - ($costofgoodsolds[0]['subtota8'] ?? 0)) - ($expenses[0]['gtotal8'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal9'] ?? 0) - ($costofgoodsolds[0]['subtota9'] ?? 0)) - ($expenses[0]['gtotal9'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal10'] ?? 0) - ($costofgoodsolds[0]['subtota10'] ?? 0)) - ($expenses[0]['gtotal10'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal11'] ?? 0) - ($costofgoodsolds[0]['subtota11'] ?? 0)) - ($expenses[0]['gtotal11'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal12'] ?? 0) - ($costofgoodsolds[0]['subtota12'] ?? 0)) - ($expenses[0]['gtotal12'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal1'] ?? 0) - ($costofgoodsolds[0]['subtota1'] ?? 0)) - ($expenses[0]['gtotal1'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal2'] ?? 0) - ($costofgoodsolds[0]['subtota2'] ?? 0)) - ($expenses[0]['gtotal2'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal3'] ?? 0) - ($costofgoodsolds[0]['subtota3'] ?? 0)) - ($expenses[0]['gtotal3'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal4'] ?? 0) - ($costofgoodsolds[0]['subtota4'] ?? 0)) - ($expenses[0]['gtotal4'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal5'] ?? 0) - ($costofgoodsolds[0]['subtota5'] ?? 0)) - ($expenses[0]['gtotal5'] ?? 0), 2, '.', ',') }}</td>
            <td style="text-align: right; font-weight: bold; border: 1px solid #000;">{{ number_format((($incomes[0]['gtotal6'] ?? 0) - ($costofgoodsolds[0]['subtota6'] ?? 0)) - ($expenses[0]['gtotal6'] ?? 0), 2, '.', ',') }}</td>
        </tr>
        <?php } } ?>
    </tbody>
</table>

