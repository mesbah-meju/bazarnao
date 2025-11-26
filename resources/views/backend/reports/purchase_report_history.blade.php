@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Month & Year wise Purchase Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('purchase_report_history.index') }}" method="get">
                    <div class="form-group row">
                        <div class="form-group mb-0">
                            <label>Date Range :</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                            <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">{{ translate('Sort by Product') }} :</label>
                            <select id="demo-ease" class="aiz-selectpicker select2" name="product_id[]" data-live-search="true" multiple>
                                <option value=''>All</option>
                                @foreach (DB::table('products')->orderBy('name')->select('id', 'name')->get() as $key => $prod)
                                <option @if(in_array($prod->id, request()->input('product_id', []))) selected @endif
                                    value="{{ $prod->id }}">{{ $prod->name }}</option>
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
                    th{text-align:center;}
                </style>

                @foreach($productsData as $productData)
                    <div class="container mt-4">
                        <h5>Month & Year wise Purchase Report</h2>
                        <h6>Product's Name: <span>{{ $productData['product_name'] }}</span></h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2">Month</th>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <th colspan="3">{{ $year }}</th>
                                    @endfor
                                </tr>
                                <tr>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <th>Qty</th>
                                        <th>Average Purchase</th>
                                        <th>Amount</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productData['months'] as $monthData)
                                <tr>
                                    <td>{{ $monthData['name'] }}</td>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <td>{{ $monthData[$year]['qty'] }}</td>
                                        <td>{{ number_format($monthData[$year]['average_sale'], 2, '.', ',') }}</td>
                                        <td>{{ $monthData[$year]['amount'] }}</td>
                                    @endfor
                                </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align:right;" colspan="1"><b>Total</b></td>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <td style="text-align:right;"><b>{{ $productData['totals'][$year]['qty'] }}</b></td>
                                        <td style="text-align:right;"><b>-</b></td>
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
    a.document.write('<body >');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}
</script>
