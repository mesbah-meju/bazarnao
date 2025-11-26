@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Warehouse Monthly Sales Report') }}</h1>
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
                    <div class="align-items-center">
                        <h1 class="h3">{{ translate('Warehouse Monthly Sales Report') }}</h1>
                        <h5>Year: {{ $year }}</h5>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                @foreach ($warehouses as $warehouse)
                                    <th>{{ $warehouse->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($months as $month => $monthData)
                                <tr>
                                    <td>{{ DateTime::createFromFormat('!m', $month)->format('F') }}</td>
                                    @foreach ($warehouses as $warehouse)
                                        <td>{{ single_price($monthData[$warehouse->id]) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;"><b>Total</b></td>
                                @foreach ($warehouses as $warehouse)
                                    @php
                                        $warehouseTotal = 0;
                                        foreach ($months as $monthData) {
                                            $warehouseTotal += $monthData[$warehouse->id];
                                        }
                                    @endphp
                                    <td style="text-align:right;"><b>{{ single_price($warehouseTotal) }}</b></td>
                                @endforeach
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
