<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Contra Voucher</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: auto;
            margin: 10mm;
        }

        @media print {
            body {
                margin: 0 !important;
                padding: 20px !important;
                font-family: 'Arial', sans-serif !important;
                color: #000000 !important;
            }

            caption {
                color: #000000 !important;
            }

            .no-print {
                display: none !important;
            }

            .container {
                max-width: 100% !important;
                padding-right: 15px;
                padding-left: 15px;
            }

            .company-logo {
                height: 100px !important;
                margin-bottom: 20px !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin: 15px 0 !important;
            }

            th, td {
                padding: 5px !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>

<body class="container" onload="initPrint()">
    <table border="0" width="100%">
        <caption class="text-center">
            <table class="print-font-size" width="100%">
                <tr>
                    <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                        <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br>
                    </td>
                    <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                        <h4><strong>BAZAR NAO LTD.</strong></h4>
                        Sukhnir, Flat: B2, House: 33, Road: 1/A<br>Block: J, Baridhara, Dhaka-1212<br>
                        info@bazarnao.com<br>
                        +880 1969 906 699<br>
                    </td>
                    <td align="right" style="border-bottom: 2px #333 solid;" width="33.333%">
                        <b>
                            <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                        </b>
                        <br>
                        <b>
                            <label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : {{ number_format($prebalance,2,'.',',');}}
                        </b>
                        <br>
                        @php
                            $CurBalance = $prebalance;
                        @endphp
    
                        @foreach($HeadName2 as $key => $data2)
                            @php 
                                if($HeadName->head_type == 'A' || $HeadName->head_type == 'E') {
                                    if($data2->debit > 0) {
                                        $CurBalance += $data2->debit;
                                    }
                                    if($data2->credit > 0) {
                                        $CurBalance -= $data2->credit;
                                    }                          
                                } else {                       
                                    if($data2->debit > 0) {
                                        $CurBalance -= $data2->debit;
                                    }                          
                                    if($data2->credit > 0) {
                                        $CurBalance += $data2->credit;
                                    }
                                }
                            @endphp
                        @endforeach
                        <b>
                            <label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : {{number_format($CurBalance,2,'.',',');}}
                        </b>
                    </td>
                </tr>
            </table>
        </caption>
        <caption class="text-center">
            <strong><u class="pt-4">{{ translate('General Ledger of') . ' ' . $ledger->head_name . ' on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To ' . date('d-m-Y', strtotime($dtpToDate)) }}</u></strong>
        </caption>
    </table>


    <table border="1" width="100%">
        <thead>
            <tr>
                <th>{{ translate('SL') }}</th>
                <th>{{ translate('Date') }}</th>
                <th>{{ translate('Account Head') }}</th>
                <th>{{ translate('Party Name') }}</th>
                <th>{{ translate('Particulars') }}</th>
                <th>{{ translate('Voucher Name') }}</th>
                <th>{{ translate('Voucher No') }}</th>
                <th>{{ translate('Debit') }}</th>
                <th>{{ translate('Credit') }}</th>
                <th>{{ translate('Balance') }}</th>
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
                <td>{{ $openid }}</td>
                <td>{{ date('d-m-Y', strtotime($dtpFromDate)) }}</td>
                <td colspan="5" class="text-right"><strong>{{ translate('Opening Balance') }}</strong></td>
                <td class="text-right">{{ number_format(0, 2, '.', ',') }}</td>
                <td class="text-right">{{ number_format(0, 2, '.', ',') }}</td>
                <td class="text-right"><strong>{{ number_format($prebalance, 2, '.', ',') }}</strong></td>
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
                        $supplierName = \App\Models\Supplier::find($supplier_id)->name;
                        $purchaseNo = $purchase->purchase_no ?? '';
                    }
                }
                
                if(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $data->reference_no) {
                    $order = \App\Models\Order::find($data->reference_no);
                    if($order) {
                        $customer_id = $order->user_id;
                        $customerName = \App\Models\User::find($customer_id)->name;
                        $orderNo = $order->code ?? '';
                    }
                }
            @endphp
            <tr>
                <td>{{ ++$key + $openid }}</td>
                <td>{{ date('d-m-Y', strtotime($data->voucher_date)) }}</td>
                <td>{{ $data->rev_coa->head_name }}</td>
                <td>
                    @if($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204)
                        {{ $supplierName }}
                    @elseif(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $orderNo)
                        {{ $customerName }}
                    @elseif($data->relvalue && $data->reltype)
                        {{ $data->relvalue->name }}({{ $data->reltype->name }})
                    @else
                        {{ translate('N/A') }}
                    @endif
                </td>
                <td>
                    @if(($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204) && $purchaseNo)
                        {{ $data->ledger_comment }} for Purchase No: {{ $purchaseNo }}
                    @elseif(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $orderNo)
                        {{ $data->ledger_comment }} for Order No: {{ $orderNo }}
                    @else
                        {{ $data->ledger_comment }}
                    @endif
                </td>
                <td>
                    @if($data->voucher_type=='DV')
                        {{ translate('Debit') }}
                    @elseif($data->voucher_type=='CV')
                        {{ translate('Credit') }}
                    @elseif ($data->voucher_type=='JV')
                        {{ translate('Journal') }}
                    @else
                        {{ translate('Contra') }}
                    @endif
                </td>
                <td>{{ $data->voucher_no }}</td>
                <td class="text-right">{{ number_format($data->debit, 2, '.', ',') }}</td>
                <td class="text-right">{{ number_format($data->credit, 2, '.', ',') }}</td>
                @php 
                    $TotalDebit += $data->debit;
                    $TotalCredit += $data->credit;

                    if($HeadName->head_type == 'A' || $HeadName->head_type == 'E') {
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
                <td class="text-right">{{ number_format($CurBalance, 2, '.', ',') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right"><strong>{{ translate('Total') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($TotalDebit, 2, '.', ',') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($TotalCredit, 2, '.', ',') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($CurBalance, 2, '.', ',') }}</strong></td>
            </tr>
        </tfoot>
    </table>
        
    <script>
        function initPrint() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</body>

</html>
