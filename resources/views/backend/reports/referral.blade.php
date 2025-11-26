@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Referral Usages report')}}</h1>
    </div>
</div>

<div class="col-md-12 mx-auto">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('referral_report.index') }}" method="get">
                <div class="row">
                       
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
                       

                    <div class="col-md-3">
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
                <h3 style="text-align:center;">{{translate('Referral Usages Report')}}</h3>
                <p style="text-align: center;">Date Period : </b> {{date('d-m-Y',strtotime($start_date))}} to {{date('d-m-Y',strtotime($end_date))}}</p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>{{ translate('Referral ID') }}</th>
                            <th>{{ translate('Referral Name') }}</th>
							<th>{{ translate('Referral Qty') }}</th>
                            <th>{{ translate('Referral Sales') }}</th>
                            <th>{{ translate('Commission') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;
                    $totalNo = 0;
					$tt = 0;
                        @endphp

                        @foreach($data as $key=>$coupon)
                   		
                    	
                       @php
					  // dd($coupon);
					  $total+=$coupon->gtotal;
										if(!empty($coupon->gqty)){
											
											$tt+=(($coupon->qty*$amt)+($coupon->gqty*$amt));
										}else{
											$tt+=($coupon->qty*$amt);
										}
                                        
                    					$totalNo+=$coupon->qty;
										
                                    @endphp
                        <tr>
                            <td>{{ $key+1 }}</td>
                        
                            <td style="text-align:center;">
                    @if(!empty($coupon->customer_id))
                    
                    <a href="{{route('customer_ledger_details.index')}}?cust_id={{$coupon->user_id}}&start_date={{$start_date}}&end_date={{$end_date}}" target="_blank" title="{{ translate('View') }}">{{ $coupon->customer_id }} </a>
                       @endif
                       
                    </td>
                            <td>{{ $coupon->name }}</td>
							<td style="text-align: right;">
							<a href="{{ route('referral_details.index') }}?user_id={{$coupon->user_id}}&start_date={{$start_date}}&end_date={{$end_date}}" target="_blank" title="{{ translate('View') }}">{{ $coupon->qty }} </a>
							</td>
                             <td style="text-align: right;">
							 @if(!empty($coupon->gtotal))
                                                    {{ single_price($coupon->gtotal) }}
												@else
													{{ single_price(0) }}
												@endif
                                                </td>
                         <td style="text-align: right;">
						 @if(!empty($coupon->gqty))
                                                    {{ single_price(($coupon->qty*$amt)+($coupon->gqty*$amt)) }}
												@else
													{{ single_price($coupon->qty*$amt) }}
												@endif
                                                </td>
                            
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" style="text-align:right">Total</th>
                            <th style="text-align:right">{{$totalNo}}</th>
                        <th style="text-align:right">{{number_format(abs($total),2)}}</th>
						<th style="text-align:right">{{number_format(abs($tt),2)}}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection