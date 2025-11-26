@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
      
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h1 class="h6">{{ translate('Group Products Report') }}</h1>
                {{-- <div class="d-flex">
                    <button class="btn btn-sm btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    <a href="{{ route('group_product_report.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a> --}}
                    {{-- <form id="culexpo" method="POST" action="{{ route('group_product_export') }}">
                        @csrf
                        <button class="btn btn-sm btn-success" type="submit">{{ translate('Excel') }}</button>
                    </form> --}}
                {{-- </div> --}}
            </div>
            <form id="prowasales" action="{{ route('group_product_report.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group p-1">
                            <label for="group_id">{{ translate('Select Group Product') }}</label>
                            <select class="form-control aiz-selectpicker" name="group_id[]" id="group_id" data-live-search="true"  multiple>
                                @foreach ($groupProducts as $group)
                                    <option value="{{ $group->id }}" 
                                        @if(is_array(request()->group_id) && in_array($group->id, request()->group_id)) selected @endif>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    
                        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Filter') }}</button>
                    
                    <button class="btn btn-sm btn-secondary mx-1" type="button" onclick="resetForm()">{{ translate('Reset') }}</button>
                    <button class="btn btn-sm btn-info mr-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    <a href="{{ route('group_product_report.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>
                    {{-- <form id="culexpo" method="POST" action="{{ route('group_product_export') }}">
                        @csrf
                        <button class="btn btn-sm btn-success" type="submit">{{ translate('Excel') }}</button>
                    </form> --}}
                </div>
            </form>
            
            <div class="card-body printArea">
                <style>
                    th {
                        text-align: center;
                    }
                    .group-row {
                        background-color: #f8f9fa;
                    }
                    .product-row {
                        background-color: #ffffff;
                    }
                </style>
                
                <div class="table-responsive">
                    <h1 class="h6">{{ translate('Group Products Report') }}</h1>
                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>{{ translate('SL') }}</th>
                                <th>{{ translate('Group Product Name') }}</th>
                                <th>{{ translate('Product Name') }}</th>
                                <th>{{ translate('Product QTY') }}</th>
                                <th>{{ translate('Product Price') }}</th>
                                <th>{{ translate('Discount') }}</th>
                                <th>{{ translate('Group Price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $i = 1;
                            @endphp
                            @foreach ($groupProducts as $groupProduct)
                                @php 
                                    $details = $productDetails->get($groupProduct->id);
                                    $total_web_price = 0;
                                    $total_app_price = 0;
                                    $total_web_discount_amount = 0;
                                    $total_app_discount_amount = 0;
                                @endphp
                                @if ($details)
                                    @foreach ($details as $detail)
                                        @php 
                                            $total_web_price += $detail->price;
                                            $total_app_price += $detail->app_price;
                                            
                                            $total_web_discount_amount += $detail->discount_amount;
                                            $total_app_discount_amount += $detail->app_discount_amount;
                                        @endphp
                                    @endforeach
                                    <tr class="group-row">
                                        <td>{{ $i++ }}</td>
                                        <td><strong>{{ $groupProduct->name }}</strong></td>
                                        <td colspan="1"></td>
                                        <td colspan="1"></td>
                                        <td colspan="1"></td>
                                        <td colspan="1">Web: </b>{{ single_price($total_web_discount_amount??0) }}<br><b>App: </b>{{ single_price($total_app_discount_amount?? 0) }}</td>
                                        <td><b>Web: </b>{{ single_price($total_web_price ?? 0) }}<br><b>App: </b>{{ single_price($total_app_price ?? 0) }}</td>
                                    </tr>
                                    @foreach ($details as $detail)
                                        <tr class="product-row">
                                            <td></td>
                                            <td></td>
                                            <td>{{ $detail->product_name }}</td>
                                            <td><b>Web: </b>{{ $detail->qty }} <br><b>App: </b>{{ $detail->app_qty ?? 0}}</td>
                                            <td><b>Web: </b>{{ single_price($detail->price ?? 0) }} <br><b>App: </b>{{ single_price($detail->app_price ?? 0) }}</td>
                                            <td><b>Web: </b>{{ single_price($detail->discount_amount ?? 0) }} <br><b>App: </b>{{ single_price($detail->app_discount_amount ?? 0) }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function printDiv() {
        var printContents = document.querySelector('.printArea').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
    
    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }
    function resetForm() {
    document.getElementById('prowasales').reset();
    // Reset select2 elements
    $('#group_id').val(null).trigger('change');
}
</script>


@endsection
