@extends('backend.layouts.app')

@section('content')


<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header row gutters-5">

            <div class="col-md-3">
                <label>Filter By Employee Executive Role:</label>
                <select name="role" id="role" class="form-control">
                    <option value="">Select Role</option>

                    @foreach(\App\Models\Role::whereBetween('id',[9, 14])->get() as $role) 
                            <option value="{{ $role->id }}">{{ $role->name }}</option>          
                        @endforeach
                </select>
            </div>

            <div class="col-lg-3">
                <div class="form-group mb-0">                    
                    <label>Date Range :</label>
                    <input type="date" name="start_date" class="form-control" >
                    <input type="date" name="end_date" class="form-control" >
                     {{-- <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}"> --}}
                </div>
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
        <h3 style="text-align:center;">{{translate('Employee Performence Reports')}}</h3>
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
                    <th data-breakpoints="md">{{ translate('Total Due') }}</th>
                    <!-- <th data-breakpoints="md">{{ translate('Due Collection Achievement') }}</th> -->
                </tr>
            </thead>
            <tbody> 
                
                <tr>
                    <td style="text-align:right;" colspan="4"><b>Total</b></td>
                    <td style="text-align:right;"><b></b></td>
                    <td style="text-align:right;"><b></b></td>
                    <td style="text-align:right;">
                    
                    </td>

                    
                    <td style="text-align:right;"><b></b></td>
                    <td style="text-align:right;"><b></b></td>
                    <td style="text-align:right;">
               
                    </td>
                    <td style="text-align:right;"><b></b></td>
                    <td style="text-align:right;"><b></b></td>
                 
                </tr>
            </tbody>
        </table>
        <div class="container" style="text-align: center;">
            <p>No Data Found</p>
        </div>

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