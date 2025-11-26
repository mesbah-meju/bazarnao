@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate(' Monthly Sales Summery Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>

                <div class="printArea">
                    <style>
                        th, td { text-align: center; }
                    </style>

                    <h5>Year: {{ $year }}</h5>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Customer</th>
                                <th>Total Order</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotalCustomers = 0;
                                $grandTotalOrders = 0;
                                $grandTotalSales = 0;
                            @endphp
                            @foreach ($months as $month => $data)
                                @php
                                    $grandTotalCustomers += $data['total_customers'];
                                    $grandTotalOrders += $data['total_orders'];
                                    $grandTotalSales += $data['total_sales'];
                                @endphp
                                <tr>
                                    <td>{{ DateTime::createFromFormat('!m', $month)->format('F') }}</td>
                                    <td>{{ $data['total_customers'] }}</td>
                                    <td>{{ $data['total_orders'] }}</td>
                                    <td>{{ number_format($data['total_sales'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;"><b>Total:</b></td>
                                <td><b>{{ $grandTotalCustomers }}</b></td>
                                <td><b>{{ $grandTotalOrders }}</b></td>
                                <td><b>{{ number_format($grandTotalSales, 2) }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

<script>
function printDiv() {
    var divContents = document.querySelector(".printArea").innerHTML;
    var a = window.open('', '', 'height=500, width=500');
    a.document.write('<html>');
    a.document.write('<body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}
</script>
