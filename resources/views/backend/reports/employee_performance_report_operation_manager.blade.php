@extends('backend.layouts.app')

@section('content')


<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Operation Manager') }}</h5>
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
                @foreach(\App\Models\Staff::where('role_id', 11)->get() as $executive) 
                <option value="{{$executive->user_id}}"@if($user_id == $executive->user_id) selected @endif >{{ $executive->user->name}}</option>
                @endforeach
                </select>
            </div>

           
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
        <h3 style="text-align:center;">{{translate('Operation Manager Performence Reports')}}</h3>
        <table class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>{{ translate('Period') }}</th>
                    <th>{{ translate('Executive Role') }}</th>
                    <th>{{ translate('Executive Name') }}</th>
                    <th data-breakpoints="md">{{ translate('Total Order Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Delivered Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Pending Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Damage Quantity') }}</th>
                    <th data-breakpoints="md">{{ translate('Replacement Product') }}</th>
                    <th data-breakpoints="md">{{ translate('Achievement') }}</th>
                  
                    <!-- <th data-breakpoints="md">{{ translate('Due Collection Achievement') }}</th> -->
                </tr>
            </thead>
            <tbody>

                <?php
                    $total_order_quantity_count = 0;
                    $total_delivered_qty_count = 0;
                    $pending_qty_count = 0;
                    $damage_qty_count = 0;
                    $replacement_product_count = 0;
                    $achivement_count = 0;
                    $i = 0;
                ?>
               @foreach($delivery_staffs as $key => $target)
                <tr>

                <?php
                    $total_order_quantity_count += $target->total_order_qty;
                    $total_delivered_qty_count += $target->total_delivered_qty;
                    $pending_qty_count += $target->pending_qty;
                    $damage_qty_count += $target->damage_qty;
                    $replacement_product_count += $target->replacement_product;
                    $achivement_count += $target->achivement;
                    $i ++;
                ?>

                    <td class='text-right'>
                        {{ $key + 1 }}
                    </td>

                    <td class='text-right'>
                        {{ $start_date }} to {{ $end_date }}
                    </td>

                    <td class='text-right'>
                        {{ $target->user->staff->role->name ? $target->user->staff->role->name: '' }}
                    </td>

                    <td class='text-right'>
                        {{ $target->user->name ? $target->user->name: '' }}
                    </td>

                    <td class='text-right'>
                        {{ $target->total_order_qty ? $target->total_order_qty: '0' }}
                    </td>
                    
                    <td class='text-right'>
                        {{ $target->total_delivered_qty ? $target->total_delivered_qty: '0' }}
                    </td>
                    
                    <td class='text-right'>

                        {{ $target->pending_qty ? $target->pending_qty: '0' }}

                    </td>

                    <td class='text-right'>
                    
                        {{ $target->damage_qty ? $target->damage_qty: '0' }}
                    </td>

                    <td class='text-right'>
                        {{ $target->replacement_product ? $target->replacement_product: '0' }}
                
                    </td>

                    <td class='text-right'>
                        {{ $target->achivement ? $target->achivement: '0'}}%
                    </td>

                    @endforeach
                </tr>
                
                <tr>
                    <td style="text-align:right;" colspan="4"><b>Total</b></td>
                    <td style="text-align:right;">
                        {{ number_format($total_order_quantity_count) }}
                    </td>
                    <td style="text-align:right;">
                        {{ number_format($total_delivered_qty_count) }}
                    </td>
                    <td style="text-align:right;">
                        {{ number_format($pending_qty_count) }}
                    </td>

                    
                    <td style="text-align:right;">
                        {{ number_format($damage_qty_count) }}
                    </td>
                    <td style="text-align:right;">
                        {{ number_format($replacement_product_count) }}
                    </td>
                    <td style="text-align:right;">
                    <?php
                            if($i == 0 || $achivement_count !== 0){
                                $achivement_count = $achivement_count/$i;
                            }
                            else{
                                $achivement_count = 0;
                            }
                    ?>
                        {{ number_format($achivement_count/$i,2) }}%
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

@endsection

@section('modal')
@include('modals.delete_modal')
@endsection
@section('script')
@endsection