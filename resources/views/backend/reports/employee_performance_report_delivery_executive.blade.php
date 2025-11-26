@extends('backend.layouts.app')

@section('content')


<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Delivery Executive') }}</h5>
            </div>

            <div class="col-md-3">
                <label>Filter By Employee Executive Role:</label>
                <select name="role" id="role" class="form-control">
                    <option value="">Select Role</option>

                    @foreach(\App\Models\Role::whereBetween('id',[9, 14])->get() as $role) 
                            <option value="{{ $role->id }}"@if($role_id == $role->id) selected @endif >{{ $role->name }}</option>          
                        @endforeach
                </select>
            </div>

            <div class="col-lg-3">
                <div class="form-group mb-0">                    
                    <label>Date Range :</label>
                    <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                    <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                     {{-- <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}"> --}}
                </div>
            </div>
             
               
            <div class="col-md-3">
                <label>Filter By Employee :</label>
                <select class="form-control" name="user_id" id="user_id">
                <option value="">Select One</option>
                @foreach($delivery_staffs as $executive)      
                <option value="{{$executive->user_id}}"@if($user_id == $executive->user_id) selected @endif >{{ $executive->user->name}}</option>
                @endforeach
                </select>
            </div>

           
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('employee_performance.index2') }}')">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>

                    <!-- <button class="btn btn-sm btn-success" id="exportButton">Excel</button> -->
                    <!-- <button class="btn btn-sm btn-info" onclick="submitForm('')">Excel</button> -->
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
        <h3 style="text-align:center;">{{translate('Delivery Executive Performence Reports')}}</h3>
        <table class="table table-bordered" style="width:100%" id="myTable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>{{ translate('Executive Role') }}</th>
                    <th>{{ translate('Executive Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Order Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Delivered Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Pending Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Cash Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Achivement') }}</th>
                    <!-- <th data-breakpoints="md">{{ translate('Due Collection Achievement') }}</th> -->
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php
                    $total_order_quantity_count = 0;
                    $delivered_qty = 0;
                    $pending_qty_count = 0;
                    $cash_balance_count = 0;
                    $achivement_count = 0;
                    $i = 0;
                ?>
               
                @foreach($delivery_staffs_main as $key => $delivery)
                <tr>
                    <?php

                        $total_order_quantity_count += $delivery->total_order_quantity;
                        $delivered_qty += $delivery->delivered_qty;
                        $pending_qty_count += $delivery->pending_qty;
                        $cash_balance_count += $delivery->cash_balance;
                        $achivement_count += $delivery->achivement;
                        $i ++;

                    ?>

                    <td class='text-right'>
                    {{ $key + 1 }}
                    </td>


                    <td class='text-right'>
                        <!-- {{ $delivery->role_name}} -->
                        {{$delivery->user->staff->role->name}}
                    </td>

                    <td class='text-right'>
                    
                        {{$delivery->user->name}}
                    </td>

                    <td class='text-right'>
                        {{$delivery->total_order_quantity}}
                    </td>
                    
                    <td class='text-right'>
                        {{$delivery->delivered_qty}}
                    </td>

                    <td class='text-right'>
                        {{$delivery->pending_qty}}
                    </td>

                    <td class='text-right'>
                        {{number_format($delivery->cash_balance,2)}}

                    </td>
                    <td class='text-right'>
                        {{$delivery->achivement}}%
                    </td>
                        
                    @endforeach
                </tr>
                   
                  
                
                <tr>
                    <td style="text-align:right;" colspan="3"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{ number_format($total_order_quantity_count) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($delivered_qty) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($pending_qty_count) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($cash_balance_count) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($achivement_count/$i, 2) }}%</b></td>

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

<!-- <script>
        document.getElementById("exportButton").addEventListener("click", function() {
            const table = document.getElementById("myTable");
            let csvContent = "data:text/csv;charset=utf-8,";

            for (const row of table.rows) {
                const rowData = Array.from(row.cells).map(cell => cell.innerText);
                const csvRow = rowData.join(",");
                csvContent += csvRow + "\n";
            }

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "delivery_Executive_Performance_Report.csv");
            document.body.appendChild(link);
            link.click();
        });
    </script> -->

    <!-- <script>
    document.getElementById("exportButton").addEventListener("click", function() {
        const table = document.getElementById("myTable");
        let csvContent = "data:text/csv;charset=utf-8,";

        for (const row of table.rows) {
            const rowData = Array.from(row.cells).map(cell => {
                // Properly escape cell content
                return '"' + encodeURIComponent(cell.innerText) + '"';
            });

            const csvRow = rowData.join(",");
            csvContent += csvRow + "\n";
        }

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "delivery_Executive_Performance_Report.csv");
        document.body.appendChild(link);
        link.click();
    });
</script> -->

<script>
    document.getElementById("exportButton").addEventListener("click", function() {
        const table = document.getElementById("myTable");
        let csvContent = "data:text/csv;charset=utf-8,";

        for (const row of table.rows) {
            const rowData = Array.from(row.cells).map(cell => {
                // Properly escape cell content and wrap in double quotes
                return '"' + cell.innerText.replace(/"/g, '""') + '"';
            });

            const csvRow = rowData.join(",");
            csvContent += csvRow + "\n";
        }

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "delivery_Executive_Performance_Report.csv");
        document.body.appendChild(link);
        link.click();
    });
</script>



@endsection

@section('modal')
@include('modals.delete_modal')
@endsection
@section('script')
@endsection