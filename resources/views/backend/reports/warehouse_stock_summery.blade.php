@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Warehouse Stock Summary Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('warehouse_stock_summery.index') }}" method="get">
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
                    <h3 class="h3">{{ translate('Warehouse Stock Summary Report') }}</h3>

                    @foreach($yearData as $year => $data)
                    <a href="{{ route('monthly_warehouse_stock_summery.index', ['year' => $year, 'warehouse' => implode(',', $warehouseIds)]) }}" 
                        target="_blank" 
                        class="btn btn-sm btn-success">
                        {{ $year }}
                     </a>
                     
                     
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Warehouse</th>
                                    <th>Opening Stock Value</th>
                                    <th>Purchase Value</th>
                                    <th>Transfer IN Value</th>
                                    <th>Sales Value</th>
                                    <th>Transfer Out Value</th>
                                    <th>Closing Stock Value</th>
                                    <th>Profit/Loss</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['warehouses'] as $warehouseData)
                                    <tr>
                                        <td>{{ $warehouseData['name'] }}</td>
                                        <td>{{ single_price($warehouseData['data']['opening_stock']) }}</td>
                                        <td>{{ single_price($warehouseData['data']['purchase']) }}</td>
                                        <td>{{ single_price($warehouseData['data']['transfer_in']) }}</td>
                                        <td>{{ single_price($warehouseData['data']['sales']) }}</td>
                                        <td>{{ single_price($warehouseData['data']['transfer_out']) }}</td>
                                        <td>{{ single_price($warehouseData['data']['closing_stock']) }}</td>
                                        <td>{{ single_price($warehouseData['data']['profit_loss']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>Total</b></td>
                                    <td><b>{{ single_price($data['totals']['opening_stock']) }}</b></td>
                                    <td><b>{{ single_price($data['totals']['purchase']) }}</b></td>
                                    <td><b>{{ single_price($data['totals']['transfer_in']) }}</b></td>
                                    <td><b>{{ single_price($data['totals']['sales']) }}</b></td>
                                    <td><b>{{ single_price($data['totals']['transfer_out']) }}</b></td>
                                    <td><b>{{ single_price($data['totals']['closing_stock']) }}</b></td>
                                    <td><b>{{ single_price($data['totals']['profit_loss']) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
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
