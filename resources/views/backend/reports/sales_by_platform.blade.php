@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Sales by Platform Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('sales_by_platform.index') }}" method="get">
                    <!-- Filter Section -->
                    <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ translate('Warehouse') }}</label>
                                <select class="aiz-selectpicker select2 form-control" name="warehouse[]" id="warehouse" multiple data-live-search="true">
                                    @foreach(\App\Models\Warehouse::all() as $warehouse)
                                        <option value="{{ $warehouse->id }}" @if(in_array($warehouse->id, (array)$warehouseIds)) selected @endif>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ translate('Start Date') }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}">
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ translate('End Date') }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}">
                            </div>

                            <div class="col-lg-2 col-md-12 d-flex align-items-end mb-3">
                                <div class="d-flex flex-column gap-2 w-100">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="las la-filter"></i> {{ translate('Filter') }}
                                    </button>
                                    <button class="btn btn-info" onclick="printDiv()" type="button">
                                        <i class="las la-print"></i> {{ translate('Print') }}
                                    </button>
                                    <a href="{{ route('sales_by_platform.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-success">
                                        <i class="las la-file-excel"></i> {{ translate('Excel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th {
                            text-align: center;
                        }

                        table {
                            width: 100%;
                            font-size: 12px;
                        }
                    </style>
                    <h3 style="text-align:center;">{{translate('Sales By Platform Report')}}</h3>
                    <table class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Order Qty</th>
                                <th>Customer Qty</th>
                                <th>Sales Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($platformData as $platform => $data)
                            <tr>
                                <td style="text-align: right;">{{ $platform }}</td>
                                <td style="text-align: right;">{{ $data['total_orders'] }}</td>
                                <td style="text-align: right;">{{ $data['total_customers'] }}</td>
                                <td style="text-align: right;">{{ single_price($data['total_order_price']) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="text-align:right;" colspan="3"><b>Total:</b></td>
                                <td style="text-align:right;"><b>
                                        @php
                                        $grandTotal = 0;
                                        foreach ($platformData as $data) {
                                        $grandTotal += $data['total_order_price'];
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