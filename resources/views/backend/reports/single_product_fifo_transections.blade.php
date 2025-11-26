@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Balance Sheet') }}</h1>
        </div>
    </div>
</div>

<div class="card">
    

    <div class="card-body printArea">
        <div class="table-responsive">
            <table border="0" width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <img src="{{ asset('public/uploads/all/eMnwm9VZLdjZlGMCgRSeXFMkamJcuHwqsxMNikaL.png') }}" class="img-bottom-m print-logo" alt="logo"><br><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <strong>Bazar Nao Limited</strong><br>
                                4th Floor, AGM Chandrima, House 12, Road 08, Block J, Baridhara, Dhaka-1212.
                                <br>
                                info@bazarnao.com
                                <br>
                                +880 1969 906 699
                                <br>
                            </td>
                            <td align=" right" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <date> {{ translate('Date') }}: {{ date('d-M-Y') }} </date>
                                <br>
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center" style="border-bottom: 1px #c9c9c9 solid;">
                    <b>{{ translate('Store Ledger(Under FIFO Method)') }} {{ translate('From') }} {{ date('F j, Y', strtotime($from_date)) }} {{ translate('To') }} {{ date('F j, Y', strtotime($to_date)) }}</b>
                </caption>
            </table>

            <table class="table table-striped table-bordered table-hover print-font-size">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Date</th>
                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Particulars</th>
                        <th colspan="3" style="text-align: center;">Receipts</th>
                        <th colspan="3" style="text-align: center;">Issues</th>
                        <th colspan="3" style="text-align: center;">Balance</th>
                    </tr>
                    <tr>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Rate</th>
                        <th style="text-align: center;">Amount</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Rate</th>
                        <th style="text-align: center;">Amount</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Rate</th>
                        <th style="text-align: center;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalReceiptQty = 0;
                        $totalReceiptValue = 0;
                    @endphp
                    @foreach ($inventoryData as $row)
                        @php
                            // Filter out balance details where qty and amount are both 0
                            $filteredBalanceDetails = [];
                            if(isset($row['balanceDetails']) && is_array($row['balanceDetails'])) {
                                foreach($row['balanceDetails'] as $balance) {
                                    $qty = $balance['qty'] ?? 0;
                                    $total = $balance['total'] ?? 0;
                                    // Only include if qty or amount is not 0
                                    if($qty != 0 || $total != 0) {
                                        $filteredBalanceDetails[] = $balance;
                                    }
                                }
                            }
                            
                            // Filter out issue details where qty and amount are both 0
                            $filteredIssueDetails = [];
                            if(isset($row['issueDetails']) && is_array($row['issueDetails'])) {
                                foreach($row['issueDetails'] as $issue) {
                                    $qty = $issue['qty'] ?? 0;
                                    $total = $issue['total'] ?? 0;
                                    // Only include if qty or amount is not 0
                                    if($qty != 0 || $total != 0) {
                                        $filteredIssueDetails[] = $issue;
                                    }
                                }
                            }
                            
                            // Check if receipt has value
                            $hasReceipt = $row['particulars'] !== 'Opening Stock' && (($row['receiptQty'] ?? 0) != 0 || ($row['receiptTotal'] ?? 0) != 0);
                            
                            // Hide entire row if no balance, receipt, or issue details remain after filtering
                            $hideRow = count($filteredBalanceDetails) == 0 && !$hasReceipt && count($filteredIssueDetails) == 0;
                        @endphp

                        @if(!$hideRow)
                        <tr>
                            <td style="text-align: center;">{{ date('jS F, Y (h:i:s A)', strtotime($row['date'])) }}</td>
                            <td style="text-align: center;">{{ $row['particulars'] }}</td>

                            @if($row['particulars'] === 'Opening Stock')
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                            @else
                                @php
                                    $receiptQty = $row['receiptQty'] ?? 0;
                                    $receiptTotal = $row['receiptTotal'] ?? 0;
                                    // Only add to total if values are not 0
                                    if($receiptQty != 0 || $receiptTotal != 0) {
                                        $totalReceiptQty += $receiptQty;
                                        $totalReceiptValue += $receiptTotal;
                                    }
                                @endphp
                                @if($receiptQty != 0 || $receiptTotal != 0)
                                    <td style="text-align: center;">{{ $row['receiptQty'] }}</td>
                                    <td style="text-align: center;">{{ $row['receiptPerUnit'] }}</td>
                                    <td style="text-align: center;">{{ $row['receiptTotal'] }}</td>
                                @else
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                @endif
                            @endif
                            
                            <td style="text-align: center;">
                                @foreach ($filteredIssueDetails as $issueDetail)
                                    {{ $issueDetail['qty'] }}<br>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                @foreach ($filteredIssueDetails as $issueDetail)
                                    {{ $issueDetail['per_unit'] }}<br>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                @foreach ($filteredIssueDetails as $issueDetail)
                                    {{ $issueDetail['total'] }}<br>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                @foreach ($filteredBalanceDetails as $balanceDetail)
                                    {{ $balanceDetail['qty'] }}<br>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                @foreach ($filteredBalanceDetails as $balanceDetail)
                                    {{ $balanceDetail['per_unit'] }}<br>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                @foreach ($filteredBalanceDetails as $balanceDetail)
                                    {{ $balanceDetail['total'] }}<br>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: center;">Total Balance</td>
                        <td style="text-align: center;">{{ $totalReceiptQty }}</td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: center;">{{ $totalReceiptValue }}</td>
                        <td style="text-align: center;">{{ $totalIssueQty }}</td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: center;">{{ $costOfGoodsSold }}</td>
                        <td style="text-align: center;">{{ $totalBalanceQty }}</td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: center;">{{ $totalBalanceValue }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;"></td>
                        <td colspan="3" style="text-align: center;">Cost of Goods Sold</td>
                        <td colspan="3" style="text-align: center;">Stock in hand</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection


