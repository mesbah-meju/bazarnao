@extends('backend.layouts.staff')

@section('content')

    @include('backend.staff_panel.delivery_executive.delivery_executive_nav')

<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Daily Activity Ledger Report') }}</h5>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label>Date Range :</label>
                    <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                    <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('staff_delivery_report') }}')">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                    {{-- <button class="btn btn-sm btn-info" onclick="submitForm('{{ route('sales_ledger_export') }}')">Excel</button> --}}
                </div>
            </div>
        </div>
    </form>
    <div class="card-body printArea">
        <style>
            th {
                text-align: center;
            }
        </style>
        <h3 style="text-align:center;">{{translate('Ledger Report')}}</h3>
        <table class="table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>{{ translate('Order No') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer ID') }}</th>
                    <th data-breakpoints="md">{{ translate('Phone') }}</th>
                    <th data-breakpoints="md">{{ translate('Address') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Collection') }}</th>
                    <th data-breakpoints="md">{{ translate('Due') }}</th>

                </tr>
            </thead>
            <tbody>
                @php
                $total_amount = 0;
                $total_collectioin = 0;
                $totaldue = 0;
                @endphp
                @foreach ($delivery_activitys  as $key => $activity)
                @php
                $total_amount  += $activity->amount; 
                $total_collectioin += $activity->cash_collection; 
                $totaldue = $total_amount - $total_collectioin; 
                @endphp
                <tr>
                    <td>
                        {{ $key + 1 }}
                    </td>
                 
                    <td>
                        {{ $activity->order_no }}
                    </td>

                    <td>
                        {{ $activity->name }}
                    </td>

                    <td>
                        {{ $activity->customer_id }}
                    </td>
              
                    <td>
                        {{ $activity->phone }}
                    </td>

                    <td>
                        {{ $activity->address }}
                    </td>

                    <td align="right">
                        {{ $activity->amount }}
                    </td>
                    
                    <td align="right">
                        {{ $activity->cash_collection }}
                    </td>

                   <td align="right">
                        {{ $activity->amount-$activity->cash_collection  }}
                    </td>
                </tr>

                @endforeach
                <tr>
                    <td style="text-align:right;" colspan="6"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{single_price($total_amount)}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($total_collectioin)}}</b></td>
                    <td style="text-align:right;"><b>{{single_price($totaldue)}}</b></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

<script type="text/javascript">
    function submitForm(url) {
        $('#culexpo').attr('action', url);
        $('#culexpo').submit();
    }
</script>

@endsection

