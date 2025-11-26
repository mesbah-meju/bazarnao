@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Product FIFO Details Report') }}</h1>
    </div>
</div>
<button class="btn btn-sm btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>

<div class="printArea">
<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="row p-3 voucher-center">
                <div class="col-md-3">
                    <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
                </div>
                <div class="col-md-6 text-center">
                    <h2>Bazarnao</h2>
                    {{-- <strong><u class="pt-4">{{ translate('Product Wise FIFO Details Report') }}</u></strong><br>
                    {{ date('F d, Y', strtotime($start_date)) }} to {{ date('F d, Y', strtotime($end_date)) }} <br> --}}

                </div>
                <div class="col-md-3">
                    <div class="pull-right" style="margin-right:20px;">
                        <b>
                            <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                        </b>
                        <br>
                        {{-- <b>
                            <label class="font-weight-600 mb-0">Product: </label>
                        </b>{{ $products->first()->product_name ?? 'N/A' }} 
                        <br> --}}
                        
                       
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- <div class="mb-3">
                    <strong>Product:</strong> <br>
                    <strong>Date Range:</strong> {{ $start_date }} to {{ $end_date }} <br>
                </div> --}}
                
                
                <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction</th>
                    <th>Units</th>
                    <th>Cost</th>
                    <th>Price</th>
                    <th>Total Costs</th>
                    <th>Total Sales</th>
                    <th>Remain-Stock-Cost</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $remainStock = [];
                @endphp

                @foreach ($fifoDetails as $detail)
                    <tr>
                        <td>{{ $detail['date'] }}</td>
                        <td>{{ $detail['transaction'] }}</td>
                        <td>{{ $detail['units'] }}</td>
                        <td>{{ number_format($detail['cost'], 2) }}</td>
                        <td>{{ number_format($detail['price'], 2) }}</td>
                        <td>{{ number_format($detail['total_costs'], 2) }}</td>
                        <td>{{ number_format($detail['total_sales'], 2) }}</td>
                        <td>{{ $detail['remaining_stock_cost'] }}</td>
                        
                        <!-- Calculate and display Remain-Stock-Cost -->
                        {{-- @php
                            if ($detail['transaction'] === 'Sale' || $detail['transaction'] === 'Transfer Out') {
                                // Deduct sold units from stock
                                $remaining = $detail['units'];
                                foreach ($remainStock as $key => &$stock) {
                                    if ($stock['units'] <= $remaining) {
                                        // $remaining -= $stock['units'];
                                        unset($remainStock[$key]);
                                    } else {
                                        $stock['units'] -= $remaining;
                                        break;
                                    }
                                }
                            } else {
                                // Add units to stock
                                $remainStock[] = [
                                    'units' => $detail['units'],
                                    'cost' => $detail['cost'],
                                ];
                            }
                        @endphp --}}
                        {{-- <td>
                            @foreach ($remainStock as $stock)
                                {{ $stock['units'] }} -> {{ number_format($stock['cost'], 2) }}
                                @if (!$loop->last), @endif
                            @endforeach
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6">Total:</th>
                    {{-- <th>{{ number_format($total_costs, 2) }}</th> --}}
                    <th>{{ number_format($total_sales, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection
