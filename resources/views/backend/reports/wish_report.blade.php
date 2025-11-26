@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
      
	</div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h1 class="h6">{{translate('Products Wishlists')}}</h1>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
            </div>
            <div class="printArea">
                <style>
                    th{text-align:center;}
                </style>
                <div class="card-body">
                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Wish Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                            @if(!empty($product->uname))
                                    <tr>
                                        <td>{{$product->name}}</td>
                                        <td>
                                            <a href="{{route('customerwishlish',$product->product_id)}}" target="_blank" >{{$product->total_customer}}</a>												  
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="aiz-pagination mt-4">
                    
            </div>
        </div>
    </div>
</div>

@endsection
