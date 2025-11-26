@extends('backend.layouts.app')

@section('content')
<style>
    #item_table .form-control{
        padding: 2px;
    }
</style>
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Edit New Warehouse Cash Transfer')}}</h5>
</div>
<div class="">
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('warehouse_cash_transfer.update',$transfer->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
            @csrf
            <input type="hidden" name="added_by" value="admin">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('cash Transfer')}}</h5>
                </div>
                <div class="card-body">
                <div class="col-md-6 pull-left">
                        <label>{{translate('From Wearhouse')}} <span class="text-danger">*</span></label>

                        <select disabled class="form-control aiz-selectpicker" name="from_wearhouse_id" id="from_wearhouse_id" data-live-search="true" required>
                            @foreach ($wearhouses as $supp)
                            <option <?php if($transfer->from_wearhouse_id == $supp->id) echo 'selected';?> value="{{ $supp->id }}">{{ $supp->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-6 pull-left">
    						<label for="name">{{translate('To Wearhouse')}} <span class="text-danger">*</span></label>
    						<select disabled name="to_wearhouse_id" id="to_wearhouse_id" class="form-control" required>
                                <option value="">{{translate('Select Wearhouse')}}</option>
                                @foreach($wearhouses as $row)
                                <option <?php if($transfer->to_wearhouse_id == $row->id) echo 'selected';?> value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach

                            </select>
    					</div>
                    
                
                    <div class="col-md-3 pull-left">
                        <label>{{translate('Transfer Amount')}} <span class="text-danger">*</span></label>

                        <input readonly type="any" class="form-control" name="amount" id="amount"  value="{{$transfer->amount}}"  required>

                    </div>
                    
                    <div class="col-md-3 pull-left">
                        <label>{{translate('Transfer Date')}} <span class="text-danger">*</span></label>

                        <input readonly type="date" class="form-control" name="date" value="{{$transfer->date}}"  required>

                    </div>

                    
                    
                    <div class="col-md-6 pull-left">
                        <label>{{translate('Remarks')}} <span class="text-danger">*</span></label>

                        <input readonly type="text" class="form-control" name="remarks" placeholder="{{ translate('Remarks') }}" value="{{$transfer->remarks}}"  required>

                    </div>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
            

            
        </form>
    </div>
</div>



@endsection

@section('script')

<script type="text/javascript">
     document.getElementById('from_wearhouse_id').addEventListener('change', function () {
            var fromWearhouseId = this.value;
            var toWearhouseSelect = document.getElementById('to_wearhouse_id');
    
            // Remove all options except the first one
            for (var i = toWearhouseSelect.options.length - 1; i >= 1; i--) {
                toWearhouseSelect.remove(i);
            }
    
            // Add the filtered options
            @foreach ($wearhouses as $row)
                if ({{ $row->id }} != fromWearhouseId) {
                    var option = document.createElement('option');
                    option.value = '{{ $row->id }}';
                    option.text = '{{ $row->name }}';
                    toWearhouseSelect.add(option);
                }
            @endforeach
        });
              

    
</script>

@endsection