<table>
    <thead>
        <tr>
            <th colspan="10" style="text-align: center; font-weight: bold; font-size: 16px;">
                Cash Book Report
            </th>
        </tr>
        <tr>
            <th colspan="10" style="text-align: center;">
                From: {{ date('d-m-Y', strtotime($dtpFromDate)) }} To: {{ date('d-m-Y', strtotime($dtpToDate)) }}
            </th>
        </tr>
        <tr>
            <th colspan="10" style="text-align: center;">
                Warehouse: {{ $warehouseName }}
            </th>
        </tr>
        <tr>
            <th colspan="10"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">SL</th>
            <th style="font-weight: bold; border: 1px solid #000;">Date</th>
            <th style="font-weight: bold; border: 1px solid #000;">Account Head</th>
            <th style="font-weight: bold; border: 1px solid #000;">Party Name</th>
            <th style="font-weight: bold; border: 1px solid #000;">Particulars</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher Name</th>
            <th style="font-weight: bold; border: 1px solid #000;">Voucher No</th>
            <th style="font-weight: bold; border: 1px solid #000;">Debit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Credit</th>
            <th style="font-weight: bold; border: 1px solid #000;">Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $TotalCredit = 0;
            $TotalDebit  = 0;
            $CurBalance = $prebalance;
            $openid = 1; 
        ?>
        <tr>
            <td style="border: 1px solid #000;">{{ $openid }}</td>
            <td style="border: 1px solid #000;">{{ date('d-m-Y', strtotime($dtpFromDate)) }}</td>
            <td colspan="5" style="text-align: right; border: 1px solid #000; font-weight: bold;">Opening Balance</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format(0, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($prebalance, 2, '.', ',') }}</td>
        </tr>
        @foreach($HeadName2 as $key => $data)
        @php
            // Fetch purchase details if this is a supplier payable transaction
            $purchase = null;
            $supplierName = 'N/A';
            $purchaseNo = '';
            
            // Initialize order variables
            $order = null;
            $customerName = 'N/A';
            $orderNo = '';
            
            if(($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204) && $data->reference_no) {
                $purchase = \App\Models\Purchase::find($data->reference_no);
                if($purchase) {
                    $supplier_id = $purchase->supplier_id;
                    $supplier = \App\Models\Supplier::find($supplier_id);
                    $supplierName = $supplier ? $supplier->name : 'N/A';
                    $purchaseNo = $purchase->purchase_no ?? '';
                }
            }
            
            if(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $data->reference_no) {
                $order = \App\Models\Order::find($data->reference_no);
                if($order) {
                    $customer_id = $order->user_id;
                    $customer = \App\Models\User::find($customer_id);
                    $customerName = $customer ? $customer->name : 'N/A';
                    $orderNo = $order->code ?? '';
                }
            }
        @endphp
        <tr>
            <td style="border: 1px solid #000;">{{ ++$key + $openid }}</td>
            <td style="border: 1px solid #000;">{{ date('d-m-Y', strtotime($data->voucher_date)) }}</td>
            <td style="border: 1px solid #000;">{{ $data->rev_coa->head_name }}</td>
            <td style="border: 1px solid #000;">
                @if($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204)
                    {{ $supplierName }}
                @elseif(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $orderNo)
                    {{ $customerName }}
                @elseif($data->relvalue && $data->reltype)
                    {{ $data->relvalue->name }}({{ $data->reltype->name }})
                @else
                    N/A
                @endif
            </td>
            <td style="border: 1px solid #000;">
                @if(($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204) && $purchaseNo)
                    {{ $data->ledger_comment }} for Purchase No: {{ $purchaseNo }}
                @elseif(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $orderNo)
                    {{ $data->ledger_comment }} for Order No: {{ $orderNo }}
                @else
                    {{ $data->ledger_comment }}
                @endif
            </td>
            <td style="border: 1px solid #000;">{{ $data->voucher_type }}</td>
            <td style="border: 1px solid #000;">{{ $data->voucher_no }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($data->debit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($data->credit, 2, '.', ',') }}</td>
            @php 
                $TotalDebit += $data->debit;
                $TotalCredit += $data->credit;
                $CurBalance += $data->debit - $data->credit;
            @endphp
            <td style="text-align: right; border: 1px solid #000;">{{ number_format($CurBalance, 2, '.', ',') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align: right; border: 1px solid #000; font-weight: bold;">Total</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($TotalDebit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($TotalCredit, 2, '.', ',') }}</td>
            <td style="text-align: right; border: 1px solid #000; font-weight: bold;">{{ number_format($CurBalance, 2, '.', ',') }}</td>
        </tr>
    </tfoot>
</table>

