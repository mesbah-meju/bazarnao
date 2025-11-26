@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Journal Voucher') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('journal-vouchers.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Journal Voucher')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_journal_vouchers" action="" method="GET">
        <?php
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $warehouses = \App\Models\Warehouse::whereIn('id', $warehousearray)->get();
        ?>
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Journal Voucher') }}</h5>
            </div>
            @if(Auth::user()->user_type == 'admin')   
            <div class="col-md-2 col-xxl-2">
                <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" onchange="sort_journal_vouchers()" data-live-search="true">
                    <option value="">Select Warehouse</option>
                    @foreach (\App\Models\Warehouse::all() as $warehouse)
                    <option value="{{ $warehouse->id }}" @isset($warehouse_id) @if($warehouse->id==$warehouse_id ) selected @endif @endisset>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="col-md-2 col-xxl-2">
                <select name="warehouse_id" id="warehouse_id" class="form-control aiz-selectpicker" onchange="sort_journal_vouchers()" data-live-search="true">
                    <option value="">Select Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @isset($warehouse_id) @if($warehouse->id==$warehouse_id ) selected @endif @endisset>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2 col-xxl-2">
                <select name="head_code" id="head_code" class="form-control aiz-selectpicker" onchange="sort_journal_vouchers()" data-live-search="true">
                    <option value="">Select Account Head</option>
                    @foreach($coas as $coa)
                    <option value="{{ $coa->head_code }}" @isset($head_code) @if($coa->head_code==$head_code ) selected @endif @endisset>{{ $coa->head_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-xxl-2">
                <select name="party_id" id="party_id" class="form-control aiz-selectpicker" onchange="sort_journal_vouchers()" data-live-search="true">
                    <option value="">Select Party Name</option>
                    @foreach($parties as $party)
                    <option value="{{ $party->id }}" @isset($party_id) @if($party->id==$party_id ) selected @endif @endisset>
                        {{ $party->name }} @if($party->sub_type_id != null) ({{ $party->subtype->name }}) @endif
                    </option>
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
                @foreach ($journal_vouchers as $key => $journal_voucher)
                @php
                    // Fetch purchase details if this is a supplier payable transaction
                    $purchase = null;
                    $supplierName = 'N/A';
                    $purchaseNo = '';
                    
                    // Initialize order variables
                    $order = null;
                    $customerName = 'N/A';
                    $orderNo = '';
                    
                    if(($journal_voucher->coa->head_code == 5020201 || $journal_voucher->coa->head_code == 10204) && $journal_voucher->reference_no) {
                        $purchase = \App\Models\Purchase::find($journal_voucher->reference_no);
                        if($purchase) {
                            $supplier_id = $purchase->supplier_id;
                            $supplierName = \App\Models\Supplier::find($supplier_id)->name ?? 'N/A';
                            $purchaseNo = $purchase->purchase_no ?? '';
                        }
                    }
                    
                    if(($journal_voucher->coa->head_code == 3010301 || $journal_voucher->coa->head_code == 1020801 || $journal_voucher->coa->head_code == 40101 || $journal_voucher->coa->head_code == 4010101 || $journal_voucher->coa->head_code == 1020802) && $journal_voucher->reference_no) {
                        $order = \App\Models\Order::find($journal_voucher->reference_no);
                        if($order) {
                            $customer_id = $order->user_id;
                            $customerName = \App\Models\User::find($customer_id)->name ?? 'N/A';
                            $orderNo = $order->code ?? '';
                        }
                    }
                @endphp
                <tr>
                    <td>{{ ($key+1) + ($journal_vouchers->currentPage() - 1)*$journal_vouchers->perPage() }}</td>
                    <td>{{ $journal_voucher->voucher_no }}</td>
                    <td>{{ $journal_voucher->voucher_date }}</td>
                    <td>
                        @if($journal_voucher->coa)
                            {{ $journal_voucher->coa->head_name }}
                            @if($journal_voucher->subcode != null)
                                <br>({{ optional($journal_voucher->subcode)->name }})
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($journal_voucher->coa->head_code == 5020201 || $journal_voucher->coa->head_code == 10204)
                            {{ $supplierName }}
                        @elseif(($journal_voucher->coa->head_code == 3010301 || $journal_voucher->coa->head_code == 1020801 || $journal_voucher->coa->head_code == 40101 || $journal_voucher->coa->head_code == 4010101 || $journal_voucher->coa->head_code == 1020802) && $orderNo)
                            {{ $customerName }}
                        @elseif($journal_voucher->relvalue && $journal_voucher->reltype)
                            {{ $journal_voucher->relvalue->name }}<br>
                            ({{ $journal_voucher->reltype->name }})
                        @else
                            {{ translate('N/A') }}
                        @endif
                    </td>
                    <td>
                        @if(($journal_voucher->coa->head_code == 5020201 || $journal_voucher->coa->head_code == 10204) && $purchaseNo)
                            {{ $journal_voucher->ledger_comment }} for Purchase No: <a href="{{ route('purchase_orders_view', $purchase->id) }}" target="_blank" class="text-primary font-weight-bold">{{ $purchaseNo }}</a>
                        @elseif(($journal_voucher->coa->head_code == 3010301 || $journal_voucher->coa->head_code == 1020801 || $journal_voucher->coa->head_code == 40101 || $journal_voucher->coa->head_code == 4010101 || $journal_voucher->coa->head_code == 1020802) && $orderNo)
                            {{ $journal_voucher->ledger_comment }} for Order No: <a href="{{ route('all_orders.show', encrypt($order->id)) }}" target="_blank" class="text-primary font-weight-bold">{{ $orderNo }}</a>
                        @else
                            {{ $journal_voucher->ledger_comment }}
                        @endif
                    </td>
                    <td>
                        {{ $journal_voucher->warehouse ? $journal_voucher->warehouse->name : 'N/A' }}
                    </td>
                    <td>{{ $journal_voucher->debit }}</td>
                    <td>{{ $journal_voucher->credit }}</td>
                    <td>
                        @if($journal_voucher->rev_coa)
                        {{ $journal_voucher->rev_coa->head_name }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('journal-vouchers.show', $journal_voucher->id) }}" title="{{ __('View') }}">
                            <i class="las la-eye"></i>
                        </a>
                        @if($journal_voucher->is_approved)
                        <a class="btn btn-soft-success btn-icon btn-circle btn-sm confirm-reverse" href="javascript:void(0);" data-href="{{ route('vouchers.reverse', [$journal_voucher->voucher_no]) }}" title="{{ __('Reverse') }}">
                            <i class="las la-undo"></i>
                        </a>
                        @else
                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('journal-vouchers.edit', $journal_voucher->id) }}" title="{{ __('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" href="javascript:void(0);" data-href="{{ route('journal-vouchers.destroy', $journal_voucher->voucher_no) }}" title="{{ __('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $journal_vouchers->appends(request()->input())->links() }}
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
            sort_journal_vouchers();
        });
    });

    function sort_journal_vouchers(el) {
        $('#sort_journal_vouchers').submit();
    }
</script>
@endsection
