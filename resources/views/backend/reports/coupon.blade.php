@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Coupon Usages report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('coupon_report.index') }}" method="get">
                <div class="row">
                       <div class="col-md-3">
                            <select id="demo-ease" class="aiz-selectpicker" name="coupon_id"  data-live-search="true">
                                <option value="">All Coupon</option>
                                @foreach ($coupons as $key => $coupon)
                                <option value="{{ $coupon->id }}" @if($coupon->id == $coupon_id) selected @endif >{{ $coupon->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="demo-ease" class="aiz-selectpicker" name="customer_id"  data-live-search="true">
                                <option value="">All Customer</option>
                                @foreach ($customers as $key => $customer)
                                <option value="{{ $customer->id }}" @if($customer->id == $customer_id) selected @endif >{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                    <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                        </div>
                       

                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                        <button class="btn btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
            <hr>
            <div class="printArea">

                @if(!empty($cust))
                <div class="col-md-12" style="text-align: center;">
                    <p><b>Customer Name : </b> {{$cust->name}}</p>
                    <!-- <p><b> Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p> -->

                </div>
                @endif
                <h3 style="text-align:center;">{{translate('Coupon Usages Report')}}</h3>
                <p style="text-align: center;">Date Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                        <th>{{ translate('Coupon ID') }}</th>
                            <th>{{ translate('Customer ID') }}</th>
                            <th>{{ translate('Customer Name') }}</th>
                            <th>{{ translate('Discounted Amount') }}</th>
                            <th>{{ translate('No of times Uages') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;
                    $totalNo = 0;
                        @endphp

                        @foreach($data as $key=>$coupon)
                    	@if(!empty($coupon->coupon_discount) && !empty($coupon->code))
                    		
                    	
                       @php
                                        $total+=$coupon->coupon_discount;
                    					$totalNo+=$coupon->usagee;
                                    @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                        <td>{{ $coupon->code }}</td>
                            <td style="text-align:center;">
                    @if(!empty($coupon->customer_no))
                    
                    <a href="{{route('customer_ledger_details.index')}}?cust_id={{$coupon->user_id}}" target="_blank" title="{{ translate('View') }}">{{ $coupon->customer_no }}</a>
                       @endif
                       
                    </td>
                            <td>{{ $coupon->name }}</td>
                             <td style="text-align: right;">
                                                    {{ single_price($coupon->coupon_discount) }}
                                                </td>
                         <td style="text-align: right;">
                                                    {{ $coupon->usagee }}
                                                </td>
                            
                        </tr>
                   		 @endif
                        @endforeach
                        <tr>
                            <th colspan="4" style="text-align:right">Total</th>
                            <th style="text-align:right">{{number_format(abs($total),2)}}</th>
                        <th style="text-align:right">{{number_format(abs($totalNo),2)}}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection