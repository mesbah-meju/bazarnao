@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Product Stock Closing Details')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <h5>{{ $product->name }}</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td>{{ single_price($supplier->rate) }}</td>
                            <td>{{ single_price($supplier->amount) }}</td>
                            <td>{{ $supplier->purchase_date->format('Y-m-d') }}</td> <!-- Format the date -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
