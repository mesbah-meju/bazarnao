@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Expenditure Statement') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('expenditure-statement-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Expenditure Statement') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin') 
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" id="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" id="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label text-white">{{ translate('Button') }}</label><br>
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
                <strong><u class="pt-4">{{ translate('Expenditure Statement') }} {{ translate('from') }} {{ date('d-m-Y', strtotime($dtpFromDate)) }} {{ translate('to') }} {{ date('d-m-Y', strtotime($dtpToDate)) }}</u></strong>
                <br>
                <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table width="99%" align="left" class="datatableReport table table-striped table-bordered print-font-size table-hover general_ledger_report_tble">
                        <thead class="table-bordered">
                            <tr>
                                <th width="60%" bgcolor="#E7E0EE" align="center"><?php echo translate('Particulars') ?></th>
                                <th width="20%" bgcolor="#E7E0EE" align="right" class="text-right" class="profitamount"><?php echo translate('Amount') ?></th>
                                <th width="20%" bgcolor="#E7E0EE" align="right" class="text-right" class="profitamount"><?php echo translate('Amount') ?></th>
                            </tr>
                        </thead>
                        <tbody class="table-bordered">
                            <?php foreach ($expenses as $expense) { ?>
                                <tr>
                                    <td align="left"><?php echo $expense['head']; ?></td>
                                    <td align="right" colspan="2"></td>
                                </tr>
                                <?php if (count($expense['nextlevel']) > 0) {
                                    foreach ($expense['nextlevel'] as  $value) { 
                                        if ($value['subtotal'] != 0) { ?>
                                        <tr>
                                            <td align="left" style="padding-left: 80px;"><?php echo $value['headName']; ?></td>
                                            <td align="right" class="profitamount"> &nbsp;</td>
                                            <td align="right" class="profitamount">
                                                <?php echo $currency . ' ' . number_format($value['subtotal'], 2); ?></td>
                                        </tr>
                                        <?php }
                                        if (count($value['innerHead']) > 0) {
                                            foreach ($value['innerHead'] as $inner) { 
                                                if ($inner['amount'] != 0) { ?>
                                                <tr>
                                                    <td align="left" style="padding-left: 160px;"><?php echo $inner['headName']; ?></td>
                                                    <td align="right" class="profitamount">
                                                        <?php echo $currency . ' ' . number_format($inner['amount'], 2); ?></td>
                                                    <td> &nbsp; </td>
                                                </tr>
                            <?php }
                                            }
                                        }
                                    }
                                }
                            } ?>
                            <tr>
                                <td align="right"><strong><?php echo translate('total'); ?></strong></td>
                                <td align="right" class="profitamount" colspan="2">
                                    <strong><?php echo $currency . ' ' . number_format($expenses[0]['gtotal'], 2); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table width="100%" cellpadding="1" cellspacing="20" class="print-font-size" style="margin-top: 200px;">
                        <tr>
                            <td width="20%" class="noborder" align="center"><?php echo translate('Prepared By') ?></td>
                            <td width="20%" class="noborder" align="center"><?php echo translate('Accounts') ?></td>
                            <td width="20%" class="noborder" align="center"><?php echo translate('Authorized Signature') ?></td>
                            <td width="20%" class="noborder" align='center'><?php echo translate('Chairman') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
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
        
        tbody {
            display: table-row-group;
        }
    }
</style>

<script>
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Expenditure Statement Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 0; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 5px; font-size: 12px; }');
        printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.text-right { text-align: right; }');
        printWindow.document.write('.text-center { text-align: center; }');
        printWindow.document.write('.pull-right { float: right; }');
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
        var url = '{{ route("expenditure-statement-report.index") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection
