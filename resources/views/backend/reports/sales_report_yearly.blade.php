@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Sales summery report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('sales_report.index') }}" method="get">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label>Filter By Warehouse :</label>
                            <select class="aiz-selectpicker select2" name="warehouse[]" id="warehouse" multiple>
                                @foreach(\App\Models\Warehouse::all() as $warehouse)
                                    <option value="{{ $warehouse->id }}" @if(in_array($warehouse->id, (array)$warehouseIds)) selected @endif>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th, td { text-align: center; }
                    </style>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Total Customer</th>
                                <th>Total Order</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($year = $currentYear; $year >= $currentYear - 3; $year--)
                                <tr>
                                    <td><a href="{{ route('sales_report_monthly.index', ['warehouse_id' => $warehouse->id, 'year' => $year]) }}" target="_blank">{{ $year }}</a></td>
                                    <td>
                                        @php
                                            $totalCustomer = 0;
                                            foreach ($productsData as $productData) {
                                                if (isset($productData['total_customers'][$year])) {
                                                    $totalCustomer += $productData['total_customers'][$year];
                                                }
                                            }
                                        @endphp
                                        {{ $totalCustomer }}
                                    </td>
                                    <td>
                                        @php
                                            $totalOrder = 0;
                                            foreach ($productsData as $productData) {
                                                if (isset($productData['total_orders'][$year])) {
                                                    $totalOrder += $productData['total_orders'][$year];
                                                }
                                            }
                                        @endphp
                                        {{ $totalOrder }}
                                    </td>
                                    <td>
                                        @php
                                            $yearSales = 0;
                                            foreach ($productsData as $productData) {
                                                if (isset($productData['totals'][$year])) {
                                                    $yearSales += $productData['totals'][$year];
                                                }
                                            }
                                        @endphp
                                        {{ single_price($yearSales) }}
                                    </td>
                                </tr>
                            @endfor
                            <tr>
                                <td style="text-align:right;" colspan="3"><b>Total:</b></td>
                                <td style="text-align:right;"><b>
                                    @php
                                        $grandTotal = 0;
                                        for ($year = $currentYear; $year >= $currentYear - 3; $year--) {
                                            foreach ($productsData as $productData) {
                                                if (isset($productData['totals'][$year])) {
                                                    $grandTotal += $productData['totals'][$year];
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ single_price($grandTotal) }}
                                </b></td>
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
