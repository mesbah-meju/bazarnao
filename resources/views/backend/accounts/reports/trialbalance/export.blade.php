<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 16px;">
                Trial Balance Report
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 14px;">
                Trial Balance on {{ date('d-m-Y', strtotime($dtpFromDate)) }} To {{ date('d-m-Y', strtotime($dtpToDate)) }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">SL</th>
            <th style="font-weight: bold; border: 1px solid #000;">Code</th>
            <th style="font-weight: bold; border: 1px solid #000;">Account Name</th>
            <th style="font-weight: bold; border: 1px solid #000;">Debit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Credit</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            if (count($results) > 0) {
                $i = 1;
                $totalOpenDebit = 0;
                $totalOpenCredit = 0;
                $totalCurentDebit = 0;
                $totalCurentCredit = 0;
                $totalCloseDebit = 0;
                $totalCloseCredit = 0;
                
                foreach ($results as $key => $result) {
                    $totalbalanceDebit = 0;
                    $totalbalanceCredit = 0;

                    $copenDebit = 0;
                    $copenCredit = 0;
                    
                    $resultDebit = isset($result[0]) && isset($result[0]->debit) ? (float)$result[0]->debit : 0.0;
                    $resultCredit = isset($result[0]) && isset($result[0]->credit) ? (float)$result[0]->credit : 0.0;

                    $openingForHead = isset($openings[$result['head_code']]) ? (float)$openings[$result['head_code']] : 0.0;

                    if($result['head_type'] == 'A' || $result['head_type'] == 'E') { 
                        if($openingForHead != 0) {
                            $totalOpenDebit += $openingForHead;
                            $copenDebit     += $openingForHead;                                       
                        } 
                        $totalbalanceDebit   +=  $copenDebit + ($resultDebit - $resultCredit);
                    } else { 
                        if($openingForHead != 0) {
                            $totalOpenCredit += $openingForHead;
                            $copenCredit     += $openingForHead;
                        } 
                        $totalbalanceCredit  +=  $copenCredit + ($resultCredit - $resultDebit);  
                    }
                                                    
                    $totalCurentDebit   += $resultDebit; 
                    $totalCurentCredit  += $resultCredit;  

                    // Show only if there is a non-zero balance
                    if ($totalbalanceDebit != 0 || $totalbalanceCredit != 0) {
                        $totalCloseDebit   += $totalbalanceDebit;
                        $totalCloseCredit  += $totalbalanceCredit; 
        ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $i++ }}</td>
            <td style="border: 1px solid #000;">{{ $result['head_code'] }}</td>
            <td style="border: 1px solid #000;">{{ $result['head_name'] }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($totalbalanceDebit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ $currency . ' ' . number_format($totalbalanceCredit, 2, '.', ',') }}</td>
        </tr>
        <?php 
                    }
                }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="text-align: center; border: 1px solid #000; font-weight: bold;">Total</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ $currency . ' ' . number_format($totalCloseDebit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ $currency . ' ' . number_format($totalCloseCredit, 2, '.', ',') }}</td>
        </tr>
    </tfoot>
        <?php } ?>
</table>

