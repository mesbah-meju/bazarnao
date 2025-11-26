@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form id="sort_debit_vouchers" action="{{ route('general-ledger.report') }}" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('General Ledger') }}</h5>
            </div>
        </div>

        <?php
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
        ?>
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
                    <div class="form-group row">
                        <label for="cmbCode" class="form-label">{{ translate('Transaction Head') }} <span class="text-danger">*</span></label>
                        <select name="cmbCode" class="form-control aiz-selectpicker" data-live-search="true" id="cmbCode" required>
                            <option value="">{{ translate('Select Option') }}</option>
                            @foreach($general_ledger as $gl_ledger)
                            <option value="{{ $gl_ledger->head_code }}" {{ (isset($cmbCode) && $cmbCode == $gl_ledger->head_code) ? 'selected' : '' }}>{{ $gl_ledger->head_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="party_name">{{ translate('Party Name') }}</label>
                        <select name="party_name" id="party_name" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('All Parties') }}</option>
                            @if(isset($party_names))
                                @foreach($party_names as $pname)
                                <option value="{{ $pname }}" {{ (isset($party_name) && $party_name == $pname) ? 'selected' : '' }}>{{ $pname }}</option>
                                @endforeach
                            @endif
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
        <div class="row pb-3 align-items-center">
            <table border="0" width="100%">
                <caption class="text-center">
                    <table class="print-font-size" width="100%">
                        <tr>
                            <td align="left" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <img src="{{ asset('public/assets/img/logo.png') }}" class="img-bottom-m print-logo" alt="logo"><br>
                            </td>
                            <td align="center" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <h4><strong>BAZAR NAO LTD.</strong></h4>
                                Sukhnir, Flat: B2, House: 33, Road: 1/A<br>Block: J, Baridhara, Dhaka-1212<br>
                                info@bazarnao.com<br>
                                +880 1969 906 699<br>
                            </td>
                            <td align="right" style="border-bottom: 2px #333 solid;" width="33.333%">
                                <b>
                                    <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                                </b>
                                <br>
                                <b>
                                    <label class="font-weight-600 mb-0">{{ translate('Opening Balance') }}</label> : {{ number_format($prebalance,2,'.',',');}}
                                </b>
                                <br>
                                @php
                                    $CurBalance = $prebalance;
                                @endphp
            
                                @foreach($HeadName2 as $key => $data2)
                                    @php 
                                        if($HeadName->head_type == 'A' || $HeadName->head_type == 'E') {
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
                                    @endphp
                                @endforeach
                                <b>
                                    <label class="font-weight-600 mb-0">{{ translate('Closing Balance') }}</label> : {{number_format($CurBalance,2,'.',',');}}
                                </b>
                            </td>
                        </tr>
                    </table>
                </caption>
                <caption class="text-center">
                    <strong><u class="pt-4">{{ translate('General Ledger of') . ' ' . $ledger->head_name . ' on ' . date('d-m-Y', strtotime($dtpFromDate)) . ' To ' . date('d-m-Y', strtotime($dtpToDate)) }}</u></strong>
                    <br>
                    <p class="mt-2"><strong>{{ translate('Warehouse') }}:</strong> {{ $warehouseName }}</p>
                </caption>
            </table>
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
                        $TotalDebit = 0;
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
                    @php
                        // Fetch purchase details if this is a supplier payable transaction
                        $purchase = null;
                        $supplierName = 'N/A';
                        $purchaseNo = '';
                        
                        // Initialize order variables
                        $order = null;
                        $customerName = 'N/A';
                        $orderNo = '';
                        
                        if(($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204) && $data->reference_no) {
                            $purchase = \App\Models\Purchase::find($data->reference_no);
                            if($purchase) {
                                $supplier_id = $purchase->supplier_id;
                                $supplierName = \App\Models\Supplier::find($supplier_id)->name;
                                $purchaseNo = $purchase->purchase_no ?? '';
                            }
                        }
                        
                        if(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $data->reference_no) {
                            $order = \App\Models\Order::find($data->reference_no);
                            if($order) {
                                $customer_id = $order->user_id;
                                $customerName = \App\Models\User::find($customer_id)->name;
                                $orderNo = $order->code ?? '';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ ++$key + $openid }}</td>
                        <td>{{ date('d-m-Y', strtotime($data->voucher_date)) }}</td>
                        <td>{{ $data->rev_coa->head_name }}</td>
                        <td>
                            @if($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204)
                                {{ $supplierName }}
                            @elseif(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $orderNo)
                                {{ $customerName }}
                            @elseif($data->relvalue && $data->reltype)
                                {{ $data->relvalue->name }}({{ $data->reltype->name }})
                            @else
                                {{ translate('N/A') }}
                            @endif
                        </td>
                        <td>
                            @if(($data->rev_coa->head_code == 5020201 || $data->rev_coa->head_code == 10204) && $purchaseNo)
                                {{ $data->ledger_comment }} for Purchase No: <a href="{{ route('purchase_orders_view', $purchase->id) }}" target="_blank" class="text-primary font-weight-bold">{{ $purchaseNo }}</a>
                            @elseif(($data->rev_coa->head_code == 3010301 || $data->rev_coa->head_code == 1020801 || $data->rev_coa->head_code == 40101 || $data->rev_coa->head_code == 4010101 || $data->rev_coa->head_code == 1020401 || $data->rev_coa->head_code == 1020802) && $orderNo)
                                {{ $data->ledger_comment }} for Order No: <a href="{{ route('all_orders.show', encrypt($order->id)) }}" target="_blank" class="text-primary font-weight-bold">{{ $orderNo }}</a> <br>
                            @else
                                {{ $data->ledger_comment }}
                            @endif
                        </td>
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
                        <td>{{ $data->voucher_no }}</td>
                        <td class="text-right">{{ number_format($data->debit, 2, '.', ',') }}</td>
                        <td class="text-right">{{ number_format($data->credit, 2, '.', ',') }}</td>
                        @php 
                            $TotalDebit += $data->debit;
                            $TotalCredit += $data->credit;

                            if($HeadName->head_type == 'A' || $HeadName->head_type == 'E') {
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
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>General Ledger Report</title>');
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
        var cmbCode = document.getElementById('cmbCode').value;
        var party_name = document.getElementById('party_name').value;
        var dtpFromDate = document.querySelector('input[name="dtpFromDate"]').value;
        var dtpToDate = document.querySelector('input[name="dtpToDate"]').value;

        // Build URL with parameters
        var url = '{{ route("general-ledger.report") }}?type=excel';
        url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        url += '&cmbCode=' + encodeURIComponent(cmbCode);
        url += '&party_name=' + encodeURIComponent(party_name);
        url += '&dtpFromDate=' + encodeURIComponent(dtpFromDate);
        url += '&dtpToDate=' + encodeURIComponent(dtpToDate);

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection
