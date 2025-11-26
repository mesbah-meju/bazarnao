@extends('backend.layouts.app')
@section('content')
    <div class="card">
        <form id="culexpo" action="{{ route('transfer_list_details.index') }}" method="GET">
            <div class="card-header row gutters-5">
                <div class="col-lg-4">
                    <div class="form-group mb-0">
                        <label>Date Range:</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                            <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Filter By From Warehouse:</label>
                        <select class="form-control" name="from_warehouse_id" id="warehouse">
                            <option value="">Select One</option>
                            @foreach (\App\Models\Warehouse::all() as $warehouse)
                                <option value="{{ $warehouse->id }}" @if ($from_warehouse_id == $warehouse->id) selected @endif>
                                    {{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Filter By To Warehouse:</label>
                        <select class="form-control" name="to_warehouse_id" id="to_warehouse">
                            <option value="">Select One</option>
                            @foreach (\App\Models\Warehouse::all() as $warehouse)
                                <option value="{{ $warehouse->id }}" @if ($to_warehouse_id == $warehouse->id) selected @endif>
                                    {{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Filter By Product:</label>
                        <select class="form-control aiz-selectpicker select2" name="product_id[]" id="product_id" data-live-search="true" multiple>
                            <option value="">Select One</option>
                            @foreach(\App\Models\Product::all() as $product)
                                <option value="{{ $product->id }}" @if(in_array($product->id, $product_ids ?? [])) selected @endif>{{ $product->name }}</option>
                            @endforeach                      
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0 mt-2">
                        <button class="btn btn-sm btn-primary"
                            type="submit">{{ translate('Filter') }}</button>
                        <button class="btn btn-sm btn-info" type="button"
                            onclick="printDiv()">{{ translate('Print') }}</button>
                            {{-- <a href="{{ route('transfer_list_details.index',[
                                'type' => 'excel',
                                'start_date' => request()->input('start_date'),
                                'end_date' => request()->input('end_date'),
                                'warehouse' => request()->input('warehouse'),
                                'to_warehouse' => request()->input('to_warehouse'),
                                'product_id' => request()->input('product_id'),
                                'search' => request()->input('search'),
                            ]) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a> --}}

                            <a href="{{ route('transfer_list_details.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>

                    </div>
                </div>
            </div>
        </form>

        <div class="card-body">
            <style>
                th {
                    text-align: center;
                }
            </style>
            <h3 style="text-align:center;">{{ translate('Transfer Details List Report') }} </h3>
            <table class="table aiz-table mb-0 printArea">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-breakpoints="md">{{ translate('Product') }}</th>
                        <th data-breakpoints="md">{{ translate('From Warehouse') }}</th>
                        <th data-breakpoints="md">{{ translate('To Warehouse') }}</th>
                        <th data-breakpoints="md">{{ translate('Qty') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Approved Date') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $totalQty = 0;
                    @endphp
                    @foreach ($transfers as $key => $order)
                        @php
                            $total += $order->total_amount;
                            $totalQty += $order->total_qty;
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $order->product->name }}</td>
                            <td>{{ getWearhouseName($order->from_wearhouse_id) }}</td>
                            <td>{{ getWearhouseName($order->to_wearhouse_id) }}</td>
                            <td>{{ $order->total_qty }}</td>
                            <td>{{ $order->total_amount ?? '-' }}</td>
                            <td>{{ $order->date }}</td>
                            <td>{{ $order->status }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" style="text-align:right;"><b>Total</b></td>
                        <td style="text-align:right;"><b>{{ $totalQty }}</b></td>
                        <td style="text-align:right;"><b>{{ single_price($total) }}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript"></script>
@endsection
