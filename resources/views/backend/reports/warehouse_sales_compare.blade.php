@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Warehouse Sales Compare Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('warehouse_sales_compare.index') }}" method="get">
                    <div class="form-group row">
                        {{-- <div class="form-group mb-0">
                            <label>Date Range :</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                            <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                        </div> --}}

                        <div class="col-md-3">
                            <label>Filter By Warehouse :</label>
                            <select class="aiz-selectpicker select2" name="warehouse[]" id="warehouse"  onchange="this.form.submit()"  multiple>
                                {{-- <option value="">All</option> --}}
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
                                <a href="{{ route('warehouse_sales_compare.index', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-sm btn-success">{{ translate('Excel') }}</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th { text-align: center; }
                    </style>
                    <h5>Warehouse Sales Compare Report</h5>
                    @foreach($productsData as $productData)
                        <div class="container mt-4">
                            
                            <h6>Warehouse: <span>{{ $productData['warehouse_name'] }}</span></h6>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Month</th>
                                        @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                            <th colspan="1">{{ $year }}</th>
                                        @endfor
                                    </tr>
                                    {{-- <tr>
                                        @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                            <th>Qty</th>
                                            <th>Average Sale</th>
                                            <th>Amount</th>
                                        @endfor
                                    </tr> --}}
                                </thead>
                                <tbody>
                                    @foreach($productData['months'] as $monthData)
                                        <tr>
                                            <td>{{ $monthData['name'] }}</td>
                                            @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                                {{-- <td>{{ $monthData[$year]['qty'] }}</td>
                                                <td>{{ number_format($monthData[$year]['average_sale'], 2, '.', ',') }}</td> --}}
                                                <td class="text-right">{{ single_price($monthData[$year]['amount']) }}</td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td style="text-align:right;" colspan="1"><b>Total</b></td>
                                        @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                            {{-- <td style="text-align:right;"><b>{{ $productData['totals'][$year]['qty'] }}</b></td>
                                            <td style="text-align:right;"><b>-</b></td> --}}
                                            <td style="text-align:right;"><b>{{ single_price($productData['totals'][$year]['amount']) }}</b></td>
                                        @endfor
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
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
