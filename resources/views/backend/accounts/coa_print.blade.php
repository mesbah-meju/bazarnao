@extends('backend.layouts.app')

@section('content')

<style>
    /* Custom styling for the print area */
    .printArea {
        font-family: Arial, sans-serif;
    }
    .voucher-center {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #000;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    h2 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    .print-btn {
        margin-bottom: 15px;
    }
    /* Table styling */
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }
    table th, table td {
        padding: 8px 12px;
        text-align: left;
        border: 1px solid #ddd;
    }
    table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>

<div class="container">
    <button class="btn btn-info btn-md print-btn mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
    <button class="btn btn-primary btn-md print-btn" onclick="downloadExcel()" type="button">{{ translate('Excel') }}</button>

    <div class="card-body printArea bg-white">
        <div class="row voucher-center">
            <div class="col-md-3 text-left">
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="50px">
            </div>
            <div class="col-md-6 text-center">
                <h2>Bazarnao</h2>
                <strong><u>{{ translate('COA Print') }}</u></strong>
            </div>
            <div class="col-md-3 text-right">
                <div>
                    <b>
                        <label>{{ translate('Date') }}:</label> {{ date('d/m/Y') }}
                    </b>
                </div>
            </div>
        </div><hr>

        <table cellpadding="3" cellspacing="0" border="1" width="100%">
            @foreach ($coaData as $row)
                @php
                    $headLevel = $row->head_level;
                    $levelDiff = $maxLevel + 1 - $headLevel;
                @endphp
        
                <tr>
                    <!-- Indentation based on HeadLevel -->
                    @for ($j = 0; $j < $headLevel; $j++)
                        <td>&nbsp;</td>
                    @endfor
        
                    <!-- Display HeadCode and HeadName -->
                    <td>{{ $row->head_code }}</td>
                    <td colspan="{{ $levelDiff }}">{{ $row->head_name }}</td>
                </tr>
            @endforeach
        </table>
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
        
        .print-btn {
            display: none !important;
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
        .aiz-topbar {
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
    }
</style>

<script>
    function printDiv() {
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Chart of Accounts</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('* { margin: 0; padding: 0; box-sizing: border-box; }');
        printWindow.document.write('html, body { height: auto; overflow: visible; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; padding: 10px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 20px 0; }');
        printWindow.document.write('th, td { border: 1px solid #000; padding: 8px 12px; font-size: 12px; }');
        printWindow.document.write('th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.text-right { text-align: right; }');
        printWindow.document.write('.text-center { text-align: center; }');
        printWindow.document.write('img { max-height: 50px; }');
        printWindow.document.write('h2 { font-size: 24px; font-weight: bold; margin: 5px 0; }');
        printWindow.document.write('hr { border: 1px solid #000; margin: 15px 0; }');
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
        // Build URL with parameters
        var url = '{{ route("account.coa_print") }}?type=excel';

        // Redirect to download
        window.location.href = url;
    }
</script>

@endsection
