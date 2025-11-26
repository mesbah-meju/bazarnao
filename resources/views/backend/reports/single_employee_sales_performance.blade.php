@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Single Employee Sales Performance Report')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form id="prowasales" action="{{ route('single_employee_sales_performance.index') }}" method="get">
                    <div class="form-group row">
                        {{-- <div class="col-md-3">
                            <label>Filter By Employee :</label>
                            <select class="form-control" name="user_id" data-live-search="true" id="user_id">
                                <option value="">Select One</option>
                                @foreach(\App\Models\Staff::whereBetween('role_id', [9, 14])->get() as $executive)      
                                    <option value="{{$executive->user_id}}" @if($user_id == $executive->user_id) selected @endif>{{ $executive->user->name }}</option>
                                @endforeach
                                <option value="">No Define</option>
                            </select>
                        </div> --}}
                        <div class="col-md-3">
                            <label>Filter By Employee:</label>
                            <select class="form-control aiz-selectpicker select2" name="user_id" data-live-search="true" id="user_id" data-live-search="true">
                                <option value="">All</option>
                                @foreach(\App\Models\Staff::whereBetween('role_id', [9, 14])->get() as $executive)
                                    <option value="{{ $executive->user_id }}" @if($user_id == $executive->user_id) selected @endif>
                                        {{ $executive->user->name }}
                                    </option>
                                @endforeach
                                {{-- <option value="" @if($user_id == '') selected @endif>No Define</option> --}}
                            </select>
                        </div>
                        
                        

                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                                <a href="{{ route('single_employee_sales_performance.index', array_merge(request()->query(), ['type' => 'excel'])) }}" target="_blank" class="btn btn-sm btn-success" type="button">Excel</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th {text-align:center;}
                    </style>

                    <div class="container">
                        <h2>Single Employee Sales Performance Report</h2>
                        <h6>Employee's Name: <span>{{ $selectedUserName }}</span></h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2">Month</th>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <th colspan="1">{{ $year }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($months as $monthData)
                                <tr>
                                    <td>{{ $monthData['name'] }}</td>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <td class="text-right">Qty: {{ $monthData[$year]['quantity'] ?? 0 }} <br> {{ single_price($monthData[$year]['grand_total'] ?? 0, 2) }}</td>
                                    @endfor
                                </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align:right;" colspan="1"><b>Total</b></td>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <td style="text-align:right;"><b>Qty: {{ $totals[$year]['quantity'] ?? 0 }} <br> {{ single_price($totals[$year]['grand_total'] ?? 0, 2) }}</b></td>
                                    @endfor
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

@push('scripts')
<script>
    function submitForm() {
        document.getElementById('prowasales').submit();
    }

    function printDiv() {
        var printContents = document.querySelector('.printArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    // Include Select2 initialization if needed
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush
