@extends('backend.layouts.staff')
<style>
    tr,
    th,
    td {
        padding: 3px !important;
    }

    th {
        background: #AE3C86;
        color: #fff;
        font-weight: bold
    }

    li.nav-item {
        width: 100%;
    }

    .navbar-nav {
        width: 100%;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css" rel="stylesheet" />

@section('content')
<div class="row gutters-10">
    <div class="col-lg-12">

        <div id="accordion">
        @include('backend.staff_panel.purchase_manager.purchase_manager_nav')
            <div class="card border-bottom-0">

                <div class="card-body">

                        <form id="culexpo" action="{{ route('damage_report.index') }}" method="GET">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-form-label">{{translate('Sort by warehouse')}} :</label>
                                <select id="demo-ease" class="from-control aiz-selectpicker" name="wearhous_id" data-live-search='true'>
                                    <option value=''>All</option>
                                    @foreach ($wearhouse as $key => $wearhous)
                                    <option @php if($sort_by==$wearhous->id)
                                        echo 'selected';
                                        @endphp
                                        value="{{ $wearhous->id }}">{{ $wearhous->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                        <label>Start Date :</label>
                            <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                            
                    </div>
                    <div class="col-md-3">
                        <label>End Date :</label>
                        <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                            
                    </div>
                           
                            <div class="col-md-3 mt-4">
                                <button class="btn btn-primary" onclick="submitForm ('{{ route('damage_report.index') }}')">{{ translate('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                    <table class="table aiz-table mb-0" style="font-size: 13px;width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Date') }}</th>
                                <th>{{ translate('Product Name') }}</th>
                                <th>{{ translate('Warehouse') }}</th>
                                <th>{{ translate('Remarks') }}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{ translate('QTY') }}</th>
                                <th>{{translate('Amount')}}</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $total = 0;
                            $totalqty = 0;
                            @endphp
                            @foreach ($gte_damage_products as $key => $product)

                            @php 
                            $totalqty +=$product->qty;
                            $total +=$product->total_amount
                            @endphp
                            <tr>
                                <td>
                                    {{ ($key+1) }}
                                </td>
                                <td>
                                    {{$product->date }}
                                </td>
                                <td>
                                    {{$product->product->name }}
                                </td>
                                <td>
                                    {{getWearhouseName($product->wearhouse_id) }}
                                </td>
                                
                                <td>
                                    {{$product->remarks }}
                                </td>
                                 <td>
                                    {{ $product->status}}
                                </td>
                                <td>
                                    {{$product->qty }}
                                </td>
                                <td>
                                    {{$product->total_amount }}
                                </td>
                            </tr>
                            @endforeach
                            <tr style="font-weight:bold;">
                                <td colspan="6">Total</td>
                                <td>{{$totalqty}}</td>
                                <td>{{$total}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>


</div>
@endsection
<script>
 function submitForm(url){
    $('#culexpo').attr('action',url);
    $('#culexpo').submit();
 }
</script>