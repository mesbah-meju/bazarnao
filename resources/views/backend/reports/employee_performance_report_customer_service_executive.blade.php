@extends('backend.layouts.app')

@section('content')


<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Customer Service Executive') }}</h5>
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
             
               
            <!-- <div class="col-md-3">
                <label>Filter By Employee :</label>
                <select class="form-control" name="user_id" id="user_id">
                <option value="">Select One</option>
                @foreach(\App\Models\Staff::where('role_id', 14)->get() as $executive) 
   
                <option value="{{$executive->user_id}}"@if($user_id == $executive->user_id) selected @endif >{{ $executive->user->name}}</option>
                @endforeach
                </select>
            </div> -->

           
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button class="btn btn-sm btn-primary" onclick="submitForm ('{{ route('employee_performance.index') }}')">{{ translate('Filter') }}</button>
                    <button class="btn btn-sm btn-info" onclick="printDiv()" type="button">{{ translate('Print') }}</button>
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
        <h3 style="text-align:center;">{{translate('Customer Service Executive Performence Reports')}}</h3>
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>{{ translate('Month') }}</th>
                    <th>{{ translate('Executive Role') }}</th>
                    <th>{{ translate('Executive Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Sales Target') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Sales') }}</th>
                    <th data-breakpoints="md">{{ translate('Sales Achivement') }}</th>
                    <th data-breakpoints="md">{{ translate('Target Customer') }}</th>
                    <th data-breakpoints="md">{{ translate('Total New Customer') }}</th>
                    <th data-breakpoints="md">{{ translate('Customer Achievement') }}</th>
                    <th data-breakpoints="md">{{ translate('Recovery Target') }}</th>
                    <th data-breakpoints="md">{{ translate('Monthly Due') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Due') }}</th>

                </tr>
            </thead>
            <tbody>

                <?php
                    $total_sales_target_count = 0;
                    $total_sales_count = 0;
                    $total_sales_acheivement_count = 0;
                    $total_target_new_customer_count = 0;
                    $total_new_customer_count = 0;
                    $total_customer_achievement_count = 0;
                    $total_recovery_target_count = 0;
                    $total_due_collection_count = 0;
                    $total_due_collection_achievement_count = 0;
                    $monthly_due_count = 0;
                    $i = 0;
                ?>
               @foreach($targets as $key => $target)
                <tr>

                <?php
                    $total_sales_target_count += $target->total_target;
                    $total_sales_count += $target->total_sales;
                    $total_sales_acheivement_count += $target->sales_achievement;
                    $total_target_new_customer_count += $target->total_target_customer;
                    $total_new_customer_count += $target->customer_count;
                    $total_customer_achievement_count += $target->customer_achivement;
                    $total_recovery_target_count += $target->total_recovery_target;
                    $total_due_collection_count += $target->totaldue;
                    $total_due_collection_achievement_count += $target->total_due_collection;
                    $monthly_due_count += $target->monthlyTotaldue;
                    
                    $i ++;
                ?>

                    <td class='text-right'>
                    {{ $key + 1 }}
                    </td>

                    <td class='text-right'>
                    {{ $target->month ? $target->month: '' }}
                    </td>

                    <td class='text-right'>
                    {{ $target->user->staff->role->name ? $target->user->staff->role->name: '' }}
                    </td>

                    <td class='text-right'>
                    {{ $target->user->name ? $target->user->name: '' }}
                    </td>

                    <td class='text-right'>
                    {{ isset($target->total_target) ? number_format($target->total_target) : '0' }}
                    </td>
                    
                    <td class='text-right'>

                   
                    {{ isset($target->total_sales) ? number_format($target->total_sales,2) : '0' }}
                    </td>
                    
                    <td class='text-right'>

                    {{ isset($target->sales_achievement) ? number_format($target->sales_achievement,2) : '0' }}%
                    </td>

                    <td class='text-right'>
                    {{ isset($target->total_target_customer) ? number_format($target->total_target_customer) : '0' }}
                    </td>

                    <td class='text-right'>
                   
                    {{ isset($target->customer_count) ? number_format($target->customer_count) : '0' }}
                    </td>

                    <td class='text-right'>
                    <?php
                        $target->customer_achivement = $target->customer_achivement;
                    ?>
                    {{ isset($target->customer_achivement) ? number_format($target->customer_achivement) : '0'}}%
                    </td>


                    <td class='text-right'>
                    {{ isset($target->total_recovery_target) ? number_format($target->total_recovery_target) : '0' }}
                    </td>
                    

                    <td class='text-right'>
                        {{ isset($target->monthlyTotaldue) ? number_format($target->monthlyTotaldue,2) : '0' }}
                    </td>
                    
                    <td class='text-right'>
                        {{ isset($target->totaldue) ? number_format($target->totaldue) : '0' }}
                    </td>
                    
                   
                    <!-- <td class='text-right'>
                        {{ isset($target->total_due_collection) ? number_format($target->total_due_collection) : '0' }}%
                    </td> -->
                    @endforeach
                </tr>
                
                <tr>
                    <td style="text-align:right;" colspan="4"><b>Total</b></td>
                    <td style="text-align:right;"><b>{{ number_format($total_sales_target_count) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($total_sales_count,2) }}</b></td>
                    <td style="text-align:right;">
                        <b>{{ number_format($total_sales_acheivement_count/($i ?: 1),2) }}%</b>

                    </td>

                    
                    <td style="text-align:right;"><b>{{ number_format($total_target_new_customer_count) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($total_new_customer_count) }}</b></td>
                    <td style="text-align:right;">
                         <b>{{ number_format($total_customer_achievement_count/($i ?: 1),2) }}%</b>
                    </td>
                    <td style="text-align:right;"><b>{{ number_format($total_recovery_target_count) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($monthly_due_count,2) }}</b></td>
                    <td style="text-align:right;"><b>{{ number_format($total_due_collection_count) }}</b></td>
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

<script>
        document.getElementById("exportButton").addEventListener("click", function() {
            const table = document.getElementById("myTable");
            let csvContent = "data:text/xlsx;charset=utf-8,";

            for (const row of table.rows) {
                const rowData = Array.from(row.cells).map(cell => cell.innerText);
                const csvRow = rowData.join(",");
                csvContent += csvRow + "\n";
            }

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "table_data.xlsx");
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