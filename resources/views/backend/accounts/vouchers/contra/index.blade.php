@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Contra Voucher') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('contra-vouchers.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Contra Voucher')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_contra_vouchers" action="" method="GET">
        <?php
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
        ?>
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Contra Voucher') }}</h5>
            </div>
            @if(Auth::user()->user_type == 'admin')   
            <div class="col-md-3 col-xxl-3">
                <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" onchange="sort_contra_vouchers()" data-live-search="true">
                    <option value="">Select Warehouse</option>
                    @foreach (\App\Models\Warehouse::all() as $warehouse)
                    <option value="{{ $warehouse->id }}" @isset($warehouse_id) @if($warehouse->id==$warehouse_id ) selected @endif @endisset>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="col-md-3 col-xxl-3">
                <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" onchange="sort_contra_vouchers()" data-live-search="true">
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @isset($warehouse_id) @if($warehouse->id==$warehouse_id ) selected @endif @endisset>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3 col-xxl-3">
                <select name="head_code" id="head_code" class="form-control aiz-selectpicker" onchange="sort_contra_vouchers()" data-live-search="true">
                    <option value="">Select Account Head</option>
                    @foreach($coas as $coa)
                    <option value="{{ $coa->head_code }}" @isset($head_code) @if($coa->head_code==$head_code ) selected @endif @endisset>{{ $coa->head_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-xxl-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control aiz-date-range" name="date_range" data-time-picker="false" data-format="DD-MM-YYYY" data-separator=" to " autocomplete="off" oninp="alert('Helllo')" placeholder="Select Date Range" value="{{$date_range}}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="md" width="5%">#</th>
                    <th>{{ translate('VNo') }}</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Account Head') }}</th>
                    <th>{{ translate('Party Name') }}</th>
                    <th>{{ translate('Particulars') }}</th>
                    <th>{{ translate('Warehouse') }}</th>
                    <th>{{ translate('Debit') }}</th>
                    <th>{{ translate('Credit') }}</th>
                    <th>{{ translate('Rev. A/C Head') }}</th>
                    <th width="135px" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach ($contra_vouchers as $key => $contra_voucher)
                @php
                    // Fetch purchase details if this is a supplier payable transaction
                    $purchase = null;
                    $supplierName = 'N/A';
                    $purchaseNo = '';
                    
                    // Initialize order variables
                    $order = null;
                    $customerName = 'N/A';
                    $orderNo = '';
                    
                    if(($contra_voucher->coa->head_code == 5020201 || $contra_voucher->coa->head_code == 10204) && $contra_voucher->reference_no) {
                        $purchase = \App\Models\Purchase::find($contra_voucher->reference_no);
                        if($purchase) {
                            $supplier_id = $purchase->supplier_id;
                            $supplierName = \App\Models\Supplier::find($supplier_id)->name ?? 'N/A';
                            $purchaseNo = $purchase->purchase_no ?? '';
                        }
                    }
                    
                    if(($contra_voucher->coa->head_code == 3010301 || $contra_voucher->coa->head_code == 1020801 || $contra_voucher->coa->head_code == 40101 || $contra_voucher->coa->head_code == 4010101 || $contra_voucher->coa->head_code == 1020802) && $contra_voucher->reference_no) {
                        $order = \App\Models\Order::find($contra_voucher->reference_no);
                        if($order) {
                            $customer_id = $order->user_id;
                            $customerName = \App\Models\User::find($customer_id)->name ?? 'N/A';
                            $orderNo = $order->code ?? '';
                        }
                    }
                @endphp
                <tr>
                    <td>{{ ($key+1) + ($contra_vouchers->currentPage() - 1)*$contra_vouchers->perPage() }}</td>
                    <td>{{ $contra_voucher->voucher_no }}</td>
                    <td>{{ $contra_voucher->voucher_date }}</td>
                    <td>
                        @if($contra_voucher->coa)
                        {{ $contra_voucher->coa->head_name }}
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($contra_voucher->coa->head_code == 5020201 || $contra_voucher->coa->head_code == 10204)
                            {{ $supplierName }}
                        @elseif(($contra_voucher->coa->head_code == 3010301 || $contra_voucher->coa->head_code == 1020801 || $contra_voucher->coa->head_code == 40101 || $contra_voucher->coa->head_code == 4010101 || $contra_voucher->coa->head_code == 1020802) && $orderNo)
                            {{ $customerName }}
                        @elseif($contra_voucher->relvalue && $contra_voucher->reltype)
                            {{ $contra_voucher->relvalue->name }}<br>
                            ({{ $contra_voucher->reltype->name }})
                        @else
                            {{ translate('N/A') }}
                        @endif
                    </td>
                    <td>
                        @if(($contra_voucher->coa->head_code == 5020201 || $contra_voucher->coa->head_code == 10204) && $purchaseNo)
                            {{ $contra_voucher->ledger_comment }} for Purchase No: <a href="{{ route('purchase_orders_view', $purchase->id) }}" target="_blank" class="text-primary font-weight-bold">{{ $purchaseNo }}</a>
                        @elseif(($contra_voucher->coa->head_code == 3010301 || $contra_voucher->coa->head_code == 1020801 || $contra_voucher->coa->head_code == 40101 || $contra_voucher->coa->head_code == 4010101 || $contra_voucher->coa->head_code == 1020802) && $orderNo)
                            {{ $contra_voucher->ledger_comment }} for Order No: <a href="{{ route('all_orders.show', encrypt($order->id)) }}" target="_blank" class="text-primary font-weight-bold">{{ $orderNo }}</a>
                        @else
                            {{ $contra_voucher->ledger_comment }}
                        @endif
                    </td>
                    <td>
                        {{ $contra_voucher->warehouse ? $contra_voucher->warehouse->name : 'N/A' }}
                    </td>
                    <td>{{ $contra_voucher->debit }}</td>
                    <td>{{ $contra_voucher->credit }}</td>
                    <td>
                        @if($contra_voucher->rev_coa)
                        {{ $contra_voucher->rev_coa->head_name }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('contra-vouchers.show', $contra_voucher->id) }}" title="{{ __('View') }}">
                            <i class="las la-eye"></i>
                        </a>
                        @if($contra_voucher->is_approved)
                        <a class="btn btn-soft-success btn-icon btn-circle btn-sm confirm-reverse" href="javascript:void(0);" data-href="{{ route('vouchers.reverse', [$contra_voucher->voucher_no]) }}" title="{{ __('Reverse') }}">
                            <i class="las la-undo"></i>
                        </a>
                        @else
                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('contra-vouchers.edit', $contra_voucher->id) }}" title="{{ __('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" href="javascript:void(0);" data-href="{{ route('contra-vouchers.destroy', $contra_voucher->voucher_no) }}" title="{{ __('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $contra_vouchers->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
    @include('modals.reverse_modal')
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
            sort_contra_vouchers();
        });
    });

    function sort_contra_vouchers(el) {
        $('#sort_contra_vouchers').submit();
    }
</script>
@endsection
