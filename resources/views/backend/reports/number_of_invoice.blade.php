@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Product Wise Sales Report') }}</h1>
    </div>
</div>
<button class="btn btn-sm btn-info" type="button" onclick="printDiv()">{{ translate('Print') }}</button>
{{-- Table for Order Details --}}
<div class="printArea">

    <div class="card">
        <div class="row p-3 voucher-center">
            <div class="col-md-3">
                <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo" height="40px"><br><br>
            </div>
            <div class="col-md-6 text-center">
                <h2>Bazarnao</h2>
                <strong><u class="pt-4">{{ translate('Invoice Details Report') }}</u></strong>
            </div>
            <div class="col-md-3">
                <div class="pull-right" style="margin-right:20px;">
                    <b>
                        <label class="font-weight-600 mb-0">{{ translate('date') }}</label> : {{ date('d/m/Y') }}
                    </b>
                    <br>
                    <b>
                        <label class="font-weight-600 mb-0">Product: </label>
                    </b>{{ $product_name ?? 'N/A' }}
                    <br>

                </div>
            </div>
        </div>
        <div class="card-header">


        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-light text-center">
                        <th>{{ translate('Sl') }}</th>
                        <th>Delivered Date</th>
                        <th>Order Code</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-center">
                    @foreach($order_details as $key => $detail)
                    <tr>
                        <td>{{ $key + 1 }}</td> {{-- Serial Number --}}
                        <td>{{ date('Y-m-d', strtotime($detail->delivered_date)) }}</td>
                        <td>
                            <a href="{{ route('all_orders.show', encrypt($detail->order_id)) }}" target="_blank"
                                title="{{ translate('View') }}">{{ $detail->code }}</a>
                        </td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="bg-white text-center">
                        <th colspan="3">Total</th>
                        <th>
                            {{ $order_details->sum('quantity') }}
                        </th>
                        <th>
                            {{ number_format($order_details->sum('price'), 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection