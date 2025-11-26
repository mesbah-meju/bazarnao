@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Fixed Assets Annual Report') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('fixed-asset-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Fixed Assets Annual Report') }}</h5>
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
                        <label for="fyear" class="form-label">{{ translate('Financial Year') }}</label>
                        <select name="fyear" id="fyear" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('Select a Year') }}</option>
                            @foreach($fyears as $year)
                            <option value="{{ $year->id }}" {{ (isset($fyear) && $fyear == $year->id) ? 'selected' : '' }}>{{ $year->year_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="button" class="form-label text-white">{{ translate('Button') }}</label><br>
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
                <strong><u class="pt-4">{{ translate('Fixed Assets Annual Report') }} {{ $currentYear->year_name }}</u></strong>
                <br>
                <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead>
                    <tr>
                        <th>{{ translate('Particulars') }}</th>
                        <th>{{ translate('Opening Balance of Fixed Assets') }}</th>
                        <th>{{ translate('Additions') }}</th>
                        <th>{{ translate('Adjustment') }}</th>
                        <th>{{ translate('Closing Balance of Fixed Assets') }}</th>
                        <th>{{ translate('Depreciation Rate') }}</th>
                        <th>{{ translate('Depreciation Value') }}</th>
                        <th>{{ translate('Opening Balance of Accumulated Depreciation') }}</th>
                        <th>{{ translate('Additions') }}</th>
                        <th>{{ translate('Adjustment') }}</th>
                        <th>{{ translate('Closing Balance of Accumulated Depreciation') }}</th>
                        <th>{{ translate('Written Down Value') }}</th>
                    </tr>
                </thead>
                <tbody class="table-bordered">
                    @if(count($fixedAssets) > 0)
                    @foreach($fixedAssets as $fixedAsset)
                    <tr>
                        <td>{{ $fixedAsset['headName'] }}</td>
                        <td colspan="11"></td>
                    </tr>
                    @if(count($fixedAsset['nextlevel']) > 0)
                    @foreach ($fixedAsset['nextlevel'] as $value)
                    <tr>
                        <td style="padding-left: 50px;">{{ $value['headName'] }}</td>
                        <td colspan="11" class="profitamount"></td>
                    </tr>

                    @if(count($value['innerHead']) > 0)
                    @foreach($value['innerHead'] as $inner)
                    <tr>
                        <td style="padding-left: 100px;">{{ $inner['headName'] }}</td>
                        <td class="profitamount">
                            {{ number_format($inner['openig'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['curentDebit'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['curentCredit'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['curentValue'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ $inner['depRate'] . ' %' }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['depAmount'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['revOpening'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['revCredit'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['revDebit'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['revBalance'], 2) }}
                        </td>
                        <td class="profitamount">
                            {{ number_format($inner['famount'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    <tr>
                        <td><strong>{{ translate('Total') }}</strong></td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal1'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal2'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal3'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal4'], 2) }}</strong>
                        </td>
                        <td class="profitamount"><strong></strong></td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal5'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal6'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal7'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal8'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal9'], 2) }}</strong>
                        </td>
                        <td class="profitamount">
                            <strong>{{ number_format($fixedAssets[0]['subtotal10'], 2) }}</strong>
                        </td>
                    </tr>
                    @endif
                </tbody>
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
        
        tbody {
            display: table-row-group;
        }
    }
</style>

<script>
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Fixed Asset Report</title>');
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
        var url = '{{ route("fixed-asset-report.index") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&fyear=' + encodeURIComponent(fyear);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection