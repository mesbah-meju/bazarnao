@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class=" align-items-center">
      <h3>Wishlist/Customers List</h3>
	</div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
       

                <table class="table table-bordered aiz-table mb-0">
                    <thead>
                        <tr>

                        
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                          
                           
                        </tr>
                    </thead>
                    <tbody>

                    @foreach ($wisher as $wish)
                           
                                <tr>

                               <td>
                               
                                <a href="{{route('customer_ledger_details.index')}}?cust_id={{$wish->user_id}}" target="_blank">{{$wish->customer_id}}</a>
                            
                            </td>
                                    <td>{{$wish->uname}}</td>
                               
                                </tr>
                            
                        @endforeach
                        
                    </tbody>
                </table>
                <div class="aiz-pagination mt-4">
                   
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
