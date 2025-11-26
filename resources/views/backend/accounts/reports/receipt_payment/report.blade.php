@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Receipt & Payment') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('receipt-payment-report.report') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Receipt & Payment') }}</h5>
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
                            <option value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
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
                            <option value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpFromDate">{{ translate('From Date') }}</label>
                        <input type="text" id="dtpFromDate" name="dtpFromDate" class="form-control datepicker" value="{{ isset($dtpFromDate) ? date('m/d/Y', strtotime($dtpFromDate)) : date('m/d/Y',strtotime('first day of this month')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpToDate">{{ translate('To Date') }}</label>
                        <input type="text" id="dtpToDate" name="dtpToDate" class="form-control datepicker" value="{{ isset($dtpToDate) ? date('m/d/Y', strtotime($dtpToDate)) : date('m/d/Y', strtotime('last day of this month')) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="reportType" class="form-label">{{ translate('Type') }}</label><br>
                        <select name="reportType" id="reportType" class="form-control aiz-selectpicker">
                            <option value="Accrual Basis" {{ (isset($reportType) && $reportType == 'Accrual Basis') ? 'selected' : '' }}>{{ translate('Accrual Basis') }}</option>
                            <option value="Cash Basis" {{ (isset($reportType) && $reportType == 'Cash Basis') ? 'selected' : '' }}>{{ translate('Cash Basis') }}</option>
                        </select>
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
                    <b><label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}</b>
                </div>
            </div>
        </div>

        <div class="row pb-3 voucher-center align-items-center">
            <div class="col-md-12 text-center">
                <strong><u class="pt-4">{{ translate('Receipt & Payment') }} {{ translate('From') }} {{ date('d-m-Y', strtotime($dtpFromDate)) }} {{ translate('To') }} {{ date('d-m-Y', strtotime($dtpToDate)) }}</u></strong>
                <br>
                <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table width="99%" align="left" class="datatableReport table table-striped table-bordered table-hover general_ledger_report_tble">
                        <thead class="table-bordered">
                            <tr>
                                <th width="60%" bgcolor="#E7E0EE" align="left">
                                    <?php echo translate('Particulars') ?>
                                </th>
                                <th class="profitamount" width="40%" bgcolor="#E7E0EE" align="left">
                                    <?php echo translate('Balance') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="table-bordered">
                            <tr>
                                <td height="70" align="left" colspan="2">
                                    <strong><?php echo translate('Opening Balance') ?></strong>
                                </td>
                            </tr>
                            <?php if ($cashOpening != 0) { ?>
                            <tr>
                                <td align="left" style="padding-left: 160px;">
                                    <?php echo  translate('Cash In Hand'); ?>
                                </td>
                                <td align="right" class="profitamount">
                                    <?php echo $currency . ' ' . number_format($cashOpening, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($bankOpening != 0) { ?>
                            <tr>
                                <td align="left" style="padding-left: 160px;">
                                    <?php echo  translate('Cash At Bank'); ?>
                                </td>
                                <td align="right" class="profitamount">
                                    <?php echo $currency . ' ' . number_format($bankOpening, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($advOpening != 0) { ?>
                            <tr>
                                <td align="left" style="padding-left: 160px;">
                                    <?php echo  translate('Advance'); ?>
                                </td>
                                <td align="right" class="profitamount">
                                    <?php echo $currency . ' ' . number_format($advOpening, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td height="70" align="left" colspan="2">
                                    <strong><?php echo translate('Receipts') ?></strong>
                                </td>
                            </tr>
                            <?php if (count($receiptitems) >= 0) {
                                $gtotal = 0;
                                foreach ($receiptitems as $receiptitem) { ?>
                                    <tr>
                                        <td align="left" style="padding-left: 80px;">
                                            <?php echo $receiptitem['headName']; ?></td>
                                        <td align="right"></td>
                                    </tr>

                                    <?php if (count($receiptitem['innerHead']) > 0) {
                                        foreach ($receiptitem['innerHead'] as $inner) { 
                                            if ($inner['credit'] != 0) { ?>
                                            <tr>
                                                <td align="left" style="padding-left: 160px;"><?php echo $inner['headName']; ?>
                                                </td>
                                                <td align="right" class="profitamount">
                                                    <?php echo $currency . ' ' . number_format($inner['credit'], 2); ?></td>
                                            </tr>
                                    <?php }
                                        }
                                    }
                                    $gtotal += $inner['credit']; ?>
                                <?php } ?>
                                <tr bgcolor="#E7E0EE">
                                    <td align="right"><strong><?php echo translate('Total'); ?></strong></td>
                                    <td align="right" class="profitamount">
                                        <strong><?php echo $currency . ' ' . number_format($gtotal, 2); ?></strong>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr bgcolor="#E7E0EE">
                                <td align="right"><strong><?php echo translate('Grand Total'); ?></strong></td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo $currency . ' ' . number_format(($gtotal + $cashOpening + $bankOpening + $advOpening), 2); ?></strong>
                                </td>
                            </tr>

                            <tr>
                                <td height="70" align="left" colspan="2">
                                    <strong><?php echo translate('Payments') ?></strong>
                                </td>
                            </tr>
                            <?php if (count($paymentitems) >= 0) {
                                $pgtotal = 0;
                                foreach ($paymentitems as $paymentitem) { ?>
                                    <tr>
                                        <td align="left" style="padding-left: 80px;">
                                            <?php echo $paymentitem['headName']; ?></td>
                                        <td align="right"></td>
                                    </tr>

                                    <?php if (count($paymentitem['innerHead']) > 0) {
                                        foreach ($paymentitem['innerHead'] as $inner) { 
                                            if ($inner['debit'] != 0) { ?>
                                            <tr>
                                                <td align="left" style="padding-left: 160px;"><?php echo $inner['headName']; ?>
                                                </td>
                                                <td align="right" class="profitamount">
                                                    <?php echo $currency . ' ' . number_format($inner['debit'], 2); ?></td>
                                            </tr>
                                    <?php }
                                        }
                                    }
                                    $pgtotal += $inner['debit']; ?>
                                <?php } ?>
                                <tr bgcolor="#E7E0EE">
                                    <td align="right"><strong><?php echo translate('Total'); ?></strong></td>
                                    <td align="right" class="profitamount">
                                        <strong><?php echo $currency . ' ' . number_format($pgtotal, 2); ?></strong>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td height="70" align="left" colspan="2">
                                    <strong><?php echo translate('Closing Balance') ?></strong>
                                </td>
                            </tr>
                            <?php if ($cashClosing != 0) { ?>
                            <tr>
                                <td align="left" style="padding-left: 160px;">
                                    <?php echo translate('Cash In Hand'); ?>
                                </td>
                                <td align="right" class="profitamount">
                                    <?php echo $currency . ' ' . number_format($cashClosing, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($bankClosing != 0) { ?>
                            <tr>
                                <td align="left" style="padding-left: 160px;">
                                    <?php echo translate('Cash At Bank'); ?>
                                </td>
                                <td align="right" class="profitamount">
                                    <?php echo $currency . ' ' . number_format($bankClosing, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($advClosing != 0) { ?>
                            <tr>
                                <td align="left" style="padding-left: 160px;">
                                    <?php echo translate('Advance'); ?>
                                </td>
                                <td align="right" class="profitamount">
                                    <?php echo $currency . ' ' . number_format($advClosing, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr bgcolor="#E7E0EE">
                                <td align="right">
                                    <strong><?php echo translate('Grand Total'); ?></strong>
                                </td>
                                <td align="right" class="profitamount">
                                    <strong><?php echo $currency . ' ' . number_format(($pgtotal + $advClosing + $bankClosing + $cashClosing), 2); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                        <table border="0" width="100%">
                            <tr>
                                <td align="left" class="noborder">
                                    <div class="border-top"><?php echo translate('Prepared By') ?></div>
                                </td>
                                <td align="center" class="noborder">
                                    <div class="border-top"><?php echo translate('Checked By') ?></div>
                                </td>
                                <td align="right" class="noborder">
                                    <div class="border-top"><?php echo translate('Authorised By') ?></div>
                                </td>
                            </tr>
                        </table>
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

<script type="text/javascript">
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Receipt & Payment Report</title>');
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
        printWindow.document.write('.border-top { border-top: 1px solid #000; padding-top: 5px; margin-top: 100px; }');
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
        var reportType = document.getElementById('reportType').value;

        // Build URL with parameters
        var url = '{{ route("receipt-payment-report.report") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);
        url += '&reportType=' + encodeURIComponent(reportType);

        // Redirect to download
        window.location.href = url;
    }
</script>
@endsection