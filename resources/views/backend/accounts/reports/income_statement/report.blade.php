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
    <form id="sort_debit_vouchers" action="{{ route('income-statement-report.index') }}" method="GET">
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
                        
                        @for ($i = 0; $i < 12; $i++)
                            @php $monthname = date("M-y", strtotime("+ " . $i . " month", $time)); @endphp
                            <th width="7%" bgcolor="#E7E0EE" align="right" class="profitamount">{{ $monthname; }}</th>
                        @endfor
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
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount1'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount2'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount3'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount4'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount5'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount6'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount7'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount8'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount9'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount10'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount11'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount12'], 2) }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount7'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount8'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount9'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount10'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount11'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount12'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount1'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount2'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount3'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount4'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount5'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount6'], 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @if ($startmonth == 1)
                            <tr>
                                <td align="right"><strong>{{ translate('Total Income') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal1'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal2'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal3'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal4'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal5'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal6'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal7'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal8'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal9'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal10'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal11'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal12'], 2) }}</strong>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td align="right"><strong>{{ translate('Total Income') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal1'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal2'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal3'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal4'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal5'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal6'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal7'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal8'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal9'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal10'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal11'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($incomes[0]['gtotal12'], 2) }}</strong>
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
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount1'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount2'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount3'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount4'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount5'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount6'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount7'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount8'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount9'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount10'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount11'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount12'], 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount7'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount8'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount9'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount10'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount11'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount12'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount1'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount2'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount3'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount4'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount5'], 2) }}</td>
                                            <td align="right" class="profitamount">{{ number_format($inner['amount6'], 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @if ($startmonth == 1)
                            <tr>
                                <td align="right"><strong>{{ translate('Total Cost of Goods Sold') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota1'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota2'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota3'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota4'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota5'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota6'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota7'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota8'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota9'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota10'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota11'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota12'], 2) }}</strong>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td align="right"><strong>{{ translate('Total Cost of Goods Sold') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota1'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota2'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota3'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota4'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota5'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota6'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota7'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota8'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota9'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota10'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota11'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($costofgoodsolds[0]['subtota12'], 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                        @if ($startmonth == 1)
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Gross Profit') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal1'] - $costofgoodsolds[0]['subtota1']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal2'] - $costofgoodsolds[0]['subtota2']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal3'] - $costofgoodsolds[0]['subtota3']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal4'] - $costofgoodsolds[0]['subtota4']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal5'] - $costofgoodsolds[0]['subtota5']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal6'] - $costofgoodsolds[0]['subtota6']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal7'] - $costofgoodsolds[0]['subtota7']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal8'] - $costofgoodsolds[0]['subtota8']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal9'] - $costofgoodsolds[0]['subtota9']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal10'] - $costofgoodsolds[0]['subtota10']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal11'] - $costofgoodsolds[0]['subtota11']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal12'] - $costofgoodsolds[0]['subtota12']), 2) }}</strong>
                                </td>
                            </tr> 
                        @else
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Gross Profit') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal7'] - $costofgoodsolds[0]['subtota7']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal8'] - $costofgoodsolds[0]['subtota8']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal9'] - $costofgoodsolds[0]['subtota9']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal10'] - $costofgoodsolds[0]['subtota10']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal11'] - $costofgoodsolds[0]['subtota11']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal12'] - $costofgoodsolds[0]['subtota12']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal1'] - $costofgoodsolds[0]['subtota1']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal2'] - $costofgoodsolds[0]['subtota2']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal3'] - $costofgoodsolds[0]['subtota3']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal4'] - $costofgoodsolds[0]['subtota4']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal5'] - $costofgoodsolds[0]['subtota5']), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal6'] - $costofgoodsolds[0]['subtota6']), 2) }}</strong>
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
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount1'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount2'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount3'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount4'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount5'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount6'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount7'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount8'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount9'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount10'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount11'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount12'], 2) }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount7'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount8'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount9'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount10'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount11'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount12'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount1'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount2'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount3'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount4'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount5'], 2) }}</td>
                                                    <td align="right" class="profitamount">{{ number_format($inner['amount6'], 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        @if ($startmonth == 1)
                            <tr>
                                <td align="right"><strong>{{ translate('Total Expense') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal1'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal2'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal3'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal4'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal5'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal6'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal7'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal8'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal9'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal10'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal11'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal12'], 2) }}</strong>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td align="right"><strong>{{ translate('Total Expense') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal1'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal2'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal3'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal4'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal5'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal6'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal7'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal8'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal9'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal10'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal11'], 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format($expenses[0]['gtotal12'], 2) }}</strong>
                                </td>
                            </tr>
                        @endif
                        @if ($startmonth == 1)
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Net Amount') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal1'] - ($costofgoodsolds[0]['subtota1'] + $expenses[0]['gtotal1'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal2'] - ($costofgoodsolds[0]['subtota2'] + $expenses[0]['gtotal2'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal3'] - ($costofgoodsolds[0]['subtota3'] + $expenses[0]['gtotal3'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal4'] - ($costofgoodsolds[0]['subtota4'] + $expenses[0]['gtotal4'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal5'] - ($costofgoodsolds[0]['subtota5'] + $expenses[0]['gtotal5'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal6'] - ($costofgoodsolds[0]['subtota6'] + $expenses[0]['gtotal6'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal7'] - ($costofgoodsolds[0]['subtota7'] + $expenses[0]['gtotal7'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal8'] - ($costofgoodsolds[0]['subtota8'] + $expenses[0]['gtotal8'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal9'] - ($costofgoodsolds[0]['subtota9'] + $expenses[0]['gtotal9'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal10'] - ($costofgoodsolds[0]['subtota10'] + $expenses[0]['gtotal10'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal11'] - ($costofgoodsolds[0]['subtota11'] + $expenses[0]['gtotal11'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal12'] - ($costofgoodsolds[0]['subtota12'] + $expenses[0]['gtotal12'])), 2) }}</strong>
                                </td>
                            </tr> 
                        @else
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong>{{ translate('Net Amount') }}</strong></td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal7'] - ($costofgoodsolds[0]['subtota7'] + $expenses[0]['gtotal7'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal8'] - ($costofgoodsolds[0]['subtota8'] + $expenses[0]['gtotal8'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal9'] - ($costofgoodsolds[0]['subtota9'] + $expenses[0]['gtotal9'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal10'] - ($costofgoodsolds[0]['subtota10'] + $expenses[0]['gtotal10'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal11'] - ($costofgoodsolds[0]['subtota11'] + $expenses[0]['gtotal11'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal12'] - ($costofgoodsolds[0]['subtota12'] + $expenses[0]['gtotal12'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal1'] - ($costofgoodsolds[0]['subtota1'] + $expenses[0]['gtotal1'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal2'] - ($costofgoodsolds[0]['subtota2'] + $expenses[0]['gtotal2'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal3'] - ($costofgoodsolds[0]['subtota3'] + $expenses[0]['gtotal3'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal4'] - ($costofgoodsolds[0]['subtota4'] + $expenses[0]['gtotal4'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal5'] - ($costofgoodsolds[0]['subtota5'] + $expenses[0]['gtotal5'])), 2) }}</strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong>{{ number_format(($incomes[0]['gtotal6'] - ($costofgoodsolds[0]['subtota6'] + $expenses[0]['gtotal6'])), 2) }}</strong>
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
            size: A3 landscape;
            margin: 0.5cm;
        }
        
        .btn, .print-btn, button {
            display: none !important;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        .aiz-main-wrapper,
        .aiz-sidebar,
        .aiz-topbar,
        .card-header,
        form {
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
        
        .print-font-size {
            font-size: 10px !important;
        }
    }
</style>

<script>
    function printDiv() {
        var printWindow = window.open('', '', 'height=800,width=1200');
        printWindow.document.write('<html><head><title>Income Statement (Monthly)</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10px; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 4px 6px; }');
        printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.text-right { text-align: right; }');
        printWindow.document.write('.text-center { text-align: center; }');
        printWindow.document.write('.profitamount { text-align: right; }');
        printWindow.document.write('.bg-secondary { background-color: #e0e0e0; }');
        printWindow.document.write('.bg-warning { background-color: #fff3cd; }');
        printWindow.document.write('.bg-success { background-color: #d4edda; }');
        printWindow.document.write('.noborder { border: none; padding-top: 80px; }');
        printWindow.document.write('.border-top { border-top: 2px solid #000; padding-top: 5px; }');
        printWindow.document.write('img { max-height: 40px; }');
        printWindow.document.write('h4 { font-size: 18px; font-weight: bold; margin: 3px 0; }');
        printWindow.document.write('@page { size: A3 landscape; margin: 0.5cm; }');
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
        // Build URL with parameters
        var url = '{{ route("income-statement-report.index") }}?type=excel';
        
        // Get form values
        var warehouseId = document.querySelector('select[name="warehouse_id"]').value;
        var fyear = document.querySelector('select[name="fyear"]').value;

        // Add warehouse_id if selected
        if (warehouseId) {
            url += '&warehouse_id=' + warehouseId;
        }

        // Add financial year
        if (fyear) {
            url += '&fyear=' + fyear;
        }

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection