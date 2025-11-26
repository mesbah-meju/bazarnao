@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Cash Transfer') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('cash-transfers.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Cash Transfer')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_credit_vouchers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Cash Transfer') }}</h5>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker" name="from_warehouse_id" id="from_warehouse_id" data-live-search="true">
                        <option value="">{{ translate('From Warehouse') }}</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $from_warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker" name="to_warehouse_id" id="to_warehouse_id" data-live-search="true">
                        <option value="">{{ translate('To Warehouse') }}</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $to_warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="date" class="form-control form-control-sm" name="from_date" value="{{ $from_date }}" placeholder="{{ translate('From Date') }}">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="date" class="form-control form-control-sm" name="to_date" value="{{ $to_date }}" placeholder="{{ translate('To Date') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>

            <div class="col-md-1">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-sm btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">
        <div class="mb-3">
            <button class="btn btn-md btn-info mr-2" onclick="downloadPDF()" type="button">
                <i class="las la-file-pdf"></i> {{ translate('PDF') }}
            </button>
            <button class="btn btn-md btn-success" onclick="downloadExcel()" type="button">
                <i class="las la-file-excel"></i> {{ translate('Excel') }}
            </button>
        </div>
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('Voucher No') }}</th>
                    <th data-breakpoints="md">{{ translate('Voucher Date') }}</th>
                    <th data-breakpoints="md">{{ translate('From wearhouse') }}</th>
                    <th data-breakpoints="md">{{ translate('To wearhouse') }}</th>
                    <th data-breakpoints="md">{{ translate('Remark') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                    <th class="text-right" width="15%">{{translate('options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transfers as $key => $transfer)
                <tr>
                    <td>{{ ($key+1) + ($transfers->currentPage() - 1)*$transfers->perPage() }}</td>
                    <td>{{ $transfer->voucher_no }}</td>
                    <td>{{ $transfer->voucher_date }}</td>
                    <td>{{ getWearhouseName($transfer->from_warehouse_id) }}</td>
                    <td>{{ getWearhouseName($transfer->to_warehouse_id) }}</td>
                    <td>{{ $transfer->remarks }}</td>
                    <td>{{ $transfer->amount }}</td>
                    <td>
                        @if($transfer->status == '0')
                            <span class="badge badge-warning w-auto">{{ translate('Pending') }}</span>
                        @else
                            <span class="badge badge-success w-auto">{{ translate('Approved') }}</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($transfer->status =='0')
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm confirm-approve" href="javascript:void(0);" data-href="{{ route('cash-transfers.approve', [$transfer->voucher_no, 'active']) }}" title="{{ translate('Approve') }}">
                                <i class="las la-check-circle"></i>
                            </a>
                            <a href="{{route('cash-transfers.edit', $transfer->id)}}" class="btn btn-soft-warning btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" href="javascript:void(0);" data-href="{{route('cash-transfers.destroy', $transfer->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        @else
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm confirm-reverse" href="javascript:void(0);" data-href="{{ route('cash-transfers.reverse', $transfer->voucher_no) }}" title="{{ translate('Reverse') }}">
                                <i class="las la-undo"></i>
                            </a>
                        @endif
                        {{-- <a href="{{route('cash-transfers.show', $transfer->id)}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('View') }}">
                            <i class="las la-eye"></i>
                        </a> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $transfers->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
    @include('modals.reverse_modal')
    @include('modals.approve_modal')
@endsection

@section('script')
<script>
    function downloadPDF() {
        // Build URL with parameters
        var url = '{{ route("cash-transfers.index") }}?type=pdf';
        
        // Get form values
        var fromWarehouseId = document.querySelector('select[name="from_warehouse_id"]').value;
        var toWarehouseId = document.querySelector('select[name="to_warehouse_id"]').value;
        var fromDate = document.querySelector('input[name="from_date"]').value;
        var toDate = document.querySelector('input[name="to_date"]').value;
        var search = document.querySelector('input[name="search"]').value;

        // Add filters to URL
        if (fromWarehouseId) {
            url += '&from_warehouse_id=' + fromWarehouseId;
        }
        if (toWarehouseId) {
            url += '&to_warehouse_id=' + toWarehouseId;
        }
        if (fromDate) {
            url += '&from_date=' + fromDate;
        }
        if (toDate) {
            url += '&to_date=' + toDate;
        }
        if (search) {
            url += '&search=' + search;
        }

        // Open in new window
        window.open(url, '_blank');
    }

    function downloadExcel() {
        // Build URL with parameters
        var url = '{{ route("cash-transfers.index") }}?type=excel';
        
        // Get form values
        var fromWarehouseId = document.querySelector('select[name="from_warehouse_id"]').value;
        var toWarehouseId = document.querySelector('select[name="to_warehouse_id"]').value;
        var fromDate = document.querySelector('input[name="from_date"]').value;
        var toDate = document.querySelector('input[name="to_date"]').value;
        var search = document.querySelector('input[name="search"]').value;

        // Add filters to URL
        if (fromWarehouseId) {
            url += '&from_warehouse_id=' + fromWarehouseId;
        }
        if (toWarehouseId) {
            url += '&to_warehouse_id=' + toWarehouseId;
        }
        if (fromDate) {
            url += '&from_date=' + fromDate;
        }
        if (toDate) {
            url += '&to_date=' + toDate;
        }
        if (search) {
            url += '&search=' + search;
        }

        // Redirect to download
        window.location.href = url;
    }
</script>
@endsection
