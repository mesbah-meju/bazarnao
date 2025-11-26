@extends('backend.layouts.app')
@section('content')
<div class="card">
    <form id="culexpo" action="{{ route('product_transfer_summery.index') }}" method="GET">
        <div class="card-header row gutters-5">
            <!-- Date Range Filter -->
            <div class="col-lg-4">
                <div class="form-group mb-0">
                    <label>Date Range:</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                    </div>
                </div>
            </div>
            <!-- From Warehouse Filter -->
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <label>Filter By From Warehouse:</label>
                    <select class="form-control" name="warehouse" id="warehouse">
                        <option value="">Select One</option>
                        @foreach(\App\Models\Warehouse::all() as $warehouse)
                            <option value="{{ $warehouse->id }}" @if($wearhouse == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                        @endforeach                      
                    </select>
                </div>
            </div>
            <!-- To Warehouse Filter -->
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <label>Filter By To Warehouse:</label>
                    <select class="form-control" name="to_warehouse" id="to_warehouse">
                        <option value="">Select One</option>
                        @foreach(\App\Models\Warehouse::all() as $warehouse)
                            <option value="{{ $warehouse->id }}" @if($to_wearhouse == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                        @endforeach                      
                    </select>
                </div>
            </div>
            <!-- Product Filter -->
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
            <!-- Filter and Print Buttons -->
            <div class="col-auto">
                <div class="form-group mb-0 mt-2">
                    <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>
                    <a href="{{ route('product_transfer_summery.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Report Table -->
    <div class="card-body printArea">
        <style>
            th {
                text-align: center;
            }
        </style>
        <h3 style="text-align:center;">{{translate('Product Transfer Summery Report')}}</h3>
        <table class="table aiz-table mb-0 printArea">
            <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('From warehouse') }}</th>
                    <th data-breakpoints="md">{{ translate('To warehouse') }}</th>
                    <th data-breakpoints="md">{{ translate('Product Type Qty') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transfers as $key => $transfer)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-center">{{ getWearhouseName($transfer->from_wearhouse_id) }}</td>
                        <td class="text-center">{{ getWearhouseName($transfer->to_wearhouse_id) }}</td>
                        <td class="text-center">
                            <a href="{{ route('transfer_list_details.index', [
                                'product_id' => $product_ids,
                                'from_warehouse_id' => $transfer->from_wearhouse_id,
                                'to_warehouse_id' => $transfer->to_wearhouse_id,
                                'start_date' => $start_date,
                                'end_date' => $end_date
                            ]) }}" target="_blank">
                                {{ $transfer->total_qty }}
                            </a>
                        </td>
                        <td class="text-right">{{ single_price($transfer->total_amount) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align:right;" colspan="3"><b>Total</b></td>
                    <td style="text-align:right;" class="text-center"><b>{{ $totalQty }}</b></td>
                    <td style="text-align:right;"><b>{{ single_price($totalAmount) }}</b></td>
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
