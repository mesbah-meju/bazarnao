@extends('backend.layouts.app')

@section('content')
<style>
    #item_table .form-control{
        padding: 2px;
    }
</style>
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Transfer')}}</h5>
</div>
<div class="">
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('transfer.update',$transfer->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
            @csrf
            <input type="hidden" name="added_by" value="admin">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Transfer')}}</h5>
                </div>
                <div class="card-body">
                <div class="col-md-6 pull-left">
                        <label>{{translate('From Wearhouse')}} <span class="text-danger">*</span></label>

                        <select class="form-control aiz-selectpicker" name="from_wearhouse_id" id="from_wearhouse_id" data-live-search="true" required>
                            @foreach ($wearhouses as $supp)
                            <option <?php if($transfer->from_wearhouse_id == $supp->id) echo 'selected';?> value="{{ $supp->id }}">{{ $supp->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-6 pull-left">
    						<label for="name">{{translate('To Wearhouse')}} <span class="text-danger">*</span></label>
    						<select name="to_wearhouse_id" id="to_wearhouse_id" class="form-control" required>
                                <option value="">{{translate('Select Wearhouse')}}</option>
                                @foreach($wearhouses as $row)
                                <option <?php if($transfer->to_wearhouse_id == $row->id) echo 'selected';?> value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach

                            </select>
    					</div>
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Product')}} <span class="text-danger">*</span></label>

                        <select class="form-control aiz-selectpicker" name="product_id" id="product_id" onchange="changeProduct(this.value)"  data-live-search="true" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                        <option <?php if($transfer->product_id == $product->id) echo 'selected';?>  value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>

                    </div>
                    <div class="col-md-3 pull-left">
                        <label>{{translate('Stock Qty')}} <span class="text-danger">*</span></label>

                        <input type="text" class="form-control"  name="stock_qty" id="stock_qty" value="{{$transfer->stock_qty}}" readonly>

                    </div>
                    <div class="col-md-3 pull-left">
                        <label>{{translate('Transfer Qty')}} <span class="text-danger">*</span></label>

                        <input type="number" class="form-control" name="qty" id="transfer_qty" max="{{$transfer->stock_qty}}" value="{{$transfer->qty}}"  required>

                    </div>
                
                    <div class="col-md-3 pull-left">
                        <label>{{translate('Unit Price')}} <span class="text-danger">*</span></label>

                        <input type="any" class="form-control" name="unit_price" id="unit_price"  value="{{$transfer->unit_price}}"  required>

                    </div>
                    
                    <div class="col-md-3 pull-left">
                        <label>{{translate('Transfer Date')}} <span class="text-danger">*</span></label>

                        <input type="date" class="form-control" name="date" value="{{$transfer->date}}"  required>

                    </div>

                    
                    
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Remarks')}} <span class="text-danger">*</span></label>

                        <input type="text" class="form-control" name="remarks" placeholder="{{ translate('Remarks') }}" value="{{$transfer->remarks}}"  required>

                    </div>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
            

            <div class="mb-3 text-right">
                <button type="submit" name="button" class="btn btn-primary">{{ translate('Save Transfer') }}</button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('script')

<script type="text/javascript">
    function changeProduct(id){
       
        var product_id = $('#product_id').val();
        var wearhouse_id = $('#from_wearhouse_id').val();
        if(wearhouse_id === '' || wearhouse_id ===undefined || wearhouse_id === null){
            alert('Please select from wearhouse');
            return false;
        }
       $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: "{{route('purchase_orders.get_puracher_product')}}",
               type: 'POST',
               data: {
                product_id: product_id,
                wearhouse_id: wearhouse_id
               },
               //dataType: 'html',
               success: function(data) {
             $('#stock_qty').val(data.qty);
             $('#transfer_qty').attr('max', data.qty);
               
               }
           }); 
       
      }

    
</script>

@endsection