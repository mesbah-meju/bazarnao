@extends('backend.layouts.app')
@section('content')
<div class="card">
    <form id="culexpo" action="{{ route('fifo_transfer_list_report.index') }}" method="GET">
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
                    <select class="form-control" name="warehouse" id="warehouse">
                        <option value="">Select One</option>
                        @foreach(\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}" @if($warehouse == $wh->id) selected @endif>{{ $wh->name }}</option>
                        @endforeach                      
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <label>Filter By To Warehouse:</label>
                    <select class="form-control" name="to_warehouse" id="to_warehouse">
                        <option value="">All</option>
                        @foreach(\App\Models\Warehouse::all() as $wh)
                            <option value="{{ $wh->id }}" @if($to_warehouse == $wh->id) selected @endif>{{ $wh->name }}</option>
                        @endforeach                      
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label>Filter By Product:</label>
                    <select class="form-control aiz-selectpicker select2" name="product_id[]" id="product_id" data-live-search="true" multiple>
                        @foreach(\App\Models\Product::all() as $product)
                            <option value="{{ $product->id }}" @if(!empty($product_ids) && in_array($product->id, $product_ids)) selected @endif>{{ $product->name }}</option>
                        @endforeach                      
                    </select>
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0 mt-2">
                    <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>

                    <a href="{{ route('transfer_list_report.index',[
                                'type' => 'excel',
                                'start_date' => request()->input('start_date'),
                                'end_date' => request()->input('end_date'),
                                'warehouse' => request()->input('warehouse'),
                                'to_warehouse' => request()->input('to_warehouse'),
                                'product_id' => request()->input('product_id'),
                                'search' => request()->input('search'),
                            ]) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>
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
        <div class="printArea">
        <h3 style="text-align:center;">{{translate('Fifo Transfer List Report')}}</h3>
        <table class="table aiz-table mb-0 ">
            <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('Product') }}</th>
                    <th data-breakpoints="md">{{ translate('From wearhouse') }}</th>
                    <th data-breakpoints="md">{{ translate('To wearhouse') }}</th>
                    <th data-breakpoints="md">{{ translate('Qty') }}</th>
                    <th data-breakpoints="md">{{ translate('Unit Price') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('date') }}</th>
                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                $i = 0;
                $total = 0;
                $totalAmount = 0;
                $totalQty = 0;
                @endphp
                @foreach ($transfers as $key => $order)
                 @php
                     $total+=$order->unit_price;
                     $totalAmount+=$order->amount;
                     $totalQty+=$order->qty;
                 @endphp
                    <tr>
                        <td>
                            {{ $key + 1 }}
                        </td>
                        <td>
                            {{ $order->product->name }}
                        </td>
                        <td>
                            {{ getWearhouseName($order->from_wearhouse_id) }}
                        </td>
                        <td>
                            {{ getWearhouseName($order->to_wearhouse_id) }}
                        </td>
                        <td>
                            {{ $order->qty }}
                        </td>
                        <td>
                            {{ $order->unit_price }}
                        </td>
                        <td>
                            {{ $order->amount }}
                        </td>
                        <td>
                            {{ $order->date }}
                        </td>
                        <td>
                            {{ $order->status }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td style="text-align:right;" colspan="4"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{$totalQty}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($total)}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($totalAmount)}}</b></td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function printDiv() {
            // Add your print logic here
        }
    </script>
@endsection
