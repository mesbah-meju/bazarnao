<!DOCTYPE html>
<html>
<head>
    <title>Cash Transfer Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .filter-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        @media print {
            @page {
                size: A4 landscape;
                margin: 0.5cm;
            }
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px">
        <h2>Cash Transfer Report</h2>
        <div class="filter-info">
            @if($from_warehouse_name)
                <strong>From Warehouse:</strong> {{ $from_warehouse_name }}
            @endif
            @if($to_warehouse_name)
                @if($from_warehouse_name) | @endif
                <strong>To Warehouse:</strong> {{ $to_warehouse_name }}
            @endif
            @if($from_date || $to_date)
                <br>
                <strong>Date Range:</strong> 
                @if($from_date) {{ date('d-m-Y', strtotime($from_date)) }} @else Start @endif
                to 
                @if($to_date) {{ date('d-m-Y', strtotime($to_date)) }} @else End @endif
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Voucher No</th>
                <th>Voucher Date</th>
                <th>From Warehouse</th>
                <th>To Warehouse</th>
                <th>Remark</th>
                <th class="text-right">Amount</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach ($transfers as $key => $transfer)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $transfer->voucher_no }}</td>
                    <td>{{ date('d-m-Y', strtotime($transfer->voucher_date)) }}</td>
                    <td>{{ getWearhouseName($transfer->from_warehouse_id) }}</td>
                    <td>{{ getWearhouseName($transfer->to_warehouse_id) }}</td>
                    <td>{{ $transfer->remarks }}</td>
                    <td class="text-right">{{ number_format($transfer->amount, 2) }}</td>
                    <td class="text-center">{{ $transfer->status == '0' ? 'Pending' : 'Approved' }}</td>
                </tr>
                @php $totalAmount += $transfer->amount; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Amount:</td>
                <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

