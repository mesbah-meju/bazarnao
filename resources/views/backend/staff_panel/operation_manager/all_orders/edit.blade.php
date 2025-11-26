@extends('backend.layouts.staff')

@section('content')
@include('backend.staff_panel.customer_service.customer_executive_nav')

    <div class="card">
        <form action="{{route('orders.update', $order->id)}}" method="POST">
        @csrf  
        @method('PUT')
        <div class="card-header">
          <h1 class="h2 fs-16 mb-0">{{ translate('Order Edit') }}</h1>
        </div>
        <div class="card-header row gutters-5">
  			<div class="col text-center text-md-left">
  			</div>
              @php
                  $delivery_status = $order->orderDetails->first()->delivery_status;
                  $payment_status = $order->orderDetails->first()->payment_status;
              @endphp
            <!-- <div class="col-md-3 ml-auto">
                <label for=update_payment_status"">{{translate('Payment Status')}}</label>
                <select class="form-control aiz-selectpicker"  data-minimum-results-for-search="Infinity" id="update_payment_status">
                    <option value="paid" @if ($payment_status == 'paid') selected @endif>{{translate('Paid')}}</option>
                    <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>{{translate('Unpaid')}}</option>
                </select>
            </div>
  			   <div class="col-md-3 ml-auto">
                  <label for=update_delivery_status"">{{translate('Delivery Status')}}</label>
                  <select class="form-control aiz-selectpicker"  data-minimum-results-for-search="Infinity" id="update_delivery_status">
                      <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{translate('Pending')}}</option>
                      <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{translate('Confirmed')}}</option>
                      <option value="on_delivery" @if ($delivery_status == 'on_delivery') selected @endif>{{translate('On delivery')}}</option>
                      <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>{{translate('Delivered')}}</option>
                      <option value="received" @if ($delivery_status == 'received') selected @endif>{{translate('Received')}}</option>
                  </select>
  			</div> -->
  		</div>
    	<div class="card-body">
            <input type="hidden" name="user_id" value="{{$order->user_id}}">
        <div class="row gutters-5">
  			<div class="col text-center text-md-left">
            <address>
                <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                {{ json_decode($order->shipping_address)->email }}<br>
                {{ json_decode($order->shipping_address)->phone }}<br>
                {{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->postal_code }}<br>
                {{ json_decode($order->shipping_address)->country }}
            </address>
            @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                <br>
                <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }}, {{ translate('Amount') }}: {{ single_price(json_decode($order->manual_payment_data)->amount) }}, {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                <br>
                <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt="" height="100"></a>
            @endif
  				</div>
  				<div class="col-md-4 ml-auto">
            <table>
        				<tbody>
            				<tr>
            					<td class="text-main text-bold">{{translate('Order #')}}</td>
            					<td class="text-right text-info text-bold">	{{ $order->code }}</td>
            				</tr>
            				<tr>
                                <td class="text-main text-bold">{{translate('Order Status')}}</td>
                                    @php
                                      $status = $order->orderDetails->first()->delivery_status;
                                    @endphp
            					<td class="text-right">
                                    @if($status == 'delivered')
                                        <span class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                    @else
                                        <span class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $status))) }}</span>
                                    @endif
      					        </td>
            				</tr>
            				<tr>
            					<td class="text-main text-bold">{{translate('Order Date')}}	</td>
            					<td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
            				</tr>
                    <tr>
            					<td class="text-main text-bold">{{translate('Total amount')}}	</td>
            					<td class="text-right">
            						{{ single_price($order->grand_total) }}
            					</td>
            				</tr>
                    <tr>
            					<td class="text-main text-bold">{{translate('Payment method')}}</td>
            					<td class="text-right">{{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</td>
            				</tr>
        				</tbody>
    				</table>
  				</div>
  			</div>
    		<hr class="new-section-sm bord-no">
    		<div class="row">
    			<div class="col-lg-12 table-responsive">
          <a href="javascript:" id="add_row" class="btn btn-sm btn-primary"><i class="las la-plus"></i>Add Row</a>
    				<table class="table table-bordered  invoice-summary">
        				<thead>
            				<tr class="bg-trans-dark">
                            <th  class="min-col text-center text-uppercase"></th>
                        <th class="min-col">#</th>
                        <th width="50%">{{translate('Product')}}</th>
        					      <!-- <th class="text-uppercase">{{translate('Description')}}</th> -->
                        <!-- <th class="text-uppercase">{{translate('Delivery Type')}}</th> -->
              					<th  class="min-col text-center text-uppercase">{{translate('Qty')}}</th>
              					<th class="min-col text-center text-uppercase">{{translate('Price')}}</th>
        					       <th  class="min-col text-right text-uppercase">{{translate('Total')}}</th>
            				</tr>
        				</thead>
        				<tbody id="item_table">
                    @foreach ($order->orderDetails as $key => $orderDetail)
                      <tr class="tablerow" id="row_{{ $key+1 }}">
                          
                      <td>
                    <a href="javascript:" onclick="removeItemRow({{$key+1}})" class="btn btn-sm btn-danger"><i class="las la-minus"></i></a>
                    </td>

                        <td>{{ $key+1 }}</td>
                        <td>
                        <select class="form-control  aiz-selectpicker" name="product[{{$key+1}}]" id="product_{{$key+1}}" onchange="productChange(this)" data-live-search="true">
                        @foreach($products as $product)
                       @php 
                       $acPrice = 0;
                        $disAmount = 0;
                        @endphp
                        @if($product->discount > 0)
                        @if($product->discount_type == 'amount')
                        @php 
                         $disAmount = $product->discount;
                         @endphp
                        @else
                        @php 
                        $disAmount = ($product->unit_price * $product->discount) / 100;
                        $disAmount = round($disAmount);
                        
                        @endphp
                        @endif
                        @php 
                        $acPrice =$product->unit_price- $disAmount;
                        
                        @endphp 
                        @else
                        @php 
                        $acPrice = $product->unit_price
                        @endphp 
                        @endif

                        <option @if($orderDetail->product->id==$product->id) {{'selected'}} @endif value="{{$product->id}}" data-price="{{ $acPrice }}" data-discount="{{ $disAmount }}">{{$product->name}}</option>
                        @endforeach
                        </select>

                          </td>
                     
                          <td class="text-center">
                            <input type="number" value="{{ $orderDetail->quantity }}" name="qty[{{$key+1}}]" class="form-control" onblur="caculatePrice({{$key+1}})" id="qty_{{$key+1}}">
                            <input type="hidden" value="{{ $orderDetail->quantity }}" name="oldqty[{{$key+1}}]" class="form-control">
                        </td>
                        <td class="text-center"><input type="number" value="{{ $orderDetail->price/$orderDetail->quantity }}" name="rate[{{$key+1}}]" class="form-control" id="rate_{{$key+1}}"></td>
                        <td  class="text-center">
						<input id="dis_amount_{{$key+1}}" name="dis_amount[{{$key+1}}]" class="dis_amount" type="hidden" value="{{ $orderDetail->discount }}">
						<input id="special_discount_{{$key+1}}" name="special_discount[{{$key+1}}]" class="special_discount" type="hidden" value="{{ $orderDetail->special_discount }}">
						<input type="any" value="{{ $orderDetail->price }}" name="total[{{$key+1}}]" class="form-control" id="total_{{$key+1}}">
						</td>
                      </tr>
                    @endforeach
        				</tbody>
    				</table>
    			</div>
    		</div>
    		<div class="clearfix float-right">
    			<table class="table">
        			<tbody>
        			<tr>
        				<td>
        					<strong class="text-muted">{{translate('Sub Total')}} :</strong>
        				</td>
        				<td>
        					<input class="form-control" type="text" name="sub_total" id="sub_total" value="{{ $order->orderDetails->sum('price') }}">
        				</td>
        			</tr>
        			<tr>
        				<td>
        					<strong class="text-muted">{{translate('Tax')}} :</strong>
        				</td>
        				<td>
        					{{ single_price($order->orderDetails->sum('tax')) }}
        				</td>
        			</tr>
                    <tr>
        				<td>
        					<strong class="text-muted">{{translate('Shipping')}} :</strong>
        				</td>
        				<td>
                        <input class="form-control" type="text" name="shippingcost" id="shippingcost" value="{{ $order->orderDetails->sum('shipping_cost') }}">
        				</td>
        			</tr>
                    <tr>
        				<td>
        					<strong class="text-muted">{{translate('Coupon')}} :</strong>
        				</td>
        				<td>
        					{{ single_price($order->coupon_discount) }}
        				</td>
        			</tr>
        			<tr>
        				<td>
        					<strong class="text-muted">{{translate('TOTAL')}} :</strong>
        				</td>
        				<td class="text-muted h5">
                        <input class="form-control" type="text" name="grand_total" id="grand_total" value="{{ $order->grand_total }}">
        				</td>
        			</tr>
        			</tbody>
    			</table>
          <div class="text-right no-print">
            <a href="{{ route('invoice.download', $order->id) }}" type="button" class="btn btn-icon btn-light"><i class="las la-print"></i></a>
          </div>
    		</div>

    	</div>
        <div class="form-group col-lg-5">
                            <button type="submit" class="btn btn-primary" >Update</button>
                            
                        </div>
</form>
    </div>
    <div class="modal fade" id="payment-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">{{ translate('Payment')}}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <div class="p-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Amount')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="number" id="payment_amount" class="form-control textarea-autogrow mb-3" placeholder="{{ translate('Amount')}}" rows="1" name="amount" required>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="save_payment" class="btn btn-primary">{{  translate('Save') }}</button>
                </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">

        $('#update_delivery_status').on('change', function(){
            var order_id = '{{ $order->id }}';
            var status = $('#update_delivery_status').val();
            $.post('{{ route('orders.update_delivery_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');
            });
        });

        $('#update_payment_status').on('change', function(){
            var order_id = '{{ $order->id }}';
            var status = $('#update_payment_status').val();
              $.post('{{ route('orders.update_payment_status') }}', {_token:'{{ @csrf_token() }}',order_id:order_id,status:status}, function(data){
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            });
        });
        function save_payment(){
          var order_id = '{{ $order->id }}';
            var status = $('#update_payment_status').val();
            var payment_amount = $('#payment_amount').val();
          $.post('{{ route('orders.update_payment_status') }}', {_token:'{{ @csrf_token() }}',payment_amount:payment_amount,order_id:order_id,status:status}, function(data){
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            });
        }
        $('#add_row').on('click',function(){
        
          var row = $('.tablerow').length;
        
    row++;
    var str = '<tr id="row_' + row + '" class="tablerow">';
    str += ' <td><a href="javascript:" onclick="removeItemRow(' + row + ')" class="btn btn-sm btn-danger"><i class="las la-minus"></i></a></td>';
  
    str += '<td>' + row + '</td>';
    str += '<td>';
    str += '<select class="form-control  aiz-selectpicker" id="aiz-selectpicker_' + row + '" name="product[' + row + ']" id="product_' + row + '" onchange="productChange(this)" data-live-search="true">';
    str += '<option value="">Please select</option>';
    <?php foreach($products as $product) { 
        
        $acPrice = 0;
         $disAmount = 0;
         
         if($product->discount > 0){
         if($product->discount_type == 'amount'){
          
          $disAmount = $product->discount;
         
         }else{
         
         $disAmount = ($product->unit_price * $product->discount) / 100;
         $disAmount = round($disAmount);
         }
         
         $acPrice =$product->unit_price- $disAmount;
         
        }else{
         
         $acPrice = $product->unit_price;
         
        }
        ?>
        str += '<option data-discount="{{ $disAmount }}" data-price="{{ $acPrice }}" value="{{$product->id}}">{{$product->name}}</option>';
  <?php  } ?>
                        
                       
    str += '</select>';

    str += ' </td>';

    str += '<td class="text-center"><input type="number" value="" name="qty[' + row + ']" class="form-control" id="qty_' + row + '" onblur="caculatePrice('+row+')"></td>';
    str += '<td class="text-center"><input type="number" value="" name="rate[' + row + ']" class="form-control" id="rate_' + row + '"></td>';
    str += '<td class="text-center"><input id="dis_amount_' + row + '" name="dis_amount[' + row + ']" class="dis_amount" type="hidden" value=""><input type="number" value="" name="total[' + row + ']" class="form-control" id="total_' + row + '"></td>';
    str += '</tr>';
    
    $('#item_table').append(str);
	$('#aiz-selectpicker_' + row).selectpicker();
        })
    
        //$('.aiz-selectpicker').selectpicker();
    </script>


<script>

function removeItemRow(id) {
        if (confirm('Are you sure to Remove ? ') == true) {
            $('#row_' + id).remove();
            calculateTotal();
        }
    }

</script>

    <script>
        
        function productChange(e) {
            var id = $(e).attr('id').split('_')[1];
        
        var price = Number($(e).find('option:selected').data('price'));
        var discount = Number($(e).find('option:selected').data('discount'));
        var qty = Number($('#qty_' + id).val());
        
        if (!qty)
            qty = 1;
        var total = price * qty;
        $('#qty_' + id).val(qty);
        $('#rate_' + id).val(price);
        $('#dis_amount_' + id).val(discount);
        
        $('#total_' + id).val(total);
        calculateTotal();
    }
        function caculatePrice(id){
var qty = Number($('#qty_'+id).val())
var rate = Number($('#rate_'+id).val())
var total = qty*rate;
$('#total_'+id).val(total)
calculateTotal();
        }

        function calculateTotal() {
           
        var total = 0;
        $('#item_table').find('tr').each(function() {
            var id = $(this).attr('id').split('_')[1];
            
            total += Number($('#total_' + id).val());
        });
        if(total >=200 ){
          var ship = 0;  
        }else{
            var ship = 60; 
        }
        $('#sub_total').val(total);
        $('#shippingcost').val(ship);
        $('#grand_total').val(total+ship);
    }
    </script>
@endsection
