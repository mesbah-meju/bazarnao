@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Bank Reconciliation Report') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>
<div class="card">
    <form id="sort_bank_reconciliation" action="{{ route('bank-reconciliation.report') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Bank Reconciliation Report') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin') 
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="" selected>Select Warehouse</option>
                            @foreach (\App\Models\Warehouse::all() as $key => $warehouse)
                            <option value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="" selected>Select Warehouse</option>
                            @foreach ($warehouses as $key => $warehouse)
                            <option value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bankCode" class="form-label">{{ translate('Bank Name') }}</label>
                        <select name="bankCode" id="bankCode" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Bank</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->head_code }}" {{ (isset($bankCode) && $bankCode == $bank->head_code) ? 'selected' : '' }}>
                                {{ $bank->head_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" id="dtpFromDate" class="datepicker form-control" value="{{ isset($dtpFromDate) ? date('m/d/Y', strtotime($dtpFromDate)) : date('m/d/Y', strtotime('first day of this month')) }}" placeholder="{{ translate('From Date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" id="dtpToDate" class="datepicker form-control" value="{{ isset($dtpToDate) ? date('m/d/Y', strtotime($dtpToDate)) : date('m/d/Y', strtotime('last day of this month')) }}" placeholder="{{ translate('To Date') }}">
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
                    <b>{{ translate('Bank Reconciliation Report') }} {{ translate('From') }} {{ date('d-m-Y', strtotime($dtpFromDate)) }} {{ translate('To') }} {{ date('d-m-Y', strtotime($dtpToDate)) }}</b>
                    <br>
                    <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
                    <p><strong>{{ translate('Bank') }}:</strong> {{ $bankName }}</p>
                </caption>
            </table>
            <?php if ($vouchers) { ?>
                <table width="100%" class="datatableReport table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th colspan="6" style="background-color:#151B8D; color: #fff;">
                                <?php echo translate('Approved') ?>
                            </th>
                            <th colspan="5" style="background-color:#5d61af; color: #fff;">
                                <?php echo translate('Unapproved') ?>
                            </th>
                        </tr>
                        <tr>
                            <th><?php echo translate('SL No') ?></th>
                            <th><?php echo translate('Voucher No') ?></th>
                            <th><?php echo translate('Particulars') ?></th>
                            <th><?php echo translate('Check No') ?></th>
                            <th><?php echo translate('Date') ?></th>
                            <th><?php echo translate('Amount') ?></th>
                            <th><?php echo translate('Voucher No') ?></th>
                            <th><?php echo translate('Particulars') ?></th>
                            <th><?php echo translate('Check No') ?></th>
                            <th><?php echo translate('Date') ?></th>
                            <th><?php echo translate('Amount') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl = 1;
                        $hsum = 0;
                        $nsum = 0;
                        foreach ($vouchers as $appr) {   ?>
                            <tr>
                                <td><?php echo $sl; ?></td>
                                <?php if ($appr->is_honour == 1) {
                                    $hsum += $appr->debit; ?>
                                    <td><?php echo $appr->voucher_no; ?></td>
                                    <td><?php echo $appr->account_name; ?></td>
                                    <td><?php echo $appr->cheque_no; ?></td>
                                    <td><?php echo $appr->cheque_date; ?></td>
                                    <td><?php echo $currency . ' ' . $appr->debit; ?></td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                <?php  } else {
                                    $nsum += $appr->debit; ?>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td> &nbsp; </td>
                                    <td><?php echo $appr->voucher_no; ?></td>
                                    <td><?php echo $appr->account_name; ?></td>
                                    <td><?php echo $appr->cheque_no; ?></td>
                                    <td><?php echo $appr->cheque_date; ?></td>
                                    <td><?php echo $currency . ' ' . $appr->debit; ?></td>
                                <?php } ?>
                            </tr>
                        <?php $sl++;
                        } ?>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd;"></td>
                            <td style="border-bottom: 1px solid #ddd;" colspan="4" align="right">
                                <strong><?php echo translate('Total'); ?> </strong>
                            </td>
                            <td style="border-bottom: 1px solid #ddd;">
                                <strong><?php echo $currency . ' ' . number_format($hsum, 2); ?></strong>
                            </td>
                            <td style="border-bottom: 1px solid #ddd;" colspan="4" align="right">
                                <strong><?php echo translate('Total'); ?> </strong>
                            </td>
                            <td style="border-bottom: 1px solid #ddd;">
                                <strong><?php echo $currency . ' ' . number_format($nsum, 2); ?></strong>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr bgcolor="#FFF" style="margin-top: 200px;">
                            <td colspan="11" align="center" height="120" valign="bottom">
                                <table width="100%" cellpadding="1" cellspacing="20">
                                    <tr>
                                        <td width="30%" class="noborder" align="center">
                                            <?php echo translate('Prepared By') ?>
                                        </td>
                                        <td width="30%" class="noborder" align="center">
                                            <?php echo translate('Checked By') ?>
                                        </td>
                                        <td width="30%" class="noborder" align="center">
                                            <?php echo translate('Authorised By') ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            <?php } else { ?>
                <h5> <?php echo translate('No Result Found'); ?> </h5>
            <?php } ?>
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
        printWindow.document.write('<html><head><title>Bank Reconciliation Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 0; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 5px; font-size: 10px; }');
        printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.text-right { text-align: right; }');
        printWindow.document.write('.text-center { text-align: center; }');
        printWindow.document.write('.noborder { border: none !important; }');
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
        var bankCode = document.getElementById('bankCode').value;
        var dtpFromDate = document.getElementById('dtpFromDate').value;
        var dtpToDate = document.getElementById('dtpToDate').value;

        // Build URL with parameters
        var url = '{{ route("bank-reconciliation.report") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&bankCode=' + encodeURIComponent(bankCode);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>
@endsection