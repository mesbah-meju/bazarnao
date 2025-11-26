@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Balance Sheet') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_balance_sheet" action="{{ route('balance-sheet.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Balance Sheet') }}</h5>
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
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
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
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" id="dtpFromDate" class="datepicker form-control" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}" placeholder="{{ translate('From Date') }}" >
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text"  name="dtpToDate" id="dtpToDate" class="datepicker form-control" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}" placeholder="{{ translate('To Date') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="text-left">
                            <button type="submit" class="btn btn-success">{{ translate('Filter') }}</button>
                            <button class="btn btn-md btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            <button class="btn btn-md btn-primary" onclick="downloadExcel()" type="button">{{ translate('Excel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body printArea">
        <div class="table-responsive">
            <table border="0" width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br><br>
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
                    <b>{{ translate('Balance Sheet') }} {{ translate('From') }} {{ date('d-m-Y', strtotime($dtpFromDate)) }} {{ translate('To') }} {{ date('d-m-Y', strtotime($dtpToDate)) }}</b>
                    <br>
                    <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
                </caption>
            </table>

            <table width="99%" align="left" class=" table table-striped table-bordered table-hover print-font-size general_ledger_report_tble">
                <thead class="table-bordered">
                    <tr>
                        <td width="40%" bgcolor="#E7E0EE" align="center"> 
                            <strong>{{ translate('Particulars') }}</strong>
                        </td>
                        <td width="20%" bgcolor="#E7E0EE" align="right" class="profitamount">
                            <strong>{{ Session::get('fyearName') }}</strong>
                        </td>
                        @foreach($financialyears as $financialyear)
                        <td width="20%" bgcolor="#E7E0EE" align="right" class="profitamount">
                            <strong>{{ $financialyear  }}</strong>
                        </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="table-bordered">
                    @foreach($assets as $asset)
                        <tr>
                            <td align="left">{{ $asset['head'] }}</td>
                            <td align="right" colspan="3"></td>
                        </tr>
                        @if(count($asset['nextlevel']) > 0)
                            @foreach ($asset['nextlevel'] as  $value)
                                @if($value['subtotal'] != 0 || $value['ssubtotal'] != 0 || $value['tsubtotal'] != 0)
                                <tr>
                                    <td align="left" style="padding-left: 80px;">{{ $value['headName'] }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '. number_format($value['subtotal'],2) }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['ssubtotal'],2) }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['tsubtotal'],2) }}</td>
                                </tr>
                                @endif
                                @if(count($value['innerHead']) > 0)
                                    @foreach($value['innerHead'] as $inner)
                                        @if($inner['amount'] != 0 || $inner['secondyear'] != 0 || $inner['thirdyear'] != 0)
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['amount'],2) }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['secondyear'],2) }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['thirdyear'],2) }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <tr>
                        <td align="right"><strong>{{ translate('Total Assets') }}</strong></td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($assets[0]['gtotal'] ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($assets[0]['sgtotal'] ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($assets[0]['tgtotal'] ,2) }}</strong>
                        </td>
                    </tr>

                    <tr bgcolor="#E7E0EE">
                        <td colspan="4"> &nbsp;</td>
                    </tr>
                    @foreach($liabilities as $liability)
                        <tr>
                            <td align="left">{{ $liability['head'] }}</td>
                            <td align="right" colspan="3"></td>
                        </tr>
                        @if(count($liability['nextlevel']) > 0)
                            @foreach ($liability['nextlevel'] as  $value)
                                @if($value['subtotal'] != 0 || $value['ssubtotal'] != 0 || $value['tsubtotal'] != 0)
                                <tr>
                                    <td align="left" style="padding-left: 80px;">{{ $value['headName'] }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['subtotal'],2) }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['ssubtotal'],2) }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['tsubtotal'],2) }}</td>
                                </tr>
                                @endif
                                @if(count($value['innerHead']) > 0)
                                    @foreach($value['innerHead'] as $inner)
                                        @if($inner['amount'] != 0 || $inner['secondyear'] != 0 || $inner['thirdyear'] != 0)
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['amount'],2) }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['secondyear'],2) }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['thirdyear'],2) }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <tr>
                        <td align="right"><strong>{{ translate('Total Liabilities') }}</strong></td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($liabilities[0]['gtotal'] ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($liabilities[0]['sgtotal'] ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($liabilities[0]['tgtotal'] ,2) }}</strong>
                        </td>
                    </tr>


                    <tr bgcolor="#E7E0EE">
                        <td colspan="4"> &nbsp;</td>
                    </tr>

                    @foreach($equitys as $equity)
                        <tr>
                            <td align="left">{{ $equity['head'] }}</td>
                            <td align="right" colspan="3"></td>
                        </tr>
                        @if(count($equity['nextlevel']) > 0)
                            @foreach ($equity['nextlevel'] as  $value)
                                @if($value['subtotal'] != 0 || $value['ssubtotal'] != 0 || $value['tsubtotal'] != 0)
                                <tr>
                                    <td align="left" style="padding-left: 80px;">{{ $value['headName'] }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['subtotal'],2) }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['ssubtotal'],2) }}</td>
                                    <td align="right" class="profitamount">{{ $currency. ' '.  number_format($value['tsubtotal'],2) }}</td>
                                </tr>
                                @endif
                                @if(count($value['innerHead']) > 0)
                                    @foreach($value['innerHead'] as $inner)
                                        @if($inner['amount'] != 0 || $inner['secondyear'] != 0 || $inner['thirdyear'] != 0)
                                        <tr>
                                            <td align="left" style="padding-left: 160px;">{{ $inner['headName'] }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['amount'],2) }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['secondyear'],2) }}</td>
                                            <td align="right" class="profitamount">{{ $currency. ' '.  number_format($inner['thirdyear'],2) }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <tr>
                        <td align="right"><strong>{{ translate('Total Equity') }}</strong></td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($equitys[0]['gtotal'] ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($equitys[0]['sgtotal'] ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format($equitys[0]['tgtotal'] ,2) }}</strong>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-bordered">
                    <tr>
                        <td align="right"><strong>{{ translate('Total Liabilities Equity') }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format(($liabilities[0]['gtotal'] + $equitys[0]['gtotal'] ),2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format(($liabilities[0]['sgtotal'] + $equitys[0]['sgtotal']) ,2) }}</strong>
                        </td>
                        <td align="right" class="profitamount">
                            <strong>{{ $currency. ' '.  number_format(($liabilities[0]['tgtotal'] + $equitys[0]['tgtotal']) ,2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script')

<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
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
        printWindow.document.write('<html><head><title>Balance Sheet Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 0; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 5px; font-size: 12px; }');
        printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.text-right { text-align: right; }');
        printWindow.document.write('.text-center { text-align: center; }');
        printWindow.document.write('.noborder { border: none !important; }');
        printWindow.document.write('img { max-height: 40px; }');
        printWindow.document.write('h2, h3, h4, h5, h6 { margin: 5px 0; }');
        printWindow.document.write('p { margin: 3px 0; }');
        printWindow.document.write('@page { size: A4; margin: 0.5cm; }');
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
        var dtpFromDate = document.getElementById('dtpFromDate').value;
        var dtpToDate = document.getElementById('dtpToDate').value;

        // Build URL with parameters
        var url = '{{ route("balance-sheet.index") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection