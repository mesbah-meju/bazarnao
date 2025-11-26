@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Sub Ledger') }}</h1>
        </div>
    </div>
</div>
<?php
        $warehousearray = getWearhouseBuUserId(auth()->user()->id);
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
?>

<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('sub-ledger.report') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Sub Ledger') }}</h5>
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
                        <label class="form-label" for="dtpFromDate">{{ translate('From Date') }}</label>
                        <input type="text" name="dtpFromDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpFromDate)) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="dtpToDate">{{ translate('To Date') }}</label>
                        <input type="text" name="dtpToDate" class="form-control datepicker" value="{{ date('m/d/Y', strtotime($dtpToDate)) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label"  for="subtype">{{ translate('Sub Type') }}</label>
                        <select name="subtype" id="subtype" class="form-control aiz-selectpicker" onchange="showAccountSubhead(this.value);" required>
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($subtypes as $st)
                            <option value="{{ $st->id }}" {{ (isset($subtype) && $subtype == $st->id) ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="accounthead">{{ translate('Account Head') }}</label>
                        <select name="accounthead[]" id="accounthead" class="form-control aiz-selectpicker" multiple data-live-search="true" onchange="showTransationSubheadFromHeads();">
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($acchead as $ac)
                            @php
                                $selectedHeads = is_array($accounthead) ? $accounthead : (is_string($accounthead) && strpos($accounthead, ',') !== false ? array_map('trim', explode(',', $accounthead)) : [$accounthead]);
                            @endphp
                            <option value="{{ $ac->head_code }}" {{ (isset($selectedHeads) && in_array($ac->head_code, $selectedHeads ?? [])) ? 'selected' : '' }}>{{ $ac->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="subcode">{{ translate('Transaction Head') }}</label>
                        <select name="subcode" id="subcode" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($subcodes as $sc)
                            <option value="{{ $sc->id }}" {{ (isset($subcode) && $subcode == $sc->id) ? 'selected' : '' }}>{{ $sc->name }}</option>
                            @endforeach
                        </select>
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
                    <br>
                    <b><label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : {{ number_format($prebalance,2,'.',',');}}</b>
                    <br>
                    @php
                        $CurBalance = $prebalance;
                        foreach($HeadName2 as $key => $data2) {
                            if($ledger->head_type == 'A' || $ledger->head_type == 'E') {
                                if($data2->debit > 0) {
                                    $CurBalance += $data2->debit;
                                }
                                if($data2->credit > 0) {
                                    $CurBalance -= $data2->credit;
                                }                          
                            } else {                       
                                if($data2->debit > 0) {
                                    $CurBalance -= $data2->debit;
                                }                          
                                if($data2->credit > 0) {
                                    $CurBalance += $data2->credit;
                                }
                            }
                        }
                    @endphp
                    <b><label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : {{number_format($CurBalance,2,'.',',');}}</b>
                </div>
            </div>
        </div>

        <div class="row pb-3 voucher-center align-items-center">
            @if ($subLedger)
                <div class="col-md-12 text-center">
                    <strong>
                        <u class="pt-4">
                            {{ translate('Sub Ledger of') . ' ' . $ledger->head_name . ' (' . $subLedger->name . ') on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To ' . date('d-m-Y', strtotime($dtpToDate)) }}
                        </u>
                    </strong>
                    <br>
                    <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
                </div>
            @else
                <div class="col-md-12 text-center text-danger">
                    <strong>{{ __('Sub Ledger not found.') }}</strong>
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="datatable table table-striped table-hover" cellpadding="6" cellspacing="1">
                <thead>
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Voucher No') }}</th>
                        <th>{{ translate('Voucher Type') }}</th>
                        <th>{{ translate('Account Name') }}</th>
                        <th>{{ translate('Ledger Comment') }}</th>
                        <th>{{ translate('Debit') }}</th>
                        <th>{{ translate('Credit') }}</th>
                        <th>{{ translate('Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $TotalCredit = 0;
                        $TotalDebit = 0;
                        $CurBalance = $prebalance;
                        $openid = 1;
                    ?>
                    <tr>
                        <td>{{ $openid }}</td>
                        <td>{{ date('d-m-Y', strtotime($dtpFromDate)) }}</td>
                        <td colspan="4" class="text-right"><strong>{{ translate('Opening Balance') }}</strong></td>
                        <td class="text-right">{{ number_format(0, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format(0, 2, '.', ',') }}</td>
                        <td class="text-right"><strong>{{ number_format($prebalance, 2, '.', ',') }}</strong></td>
                    </tr>
                    @foreach($HeadName2 as $key => $data)
                    <tr>
                        <td>{{ ++$key + $openid }}</td>
                        <td>{{ date('d-m-Y', strtotime($data->voucher_date)) }}</td>
                        <td>{{ $data->voucher_no }}</td>
                        <td>
                            @if($data->voucher_type=='DV')
                                {{ translate('Debit') }}
                            @elseif($data->voucher_type=='CV')
                                {{ translate('Credit') }}
                            @elseif ($data->voucher_type=='JV')
                                {{ translate('Journal') }}
                            @else
                                {{ translate('Contra') }}
                            @endif
                        </td>
                        <td>{{ $data->coa->head_name }}</td>
                        <td>{{ $data->ledger_comment }}</td>
                        <td class="text-right">{{ number_format($data->debit, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format($data->credit, 2, '.', ',') }}</td>
                        @php 
                            $TotalDebit += $data->debit;
                            $TotalCredit += $data->credit;
                            if($ledger->head_type == 'A' || $ledger->head_type == 'E') {
                                if($data->debit > 0) {
                                    $CurBalance += $data->debit;
                                }
                                if($data->credit > 0) {
                                    $CurBalance -= $data->credit;
                                }                          
                            } else {                       
                                if($data->debit > 0) {
                                    $CurBalance -= $data->debit;
                                }                          
                                if($data->credit > 0) {
                                    $CurBalance += $data->credit;
                                }
                            }
                        @endphp
                        <td class="text-right">{{ number_format($CurBalance, 2, '.', ',') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><strong>{{ translate('Total') }}</strong></td>
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
            size: A4 landscape;
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
    function makeAccountHeadReadonly() {
        var $form = $('#sort_debit_vouchers');
        $form.find('input.accounthead-hidden').remove();
        var selectedHeads = $('#accounthead').val() || [];
        selectedHeads.forEach(function(v){
            $('<input/>', { type: 'hidden', name: 'accounthead[]', value: v, class: 'accounthead-hidden' }).appendTo($form);
        });
        $('#accounthead').prop('disabled', true).selectpicker('refresh');
        $('#accounthead').closest('.bootstrap-select').css('pointer-events', 'none').find('button').addClass('disabled');
    }
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Sub Ledger Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 0; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 5px; font-size: 11px; }');
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
        var subtype = document.getElementById('subtype').value;
        var subcode = document.getElementById('subcode').value;
        var accounthead = $('#accounthead').val() || [];
        var dtpFromDate = document.querySelector('input[name="dtpFromDate"]').value;
        var dtpToDate = document.querySelector('input[name="dtpToDate"]').value;

        // Build URL with parameters
        var url = '{{ route("sub-ledger.report") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&subtype=' + encodeURIComponent(subtype);
        url += '&subcode=' + encodeURIComponent(subcode);
        
        // Add multiple account heads
        accounthead.forEach(function(head) {
            url += '&accounthead[]=' + encodeURIComponent(head);
        });
        
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>

<script type="text/javascript">
    function showTransationSubheadFromHeads() {
        $('#subcode').html('');
        var selectedHeads = $('#accounthead').val() || [];
        var codes = selectedHeads.join(',');
        if (!codes) { return; }
        $.ajax({
            url: "{{ route('sub-ledger.subcode-by-head', '') }}/" + codes,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data != '') {
                    $('#subcode').html(data).selectpicker('refresh');
                }
            },
            error: function() {
                alert('Error fetching transaction heads');
            }
        });
    }

    function showAccountSubhead(id) {
        $('#accounthead').html('');
        $.ajax({
            url: "{{ route('sub-ledger.accounthead', '') }}/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data != '') {
                    $('#accounthead').html(data);
                    $('#accounthead option').prop('selected', true);
                    $('#accounthead').selectpicker('refresh');
                    showTransationSubheadFromHeads();
                    makeAccountHeadReadonly();
                }
            },
            error: function() {
                alert('Error fetching account heads');
            }
        });
    }

    $(document).ready(function(){
        if ($('#accounthead option').length > 0) {
            if (!(($('#accounthead').val() || []).length)) {
                $('#accounthead option').prop('selected', true);
                $('#accounthead').selectpicker('refresh');
            }
            makeAccountHeadReadonly();
        }
    });
</script>

@endsection
