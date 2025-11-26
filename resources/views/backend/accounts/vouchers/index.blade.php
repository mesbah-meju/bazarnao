@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left my-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3 mb-0">{{ translate('All Vouchers') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            {{-- Optional Top Button --}}
        </div>
    </div>
</div>

<div class="card">
    <form id="sort_vouchers" action="" method="GET">
        <div class="card-header bg-light border-bottom d-flex flex-wrap align-items-end gap-3">

            <div class="flex-grow-1">
                <h5 class="mb-0">{{ translate('Filter Vouchers') }}</h5>
            </div>

            <div class="form-group mb-0">
                <label class="small text-muted">{{ translate('Warehouse') }}</label>
                <select id="warehouse" class="aiz-selectpicker select2 form-control form-control-sm" name="warehouse" data-live-search="true" onchange="sort_vouchers()">
                    <option value="">{{ translate('All Warehouses') }}</option>
                    @if(Auth::user()->user_type == 'admin')
                    @foreach (\App\Models\Warehouse::all() as $warehous)
                    <option value="{{ $warehous->id }}" @selected(isset($warehouse) && $warehouse==$warehous->id)>{{ $warehous->name }}</option>
                    @endforeach
                    @else
                    @foreach ($warehouses as $warehous)
                    <option value="{{ $warehous->id }}" @selected(isset($warehouse) && $warehouse==$warehous->id)>{{ $warehous->name }}</option>
                    @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group mb-0">
                <label class="small text-muted">{{ translate('Fiscal Year') }}</label>
                <input type="text" class="form-control form-control-sm" id="search" name="search" value="{{ $sort_search ?? '' }}" placeholder="{{ translate('Type & Enter') }}">
            </div>

            <div class="form-group mb-0">
                <label class="small text-muted">{{ translate('Voucher No') }}</label>
                <input type="text" class="form-control form-control-sm" name="voucher_no" value="{{ request('voucher_no') }}" placeholder="{{ translate('Enter Voucher No') }}" onkeypress="if(event.keyCode == 13) { sort_vouchers(); }">
            </div>

        </div>
    </form>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped aiz-table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th>{{ translate('Invoice No') }}</th>
                        <th>{{ translate('Voucher No') }}</th>
                        <th>{{ translate('Account Name') }}</th>
                        <th>{{ translate('Remark') }}</th>
                        <th>{{ translate('Debit') }}</th>
                        <th>{{ translate('Credit') }}</th>
                        <th class="text-right" width="10%">{{ translate('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vouchers as $key => $voucher)
                    <tr>
                        <td>{{ ($key+1) + ($vouchers->currentPage() - 1) * $vouchers->perPage() }}</td>
                        <td>{{ $voucher->reference_no }}</td>
                        <td>{{ $voucher->voucher_no }}</td>
                        <td>{{ $voucher->coa ? $voucher->coa->head_name : '-' }}</td>
                        <td>{{ $voucher->narration }}</td>
                        <td>{{ number_format($voucher->debit_amnt, 2) }}</td>
                        <td>{{ number_format($voucher->credit_amnt, 2) }}</td>
                        <td class="text-right">
                            @php
                            $view_route = match($voucher->voucher_type) {
                            'DV' => route('debit-vouchers.show', $voucher->id),
                            'CV' => route('credit-vouchers.show', $voucher->id),
                            'CT' => route('contra-vouchers.show', $voucher->id),
                            default => route('journal-vouchers.show', $voucher->id),
                            };
                            @endphp
                            <a href="{{ $view_route }}" class="btn btn-icon btn-soft-primary btn-circle btn-sm" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a href="javascript:void(0);" data-href="{{ route('vouchers.approve', [$voucher->voucher_no, 'active']) }}" class="btn btn-icon btn-soft-success btn-circle btn-sm confirm-approval" title="{{ translate('Approve') }}">
                                <i class="las la-check-square"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
        <div class="aiz-pagination p-3">
            {{ $vouchers->appends(request()->input())->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@section('modal')
@include('modals.approve_modal')
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function sort_vouchers() {
        $('#sort_vouchers').submit();
    }

    $(document).ready(function() {
        $('#search').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                sort_vouchers();
            }
        });

        // $(document).on('click', '.confirm-approval', function(e) {
        //     e.preventDefault(); 
        //     var approveUrl = $(this).data('href');

        //     Swal.fire({
        //         title: "{{ translate('Are you sure you want to approve this voucher?') }}",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: "{{ translate('Yes, approve it!') }}"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             window.location.href = approveUrl;
        //         }
        //     });
        // });

        $(document).on('click', '.confirm-approval', function(e) {
            e.preventDefault();
            var approveUrl = $(this).data('href');
            var button = $(this);
            var row = button.closest('tr');

            Swal.fire({
                title: "{{ translate('Are you sure you want to approve this voucher?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ translate('Yes, approve it!') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: approveUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                });

                                AIZ.plugins.notify('success', response.message);
                            } else {
                                AIZ.plugins.notify('danger', response.message);
                            }
                        },
                        error: function(xhr) {
                            AIZ.plugins.notify('danger', 'Something went wrong. Please try again.');
                        }
                    });
                }
            });
        });



    });
</script>
@endsection