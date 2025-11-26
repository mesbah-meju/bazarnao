@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Sales Report</h2>
            <div class="table-responsive">
                <table class="table table-bordered" id="sales-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Code</th>
                            <th>Payment Details</th>
                            <th>Grand Total</th>
                            <th>Delivered Date</th>
                            <th>Guest ID</th>
                            <th>Shipping Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- The data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include jQuery, DataTables, and any other necessary scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<script>
$(document).ready(function() {
    $('#sales-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("backend.reports.sales") }}', // Your route to fetch data
            data: function (d) {
                d.start_date = '{{ $start_date }}';
                d.end_date = '{{ $end_date }}';
                d.warehouse = '{{ $warehouse }}';
                d.user_id = '{{ $user_id }}';
                d.month = '{{ $month }}';
                d.year = '{{ $year }}';
                d.month_year = '{{ $month_year }}';
                d.search = $('input[type="search"]').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_id', name: 'user_id' },
            { data: 'code', name: 'code' },
            { data: 'payment_details', name: 'payment_details' },
            { data: 'grand_total', name: 'grand_total' },
            { data: 'delivered_date', name: 'delivered_date' },
            { data: 'guest_id', name: 'guest_id' },
            { data: 'shipping_address', name: 'shipping_address' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush