@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Bank Book') }}</h1>
        </div>
    </div>
</div>

<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>

<div class="card">
<form id="sort_bank_book" action="{{ route('bank-book-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Bank Book') }}</h5>
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
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
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
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="cmbCode">{{ translate('Bank Account') }} <span class="text-danger">*</span></label>
                        <select name="cmbCode" id="cmbCode" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($bankbook as $bank)
                                <option value="{{ $bank->head_code }}" {{ (isset($cmbCode) && $cmbCode == $bank->head_code) ? 'selected' : '' }}>{{ $bank->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpFromDate">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="dtpToDate">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                            <button class="btn btn-md btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            <button class="btn btn-md btn-success" onclick="downloadExcel()" type="button">{{ translate('Excel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <div class="card-body printArea">
        <div class="row pb-3 voucher-center">
            <div class="col-md-3">
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h2>Bazarnao</h2>
                <strong><u class="pt-4">{{ translate('Bank Book Report') }}</u></strong>
                <br>
                <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
                <p><strong>{{ translate('From') }}:</strong> {{ date('d-m-Y', strtotime($dtpFromDate)) }} <strong>{{ translate('To') }}:</strong> {{ date('d-m-Y', strtotime($dtpToDate)) }}</p>
            </div>
            
            <div class="col-md-3">
                <div class="pull-right" style="margin-right:20px;">
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                    </b>
                    <br>
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : {{ number_format($prebalance,2,'.',',');}}
                    </b>
                    <br>
                    @php
                        $TotalCredit = 0;
                        $TotalDebit  = 0;
                        $CurBalance = $prebalance;
                        $openid = 1; 
                    @endphp

                    @foreach($HeadName2 as $key => $data2)
                        @php 
                            $TotalDebit += $data2->debit;
                            $TotalCredit += $data2->credit;
                            $CurBalance += $data2->debit - $data2->credit;
                        @endphp
                    @endforeach
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : {{number_format($CurBalance,2,'.',',');}}
                    </b>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
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
                        $TotalDebit  = 0;
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
                    <tr>
                        <td>{{ ++$key + $openid }}</td>
                        <td>{{ date('d-m-Y', strtotime($data->voucher_date)) }}</td>
                        <td>{{ $data->rev_coa->head_name }}</td>
                        <td>
                            @if($data->relvalue && $data->reltype)
                                {{ $data->relvalue->name }}({{ $data->reltype->name }})
                            @else
                                {{ translate('N/A') }}
                            @endif
                        </td>
                        <td>{{ $data->ledger_comment }}</td>
                        <td>{{ translate($data->voucher_type) }}</td>
                        <td>{{ $data->voucher_no }}</td>
                        <td class="text-right">{{ number_format($data->debit, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format($data->credit, 2, '.', ',') }}</td>
                        @php 
                            $TotalDebit += $data->debit;
                            $TotalCredit += $data->credit;
                            $CurBalance += $data->debit - $data->credit;
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

<script type="text/javascript">
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Bank Book Report</title>');
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
        var cmbCode = document.getElementById('cmbCode').value;
        var dtpFromDate = document.querySelector('input[name="dtpFromDate"]').value;
        var dtpToDate = document.querySelector('input[name="dtpToDate"]').value;

        // Build URL with parameters
        var url = '{{ route("bank-book-report.index") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&cmbCode=' + encodeURIComponent(cmbCode);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection
