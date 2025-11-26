@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Income Statement') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('income-statement-yearly-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Income Statement') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin') 
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="fyear">{{ translate('Year') }}</label>
                        <select name="fyear" id="fyear" class="form-control aiz-selectpicker" data-live-search="true">
                            @foreach($fyears as $fyear)
                            <option value="{{ $fyear->id }}" {{ (isset($curentYear) && $curentYear->id == $fyear->id) ? 'selected' : '' }}>{{ $fyear->year_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label text-white">{{ translate('To Date') }}</label><br>
                        <button type="submit" class="btn btn-success">{{ translate('Filter') }}</button>
                        <button class="btn btn-md btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                        <button class="btn btn-md btn-primary" onclick="downloadExcel()" type="button">{{ translate('Excel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="row pb-3 align-items-center">
            <div class="col-md-3">
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h4><strong class="">Bazar Nao Limited</strong><br></h4>
                4th Floor, AGM Chandrima, House 12, Road 08, Block J, Baridhara, Dhaka-1212.<br>
                info@bazarnao.com<br>
                +880 1969 906 699<br>
            </div>
            <div class="col-md-3 text-right">
                <div class="pull-right">
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                    </b>
                </div>
            </div>
        </div>

        <div class="row pb-3 voucher-center align-items-center">
            <div class="col-md-12 text-center">
                <strong><u class="pt-4">{{ translate('Income Statement for ' . $curentYear->year_name) }}</u></strong>
                <br>
                <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
            <table width="99%" align="left" class="datatableReport table table-striped table-bordered table-hover general_ledger_report_tble print-font-size">
                <thead>
                    <tr>
                        <th width="16%" bgcolor="#E7E0EE" align="left">{{ translate('Particulars') }}</th>
                        @php
                            $time = strtotime($curentYear->start_date);
                            $startmonth = date('n',  strtotime($curentYear->start_date));
                        @endphp
                        
                        @foreach ($financial_years as $financial_year)
                            <th width="7%" bgcolor="#E7E0EE" class="profitamount text-right">{{ $financial_year->year_name; }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (count($incomes) > 0)
                        @foreach ($incomes as $income)
                            <tr>
                                <td align="left">{{ $income['head'] }}</td>
                                <td align="right" colspan="12"></td>
                            </tr>
                            @if (count($income['nextlevel']) > 0)
                                @foreach ($income['nextlevel'] as  $value)
                                    <tr>
                                        <td align="left" style="padding-left: 80px;">{{ $value['headName'] }}</td>
                                        <td align="right" colspan="12" class="profitamount"></td>
                                    </tr>
                                    @if (count($value['innerHead']) > 0)
                                        @foreach ($value['innerHead'] as $inner)
                                            @if ($startmonth == 1)
                                                @php
                                                    $yearly_incomes = $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'] + $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'];
                                                @endphp
                                                @if($yearly_incomes != 0)
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($yearly_incomes, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                </tr>
                                                @endif
                                            @else
                                                @php
                                                    $yearly_incomes = $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'] + $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'];
                                                @endphp
                                                @if($yearly_incomes != 0)
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($yearly_incomes, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @if ($startmonth == 1)
                        @php
                            $total_yearly_incomes = $incomes[0]['gtotal1'] + $incomes[0]['gtotal2'] + $incomes[0]['gtotal3'] + $incomes[0]['gtotal4'] + $incomes[0]['gtotal5'] + $incomes[0]['gtotal6'] + $incomes[0]['gtotal7'] + $incomes[0]['gtotal8'] + $incomes[0]['gtotal9'] + $incomes[0]['gtotal10'] + $incomes[0]['gtotal11'] + $incomes[0]['gtotal12'];
                        @endphp
                            <tr>
                                <td align="right"><strong>{{ translate('Total Income') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($total_yearly_incomes, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @else
                        @php
                            $total_yearly_incomes = $incomes[0]['gtotal7'] + $incomes[0]['gtotal8'] + $incomes[0]['gtotal9'] + $incomes[0]['gtotal10'] + $incomes[0]['gtotal11'] + $incomes[0]['gtotal12'] + $incomes[0]['gtotal1'] + $incomes[0]['gtotal2'] + $incomes[0]['gtotal3'] + $incomes[0]['gtotal4'] + $incomes[0]['gtotal5'] + $incomes[0]['gtotal6'];
                        @endphp
                            <tr>
                                <td align="right"><strong>{{ translate('Total Income') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($total_yearly_incomes, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr bgcolor="#E7E0EE">
                            <td colspan="13"> &nbsp;</td>
                        </tr>
                    @endif

                    @if (count($costofgoodsolds) > 0)
                        @foreach ($costofgoodsolds as $costofgoodsold)
                            <tr>
                                <td align="left" style="padding-left: 80px;">{{ $costofgoodsold['headName'] }}</td>
                                <td align="right" colspan="12"></td>
                            </tr>

                            @if (count($costofgoodsold['innerHead']) > 0)
                                @foreach ($costofgoodsold['innerHead'] as $inner)
                                    @if ($startmonth == 1)
                                    @php
                                        $yearly_cost_of_goods_sold = $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'] + $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'];
                                    @endphp
                                        @if($yearly_cost_of_goods_sold != 0)
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ number_format($yearly_cost_of_goods_sold, 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                        </tr>
                                        @endif
                                    @else
                                    @php
                                        $yearly_cost_of_goods_sold = $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'] + $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'];
                                    @endphp
                                        @if($yearly_cost_of_goods_sold != 0)
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ number_format($yearly_cost_of_goods_sold, 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                        </tr>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @if ($startmonth == 1)
                        @php
                            $total_yearly_cost_of_goods_sold = $costofgoodsolds[0]['subtota1'] + $costofgoodsolds[0]['subtota2'] + $costofgoodsolds[0]['subtota3'] + $costofgoodsolds[0]['subtota4'] + $costofgoodsolds[0]['subtota5'] + $costofgoodsolds[0]['subtota6'] + $costofgoodsolds[0]['subtota7'] + $costofgoodsolds[0]['subtota8'] + $costofgoodsolds[0]['subtota9'] + $costofgoodsolds[0]['subtota10'] + $costofgoodsolds[0]['subtota11'] + $costofgoodsolds[0]['subtota12'];
                        @endphp
                            <tr>
                                <td align="right"><strong>{{ translate('Total Cost of Goods Sold') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($total_yearly_cost_of_goods_sold, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @else
                        @php
                            $total_yearly_cost_of_goods_sold = $costofgoodsolds[0]['subtota7'] + $costofgoodsolds[0]['subtota8'] + $costofgoodsolds[0]['subtota9'] + $costofgoodsolds[0]['subtota10'] + $costofgoodsolds[0]['subtota11'] + $costofgoodsolds[0]['subtota12'] + $costofgoodsolds[0]['subtota1'] + $costofgoodsolds[0]['subtota2'] + $costofgoodsolds[0]['subtota3'] + $costofgoodsolds[0]['subtota4'] + $costofgoodsolds[0]['subtota5'] + $costofgoodsolds[0]['subtota6'];
                        @endphp
                            <tr>
                                <td align="right"><strong>{{ translate('Total Cost of Goods Sold') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($total_yearly_cost_of_goods_sold, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                        @if ($startmonth == 1)
                        @php
                            $yearly_gross_profit = $total_yearly_incomes - $total_yearly_cost_of_goods_sold;
                        @endphp
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Gross Profit') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($yearly_gross_profit, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr> 
                        @else
                        @php
                            $yearly_gross_profit = $total_yearly_incomes - $total_yearly_cost_of_goods_sold;
                        @endphp
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Gross Profit') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($yearly_gross_profit, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                    @endif

                    @if (count($expenses) > 0)
                        @foreach ($expenses as $expense)
                            <tr>
                                <td align="left">{{ $expense['head'] }}</td>
                                <td align="right" colspan="12"></td>
                            </tr>
                            @if (count($expense['nextlevel']) > 0)
                                @foreach ($expense['nextlevel'] as  $value)
                                    <tr>
                                        <td align="left" style="padding-left: 80px;">{{ $value['headName'] }}</td>
                                        <td align="right" colspan="12" class="profitamount"></td>
                                    </tr>
                                    @if (count($value['innerHead']) > 0)
                                        @foreach ($value['innerHead'] as $inner)
                                            @if ($startmonth == 1)
                                            @php
                                                $yearly_expenses = $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'] + $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'];
                                            @endphp
                                                @if($yearly_expenses != 0)
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($yearly_expenses, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                </tr>
                                                @endif
                                            @else
                                            @php
                                                $yearly_expenses = $inner['amount7'] + $inner['amount8'] + $inner['amount9'] + $inner['amount10'] + $inner['amount11'] + $inner['amount12'] + $inner['amount1'] + $inner['amount2'] + $inner['amount3'] + $inner['amount4'] + $inner['amount5'] + $inner['amount6'];
                                            @endphp
                                                @if($yearly_expenses != 0)
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($yearly_expenses, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format(0, 2) }}</td>
                                                </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @if ($startmonth == 1)
                        @php
                            $total_yearly_expenses = $expenses[0]['gtotal1'] + $expenses[0]['gtotal2'] + $expenses[0]['gtotal3'] + $expenses[0]['gtotal4'] + $expenses[0]['gtotal5'] + $expenses[0]['gtotal6'] + $expenses[0]['gtotal7'] + $expenses[0]['gtotal8'] + $expenses[0]['gtotal9'] + $expenses[0]['gtotal10'] + $expenses[0]['gtotal11'] + $expenses[0]['gtotal12'];
                        @endphp
                            <tr>
                                <td align="right"><strong>{{ translate('Total Expense') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($total_yearly_expenses, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @else
                        @php
                            $total_yearly_expenses = $expenses[0]['gtotal7'] + $expenses[0]['gtotal8'] + $expenses[0]['gtotal9'] + $expenses[0]['gtotal10'] + $expenses[0]['gtotal11'] + $expenses[0]['gtotal12'] + $expenses[0]['gtotal1'] + $expenses[0]['gtotal2'] + $expenses[0]['gtotal3'] + $expenses[0]['gtotal4'] + $expenses[0]['gtotal5'] + $expenses[0]['gtotal6'];
                        @endphp
                            <tr>
                                <td align="right"><strong>{{ translate('Total Expense') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($total_yearly_expenses, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                        @if ($startmonth == 1)
                        @php
                            $total_yearly_net_profit = $yearly_gross_profit - $total_yearly_expenses;
                        @endphp
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Net Amount') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($total_yearly_net_profit), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr> 
                        @else
                        @php
                            $total_yearly_net_profit = $yearly_gross_profit - $total_yearly_expenses;
                        @endphp
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Net Amount') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($total_yearly_net_profit), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(0, 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
            <table border="0" width="100%" style="padding-top: 100px;">
                <tr>
                    <td align="left" class="noborder">
                        <div class="border-top">{{ translate('Prepared By') }}</div>
                    </td>
                    <td align="center" class="noborder">
                        <div class="border-top">{{ translate('Checked By') }}</div>
                    </td>
                    <td align="right" class="noborder">
                        <div class="border-top">{{ translate('Authorised By') }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script')

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 0.5cm;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        /* Hide everything except printArea */
        body > *:not(.wrapper):not(style):not(script) {
            display: none !important;
        }
        
        .aiz-main-wrapper,
        .aiz-sidebar,
        .aiz-topbar,
        form,
        .card-header {
            display: none !important;
        }
        
        .printArea {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        table {
            width: 100% !important;
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }
    }
</style>

<script>
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Income Statement Yearly Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 0; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 5px; font-size: 10px; }');
        printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.text-right { text-align: right; }');
        printWindow.document.write('.text-center { text-align: center; }');
        printWindow.document.write('.pull-right { float: right; }');
        printWindow.document.write('img { max-height: 40px; }');
        printWindow.document.write('h2, h3, h4, h5, h6 { margin: 5px 0; }');
        printWindow.document.write('p { margin: 3px 0; }');
        printWindow.document.write('.noborder { border: none !important; }');
        printWindow.document.write('.border-top { border-top: 1px solid #000; padding-top: 5px; }');
        printWindow.document.write('@page { size: A4 landscape; margin: 0.5cm; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(document.querySelector('.printArea').innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
            setTimeout(function() { printWindow.close(); }, 100);
        }, 250);
    }

    function downloadExcel() {
        // Get current form values
        var warehouse_id = document.getElementById('warehouse_id').value;
        var fyear = document.getElementById('fyear').value;

        // Build URL with parameters
        var url = '{{ route("income-statement-yearly-report.index") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&fyear=' + encodeURIComponent(fyear);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection