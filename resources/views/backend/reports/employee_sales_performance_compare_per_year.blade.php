@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{ translate('Employee Sales Performance Compare Yearly Report') }}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <button class="btn btn-sm btn-info mx-2 my-2" onclick="printDiv()" type="button">{{ translate('Print') }}</button>

                <div class="printArea p-0">
                    <style>
                        th, td { text-align: center; font-size: 10px!important; }

                        @media print {
                            @page {
                                size: A4 portrait;
                                margin: 1cm;
                                 transform: rotate(270deg); 
                                transform-origin: left top 0;
                            }

                            body * {
                                visibility: hidden;
                            }

                            .printArea, .printArea * {
                                visibility: visible;
                            }

                            .printArea {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                            }

                            table {
                                width: 100%;
                                border-collapse: collapse;
                                font-size: 14px;
                                page-break-inside: auto;
                            }

                            tr {
                                page-break-inside: avoid;
                                page-break-after: auto;
                            }

                            th, td {
                                border: 1px solid #ddd;
                                vertical-align: middle;
                            }

                            .table-responsive {
                                overflow: visible !important;
                            }
                        }
                    </style>

                    <div class="p-1">
                        <h4>{{ translate('Employee Sales Performance Compare Yearly Report') }}</h4>
                        <h6>Year: {{ $year }}</h6>

                        <table class="table table-bordered table-striped ">
                            <thead>
                                <tr>
                                    <th>{{ translate('Employee Name') }}</th>
                                    @foreach ($months as $month)
                                        <th>{{ DateTime::createFromFormat('!m', $month)->format('M') }}</th>
                                    @endforeach
                                    <th>{{ translate('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $monthly_totals = array_fill(1, 12, ['grand_total' => 0, 'quantity' => 0]);
                                    $grand_total_amount = 0;
                                    $grand_total_quantity = 0;
                                @endphp
                                @foreach ($employeeData as $employee)
                                    @php
                                        $employee_total_amount = 0;
                                        $employee_total_quantity = 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $employee['name'] }}</td>
                                        @foreach ($months as $month)
                                            @php
                                                $month_data_amount = $employee['totals'][$month]['grand_total'] ?? 0;
                                                $month_data_quantity = $employee['totals'][$month]['quantity'] ?? 0;
                                                $monthly_totals[$month]['grand_total'] += $month_data_amount;
                                                $monthly_totals[$month]['quantity'] += $month_data_quantity;
                                                $employee_total_amount += $month_data_amount;
                                                $employee_total_quantity += $month_data_quantity;
                                            @endphp
                                            <td>
                                                qty: {{ $month_data_quantity }}<br>
                                                {{ single_price($month_data_amount, 2) }}
                                            </td>
                                        @endforeach
                                        <td>
                                            <b>Qty: {{ $employee_total_quantity }}<br>
                                            {{ single_price($employee_total_amount, 2) }}</b>
                                        </td>
                                    </tr>
                                    @php
                                        $grand_total_amount += $employee_total_amount;
                                        $grand_total_quantity += $employee_total_quantity;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td><b>Total:</b></td>
                                    @foreach ($months as $month)
                                        <td>
                                            <b>Qty: {{ $monthly_totals[$month]['quantity'] }}<br>
                                            {{ single_price($monthly_totals[$month]['grand_total'], 2) }}</b>
                                        </td>
                                    @endforeach
                                    <td>
                                        <b>qty: {{ $grand_total_quantity }}<br>
                                        {{ single_price($grand_total_amount, 2) }}</b>
                                    </td>
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

@section('script')
<script>
    function printDiv() {
        window.print();
    }
</script>
@endsection
