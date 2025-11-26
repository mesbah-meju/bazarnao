@extends('backend.layouts.staff')
<style>
    tr,
    th,
    td {
        padding: 3px !important;
    }

    th {
        background: #AE3C86;
        color: #fff;
        font-weight: bold
    }

    li.nav-item {
        width: 100%;
    }

    .navbar-nav {
        width: 100%;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/2.4.85/css/materialdesignicons.css"
    rel="stylesheet" />

@section('content')
    <div class="row gutters-10">
        <div class="col-lg-12">

            <div id="accordion">
                @include('backend.staff_panel.purchase_manager.purchase_manager_nav')
                <div class="card border-bottom-0">

                    <div class=" card-body">
                        <form id="culexpo" action="{{ route('purchase_list.index') }}" method="GET">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label>Purchase Start Date :</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                                </div>
                                <div class="col-md-3">
                                    <label>Purchase End Date :</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                                </div>

                                @if(Auth::user()->staff->role->name == 'Manager')
                                <div class="col-md-2 ml-auto">
                                    <label>Warehouse:</label>
                                    <select id="warehouse" class="form-control aiz-selectpicker" name="warehouse">
                                        <option value=''>Filter by Warehouse</option>
                                        @foreach (\App\Models\Warehouse::all() as $key => $warehous)
                                            <option @if ($warehouse == $warehous->id) selected @endif value="{{ $warehous->id }}">
                                                {{ $warehous->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="col-md-2 mt-4">
                                    <button class="btn btn-primary mt-md-2">{{ translate('Filter') }}</button>
                                </div>
                            </div>
                        </form>


                        <table class="table aiz-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-breakpoints="md">{{ translate('Purchase Date') }}</th>
                                    {{-- <th data-breakpoints="md">{{ translate('Expired Date') }}</th> --}}
                                    <th data-breakpoints="md">{{ translate('Purchase Order') }}</th>
                                    <th data-breakpoints="md">{{ translate('Supplier') }}</th>
                                    <th data-breakpoints="md">{{ translate('Executive Name') }}</th>
                                    <th data-breakpoints="md">{{ translate('Status') }}</th>
                                    <th data-breakpoints="md">{{ translate('Total') }}</th>
                                    <th class="text-right">{{ translate('options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($data as $key => $order)
                                    @php
                                        $total += $order->total_value;
                                        $exe_name = getUsernameBycustomerstaffId($order->created_by);
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            {{ $order->date }}
                                        </td>
                                        {{-- <td>
                            {{ $order->expiry_date }}
                        </td> --}}
                                        <td>
                                            <a href="{{ route('purchase_orders_view', $order->id) }}"
                                                title="{{ translate('View') }}">
                                                {{ $order->purchase_no }}
                                            </a>

                                        </td>
                                        <td>
                                            {{ $order->name }}
                                        </td>
                                        <td>{{ $exe_name }}</td>
                                        <td>
                                            @if ($order->status == 1)
                                                Pending
                                            @elseif($order->status == 2)
                                                Approved
                                            @else
                                                Rejected
                                            @endif
                                        </td>
                                        <td>
                                            {{ single_price($order->total_value) }}
                                        </td>

                                        <td class="text-right">
                                            @if ($order->status == 1)
                                                <a href="javascript:void(0);" onclick="showApproveModal({{ $order->id }})"
                                                    class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                    title="{{ translate('Approve') }}">
                                                    <i class="las la-check-circle"></i>
                                                </a>

                                                <a href="#"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                    data-href="{{ route('purchase_reject.index', $order->id) }}"
                                                    title="{{ translate('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align:right;" colspan="6">Total</td>
                                    <td>{{ single_price($total) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                </div>
            </div>
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
