@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form id="sort_daybook" action="{{ route('day-book-report.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Day Book') }}</h5>
            </div>
        </div>

        <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
        ?>
        <div class="card-body">
            <div class="row">
                @if(Auth::user()->user_type == 'admin')   
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="warehouse_id">{{ translate('Warehouse') }} <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">All Warehouse</option>
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
                            <option value="">All Warehouse</option>
                            @foreach ($warehouses as $key => $warehouse)
                            <option  value="{{ $warehouse->id }}" {{ (isset($warehouse_id) && $warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="head_code" class="form-label">{{ translate('Account Head') }}</label>
                        <select name="head_code" id="head_code" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Account Head</option>
                            @foreach($coas as $coa)
                            <option value="{{ $coa->head_code }}" @isset($head_code) @if($coa->head_code==$head_code ) selected @endif @endisset>{{ $coa->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="party_id" class="form-label">{{ translate('Party Name') }}</label>
                        <select name="party_id" id="party_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">Select Party Name</option>
                            @foreach($parties as $party)
                            <option value="{{ $party->id }}" @isset($party_id) @if($party->id==$party_id ) selected @endif @endisset>
                                {{ $party->name }} @if($party->sub_type_id != null) ({{ $party->subtype->name }}) @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpFromDate" class="form-label">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dtpToDate" class="form-label">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="button" class="form-label text-white">{{ translate('To Date') }}</label>
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
                <strong><u class="pt-4">{{ translate('Day Book Voucher') }}</u></strong>
                <br>
                <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
                <p><strong>{{ translate('From') }}:</strong> {{ date('d-m-Y', strtotime($dtpFromDate)) }} <strong>{{ translate('To') }}:</strong> {{ date('d-m-Y', strtotime($dtpToDate)) }}</p>
            </div>
            <div class="col-md-3">
                <div class="pull-right" style="margin-right:20px;">
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> :
                        {{ date('d/m/Y') }}
                    </b>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead>
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Account Head') }}</th>
                        <th>{{ translate('Party Name') }}</th>
                        <th>{{ translate('Particulars') }}</th>
                        <th>{{ translate('Voucher') }}</th>
                        <th>{{ translate('Debit') }}</th>
                        <th>{{ translate('Credit') }}</th>
                        <th>{{ translate('Rev. Head') }}</th>
                        <th>{{ translate('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $TotalCredit = 0;
                        $TotalDebit = 0;
                    @endphp
                    @if($voucherInfo->isNotEmpty())
                        @foreach($voucherInfo as $key => $row)
                        <tr class="{{ $loop->odd ? 'odd gradeX' : 'even gradeC' }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                            <td>
                                {{ optional($row->coa)->head_name }}
                                @if($row->subcode != null)
                                <br>({{ optional($row->subcode)->name }})
                                @endif
                            </td>
                            <td>
                                @if($row->relvalue && $row->reltype)
                                    {{ $row->relvalue->name }}<br>
                                    ({{ $row->reltype->name }})
                                @else
                                    {{ translate('N/A') }}
                                @endif
                            </td>
                            <td>{{ $row->ledger_comment }}</td>
                            <td>{{ $row->voucher_no }}</td>
                            <td class="text-right">{{ number_format($row->debit, 2, '.', ',') }}</td>
                            <td class="text-right">{{ number_format($row->credit, 2, '.', ',') }}</td>
                            <td>{{ optional($row->rev_coa)->head_name }}</td>
                            <td class="center">
                                @if($row->voucher_type == "DV")
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('debit-vouchers.show', $row->id) }}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                @elseif($row->voucher_type == "CV")
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('credit-vouchers.show', $row->id) }}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                @elseif($row->voucher_type == "CT")
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('contra-vouchers.show', $row->id) }}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                @else
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('journal-vouchers.show', $row->id) }}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @php
                            $TotalDebit += $row->debit;
                            $TotalCredit += $row->credit;
                        @endphp
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center">{{ translate('No vouchers found') }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><strong>{{ translate('Total') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($TotalDebit, 2, '.', ',') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($TotalCredit, 2, '.', ',') }}</strong></td>
                        <td colspan="2"></td>
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
        printWindow.document.write('<html><head><title>Day Book Voucher</title>');
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
        var head_code = document.getElementById('head_code').value;
        var party_id = document.getElementById('party_id').value;
        var dtpFromDate = document.querySelector('input[name="dtpFromDate"]').value;
        var dtpToDate = document.querySelector('input[name="dtpToDate"]').value;

        // Build URL with parameters
        var url = '{{ route("day-book-report.index") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&head_code=' + encodeURIComponent(head_code);
        url += '&party_id=' + encodeURIComponent(party_id);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection
