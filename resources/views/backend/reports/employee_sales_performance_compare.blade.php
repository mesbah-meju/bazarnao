@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h3 class="h4">{{ translate('Employee Sales Performance Compare Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('employee_sales_performance_compare.index') }}" method="GET">
                    <div class="card-header row gutters-5">
                        <div class="col-md-3">
                            <label>Filter By Employee :</label>
                            <select class="aiz-selectpicker select2" name="user_id[]" id="user_id"  data-live-search="true" multiple>
                                @foreach($users as $executive)      
                                    <option value="{{ $executive->userId }}" @if(is_array(request()->input('user_id')) && in_array($executive->userId, request()->input('user_id'))) selected @endif>{{ $executive->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto">
                            <div class="form-group mb-0">
                                <button class="btn btn-sm btn-primary" type="submit">{{ translate('Filter') }}</button>
                                <button class="btn btn-sm btn-info mx-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="printArea">
                    <style>
                        th, td { text-align: center; }
                        table {
                            width: 100%;
                           font-size: 18px;
                        }
                    </style>

                    <div class="container">
                        <h4>{{ translate('Employee Sales Performance Compare Report') }}</h2>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ translate('Employee Name') }}</th>
                                    @for ($year = $currentYear; $year >= $currentYear - 2; $year--)
                                        <th>
                                            {{-- <a href="{{ route('employee_sales_performance_compare_per_year.index', ['year' => $year, 'user_id' => request()->input('user_id')]) }}" target="_blank">
                                                {{ $year }}
                                            </a> --}}
                                            <a href="{{ route('employee_sales_performance_compare_per_year.index', array_merge(request()->query(), ['year' => $year])) }}" target="_blank">
                                                {{ $year }}
                                            </a>
                                            
                                        </th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php $total_amount = 0; $total_quantity = 0; @endphp
                                @foreach ($employeeData as $employee)
                                    @php 
                                        $total_amount += array_sum(array_column($employee['totals'], 'grand_total')); 
                                        $total_quantity += array_sum(array_column($employee['totals'], 'quantity'));
                                    @endphp
                                    <tr>
                                        <td>{{ $employee['name'] }}</td>
                                        @foreach ($employee['totals'] as $total)
                                            <td class="text-right">
                                                Qty: {{ $total['quantity'] }}<br>
                                                {{ single_price($total['grand_total'], 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align:right;"><b>Total:</b></td>
                                    @foreach ($employeeData[0]['totals'] as $year => $yearTotal)
                                        @php
                                            $total_amount_year = 0;
                                            $total_quantity_year = 0;
                                            foreach ($employeeData as $employee) {
                                                $total_amount_year += $employee['totals'][$year]['grand_total'];
                                                $total_quantity_year += $employee['totals'][$year]['quantity'];
                                            }
                                        @endphp
                                        <td style="text-align:right;">
                                            <b>Qty: {{ $total_quantity_year }}<br>
                                            {{ single_price($total_amount_year, 2) }}</b>
                                        </td>
                                    @endforeach
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
    function printDiv() {
        var printContents = document.querySelector('.printArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
