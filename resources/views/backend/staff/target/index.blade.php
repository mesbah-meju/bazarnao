@extends('backend.layouts.app')

@section('content')

<style>
.form-group label {
    font-weight: 600;
    margin-bottom: 5px;
}

.selectpicker {
    border-radius: 5px;
    border: 1px solid #ced4da;
    padding: 0.5rem 1rem;
}

.form-control {
    border-radius: 5px;
    padding: 0.75rem 1rem;
    border: 1px solid #ced4da;
    font-size: 1rem;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 10px 20px;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
    padding: 10px 20px;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
}

.card-header {
    background-color: #f7f8fa;
    border-bottom: 1px solid #e9ecef;
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    border: none;
}

.gutters-5 > [class^='col-'] {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

@media (max-width: 768px) {
    .btn-lg {
        width: 100%;
    }
}

    .table {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .table th {
        background-color: #f8f9fa; /* Light gray background for header */
        font-weight: bold;
        text-align: center;
        color: #495057; /* Darker text color */
    }

    .table td {
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f2f2f2; /* Light gray background for odd rows */
    }

    .table-hover tbody tr:hover {
        background-color: #e9ecef; /* Slightly darker gray for row hover */
    }

    .btn-icon {
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        padding: 0;
        margin: 0 5px;
    }

</style>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Target')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('targets.create') }}" class="btn btn-md btn-danger">
                <span>{{translate('Add Target')}}</span>
            </a>

            <button onclick="printTable()" class="btn btn-md btn-primary">
                <span>{{translate('Print Target')}}</span>
            </button>
        </div>
    </div>
</div>

<div class="card">
    <form id="culexpo" class="" action="" method="GET">
        <div class="card-header bg-light text-dark py-3">
            <h5 class="mb-0">{{ translate('Filter Targets') }}</h5>
        </div>
        <div class="card-body">
            <div class="row gutters-5">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="user_id" class="form-label">{{ translate('Filter By Employee') }}:</label>
                        <select class="form-control selectpicker" name="user_id" id="user_id" data-live-search="true">
                            <option value="">{{ translate('Select One') }}</option>
                            @foreach(\App\Models\Staff::get() as $executive)
                                <option value="{{$executive->user_id}}" @if($user_id == $executive->user_id) selected @endif>
                                    {{ $executive->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="user_role_id" class="form-label">{{ translate('Filter By Employee Role') }}:</label>
                        <select class="form-control selectpicker" name="user_role_id[]" id="user_role_id" multiple data-live-search="true">
                            <option value="">{{ translate('Select One') }}</option>
                            @foreach(\App\Models\Role::get() as $role)
                                <option value="{{$role->id}}" @if(in_array($role->id, (array)$user_role_ids)) selected @endif>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="month" class="form-label">{{ translate('Month') }}:</label>
                        <input type="text" id="month" name="month" class="form-control monthpicker" placeholder="{{ translate('Month') }}" value="{{ !empty($month) ? $month : '' }}">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-md-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="las la-search"></i> {{ translate('Filter') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-lg" id="resetBtn">
                        <i class="las la-undo"></i> {{ translate('Reset') }}
                    </button>

                </div>
            </div>
        </div>
    </form>
</div>


<div class="card">
    <div class="card-header bg-light text-dark">
        <h5 class="mb-0 h6">{{ translate('Targets') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="target-table" class="table table-striped table-hover aiz-table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Name') }}</th>
                        <th>{{ translate('Email') }}</th>
                        <th>{{ translate('Phone') }}</th>
                        <th>{{ translate('Year') }}</th>
                        <th>{{ translate('Month') }}</th>
                        <th>{{ translate('Target Amount') }}</th>
                        <th>{{ translate('Target New Customer') }}</th>
                        <th>{{ translate('Recovery Target') }}</th>
                        <th>{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($targets as $key => $target)
                        @if($target->user != null)
                            <tr>
                                <td>{{ ($key + 1) + ($targets->currentPage() - 1) * $targets->perPage() }}</td>
                                <td>{{ $target->user->name }}</td>
                                <td>{{ $target->user->email }}</td>
                                <td>{{ $target->user->phone }}</td>
                                <td>{{ $target->year }}</td>
                                <td>{{ $target->month }}</td>
                                <td>{{ $target->target }}</td>
                                <td>{{ $target->target_customer }}</td>
                                <td>{{ $target->recovery_target }}</td>
                                <td class="text-right">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('targets.edit', encrypt($target->id)) }}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('targets.destroy', $target->id) }}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="aiz-pagination">
            {{ $targets->appends(request()->input())->links() }}
        </div>
    </div>
</div>


@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
<script type="text/javascript">
    function printTable() {
        var tableContents = document.getElementById("target-table").outerHTML;
        var printWindow = window.open('', '', 'height=500, width=800');
        printWindow.document.write('<html><head><title>{{ translate("Targets") }}</title>');
        printWindow.document.write('<style>body{font-family: Arial, sans-serif;} table {width: 100%; border-collapse: collapse;} table, th, td {border: 1px solid black; padding: 8px; text-align: left;} </style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(tableContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }

    document.getElementById('resetBtn').addEventListener('click', function() {
        // Reset selectpicker inputs
        document.querySelectorAll('.selectpicker').forEach(select => {
            select.selectedIndex = 0; // Reset to the first option
            $(select).selectpicker('refresh'); // Refresh the selectpicker
        });

        // Reset text inputs
        document.querySelectorAll('input[type="text"], input[type="month"]').forEach(input => {
            input.value = ''; // Clear the value
        });

        // Optionally, reset hidden inputs (if any)
        document.querySelectorAll('input[type="hidden"]').forEach(input => {
            input.value = ''; // Clear hidden inputs if needed
        });
    });

    $(document).ready(function() {
        $('.selectpicker').selectpicker({
            // Additional options can be set here if necessary
            liveSearch: true,
            actionsBox: true,
            deselectAllText: 'Deselect All',
            selectAllText: 'Select All'
        });
    });

</script>
@endsection


