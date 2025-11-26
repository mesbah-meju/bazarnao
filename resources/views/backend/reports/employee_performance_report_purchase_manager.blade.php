@extends('backend.layouts.app')

@section('content')


<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Purchase Manager') }}</h5>
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
        <h3 style="text-align:center;">{{translate('Purchase Manager Performence Reports')}}</h3>
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>{{ translate('Executive Role') }}</th>
                    <th>{{ translate('Executive Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Purchase Product Quantity ') }}</th>
                    <th data-breakpoints="md">{{ translate('Purchase Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Damage Product Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Damage Product Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Vendor Create') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Vendor') }}</th>
                    <th data-breakpoints="md">{{ translate('Achivement') }}</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                 <?php
                 $total_purchase_qty_count = 0;
                 $total_purchase_amount_count = 0;
                 $damage_product_qty_count = 0;
                 $damage_product_amount_count = 0;
                 $vendor_create_count = 0;
                 $total_vendor_count = 0;
                 $achivement_count = 0;
                 $i = 0;
                ?> 
               
                @foreach($delivery_staffs_main as $key => $delivery)
                <tr>
                    <?php
                        $total_purchase_qty_count += $delivery->total_purchase_qty;
                        $total_purchase_amount_count += $delivery->total_purchase_amount;
                        $damage_product_qty_count += $delivery->damage_product_qty;
                        $damage_product_amount_count += $delivery->damage_product_amount;
                        $vendor_create_count += $delivery->vendor_create;
                        $total_vendor_count += $delivery->total_vendor;
                        $achivement_count += $delivery->achivement;
                        $i++;
                    ?>

                    <td class='text-right'>
                    {{ $key + 1 }}
                    </td>


                    <td class='text-right'>
                        {{$delivery->user->staff->role->name}}
                    </td>

                    <td class='text-right'>
                    
                        {{$delivery->user->name}}
                    </td>

                    <td class='text-right'>
                        {{number_format($delivery->total_purchase_qty)}}
                    </td>
                    
                    <td class='text-right'>
                        {{number_format($delivery->total_purchase_amount)}}

                    </td>

                    <td class='text-right'>
                        {{number_format($delivery->damage_product_qty)}}

                    </td>

                    <td class='text-right'>
                         {{number_format($delivery->damage_product_amount)}}
                    </td>
                    <td class='text-right'>
                        {{number_format($delivery->vendor_create)}}
                    </td>

                    <td class='text-right'>
                        {{number_format($delivery->total_vendor)}}
                    </td>

                    <td class='text-right'>
                        {{  number_format($delivery->achivement,2) }}%               
                    </td>
                        
                    @endforeach
                </tr>
                   
                  
                
                <tr>
                    <td style="text-align:right;" colspan="3">
                        <b>Total<b>
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($total_purchase_qty_count) }}
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($total_purchase_amount_count) }}
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($damage_product_qty_count) }}
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($damage_product_amount_count) }}
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($vendor_create_count) }}
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($total_vendor_count) }}
                    </td>

                    <td style="text-align:right;">
                        {{ number_format($achivement_count/($i ?: 1),2) }}%
                    </td>

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