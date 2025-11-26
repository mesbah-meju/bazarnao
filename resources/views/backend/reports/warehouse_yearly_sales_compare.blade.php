@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Warehouse Yearly Sales Compare Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('warehouse_yearly_sales_compare.index') }}" method="get">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label>Filter By Warehouse :</label>
                            <select class="aiz-selectpicker select2" name="warehouse[]" id="warehouse" onchange="this.form.submit()" multiple>
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
                                <a href="{{ route('warehouse_yearly_sales_compare.index', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-sm btn-success">{{ translate('Excel') }}</a>

                                
                                
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th, td { text-align: center; }
                    </style>
                    <div class="align-items-center">
                        <h1 class="h3">{{ translate('Warehouse Yearly Sales Compare Report') }}</h1>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Warehouse</th>
                                @for ($year = $currentYear; $year >= $currentYear-2; $year--)
                                    <th>
                                        <a href="{{ route('warehouse_monthly_sales_report', ['warehouse' => $warehouseIds, 'year' => $year]) }}" target="_blank">
                                            {{ $year }}
                                        </a>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productsData as $productData)
                                <tr>
                                    <td>{{ $productData['warehouse_name'] }}</td>
                                    @for ($year = $currentYear; $year >= $currentYear-2; $year--)
                                        <td>{{ single_price($productData['totals'][$year]) }}</td>
                                    @endfor
                                </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;" colspan="1"><b>Total</b></td>
                                @for ($year = $currentYear; $year >= $currentYear-2; $year--)
                                    @php
                                        $yearTotal = 0;
                                        foreach($productsData as $productData) {
                                            if (isset($productData['totals'][$year])) {
                                                $yearTotal += $productData['totals'][$year];
                                            }
                                        }
                                    @endphp
                                    <td style="text-align:right;"><b>{{ single_price($yearTotal) }}</b></td>
                                @endfor
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
