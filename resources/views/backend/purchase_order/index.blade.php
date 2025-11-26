@extends('backend.layouts.app')

@section('content')
@php
    $refund_request_addon = App\Models\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="card shadow-sm">
    <form action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-lg-6 col-md-8">
                <h5 class="mb-0 h6"><i class="las la-file-invoice-dollar"></i> {{ translate('All Purchases') }}</h5>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Purchase Code') }}">
                </div>
            </div>
            <div class="col-lg-2 text-right">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="las la-filter"></i> {{ translate('Filter') }}
                </button>
            </div>
        </div>
    </form>
    
    <div class="card-body">
        <table class="table table-hover aiz-table mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>{{ translate('Purchase Order') }}</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Supplier') }}</th>
                    <th>{{ translate('Total') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $order)
                    <tr>
                        <td>{{ ($key+1) + ($data->currentPage() - 1)*$data->perPage() }}</td>
                        <td>{{ $order->purchase_no }}</td>
                        <td>{{ $order->date }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ single_price($order->total_value) }}</td>
                        <td class="text-right">
                            @if($order->status == '1')
                                <a href="{{ route('puracher_edit', $order->id) }}" class="btn btn-icon btn-soft-primary btn-circle btn-sm" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                            @endif
                            <a href="{{ route('purchase_orders_view', $order->id) }}" class="btn btn-icon btn-soft-info btn-circle btn-sm" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @if($order->status == '1')
                                <a href="javascript:void(0);" onclick="showApproveModal({{ $order->id }})" class="btn btn-icon btn-soft-success btn-circle btn-sm" title="{{ translate('Approve') }}">
                                    <i class="las la-check-circle"></i>
                                </a>
                                <a href="#" class="btn btn-icon btn-soft-danger btn-circle btn-sm confirm-delete" data-href="{{ route('orders.destroy_po', $order->id) }}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="aiz-pagination mt-3">
            {{ $data->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
    
    <!-- Approve Modal -->
    <div class="modal fade" id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">{{ translate('Approve Purchase Order') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="approve-modal-body">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{ translate('Loading...') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="las la-times"></i> {{ translate('No') }}
                    </button>
                    <button type="button" class="btn btn-success" id="approve-btn" onclick="approvePurchase()">
                        <i class="las la-check"></i> {{ translate('Yes, Approve') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    var currentPurchaseId = null;

    function showApproveModal(purchaseId) {
        currentPurchaseId = purchaseId;
        $('#approve-modal').modal('show');
        
        // Show loading spinner
        $('#approve-modal-body').html(`
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">{{ translate('Loading...') }}</span>
                </div>
            </div>
        `);
        
        // Load purchase details
        $.ajax({
            url: '{{ route("purchase_orders.get_details", ":id") }}'.replace(':id', purchaseId),
            type: 'GET',
            success: function(response) {
                displayPurchaseDetails(response);
            },
            error: function(xhr) {
                $('#approve-modal-body').html(`
                    <div class="alert alert-danger">
                        {{ translate('Failed to load purchase details. Please try again.') }}
                    </div>
                `);
            }
        });
    }

    function displayPurchaseDetails(purchase) {
        let voucherHtml = '';
        if (purchase.voucher_img) {
            voucherHtml = `
                <div class="col-md-12 mb-3">
                    <h6 class="mb-2"><strong>{{ translate('Chalan Voucher') }}:</strong></h6>
                    <a href="${purchase.voucher_url}" target="_blank">
                        <img src="${purchase.voucher_url}" class="img-fluid" style="max-height: 300px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                    </a>
                </div>
            `;
        }

        let detailsHtml = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <p><strong>{{ translate('Purchase No') }}:</strong> ${purchase.purchase_no}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>{{ translate('Date') }}:</strong> ${purchase.date}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>{{ translate('Supplier') }}:</strong> ${purchase.supplier_name}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>{{ translate('Chalan No') }}:</strong> ${purchase.chalan_no}</p>
                </div>
                <div class="col-md-12 mb-2">
                    <p><strong>{{ translate('Remarks') }}:</strong> ${purchase.remarks}</p>
                </div>
                <div class="col-md-12 mb-2">
                    <p><strong>{{ translate('Total Value') }}:</strong> <span style="width: 100px;" class="badge badge-success">${purchase.total_value_formatted}</span></p>
                </div>
                ${voucherHtml}
                <div class="col-md-12 mt-3">
                    <h6><strong>{{ translate('Product Details') }}:</strong></h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>{{ translate('Product') }}</th>
                                <th>{{ translate('Quantity') }}</th>
                                <th>{{ translate('Price') }}</th>
                                <th>{{ translate('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        if (purchase.details && purchase.details.length > 0) {
            purchase.details.forEach(function(item) {
                detailsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.qty}</td>
                        <td>${item.price_formatted}</td>
                        <td>${item.amount_formatted}</td>
                    </tr>
                `;
            });
        }

        detailsHtml += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        $('#approve-modal-body').html(detailsHtml);
    }

    function approvePurchase() {
        if (!currentPurchaseId) return;
        
        $('#approve-btn').prop('disabled', true).html('<i class="las la-spinner la-spin"></i> {{ translate("Processing...") }}');
        
        $.ajax({
            url: '{{ route("purchase_approve.index", ":id") }}'.replace(':id', currentPurchaseId),
            type: 'GET',
            success: function(response) {
                $('#approve-modal').modal('hide');
                AIZ.plugins.notify('success', '{{ translate("Purchase order approved successfully") }}');
                location.reload();
            },
            error: function(xhr) {
                $('#approve-btn').prop('disabled', false).html('<i class="las la-check"></i> {{ translate("Yes, Approve") }}');
                AIZ.plugins.notify('danger', '{{ translate("Failed to approve purchase order") }}');
            }
        });
    }

    $(document).ready(function () {
        // Reset button when modal is closed
        $('#approve-modal').on('hidden.bs.modal', function () {
            $('#approve-btn').prop('disabled', false).html('<i class="las la-check"></i> {{ translate("Yes, Approve") }}');
            currentPurchaseId = null;
        });
    });
</script>
@endsection
