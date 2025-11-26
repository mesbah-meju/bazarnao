@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Financial Year') }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('financial-years.create') }}" class="btn btn-circle btn-soft-info">
                <span>{{translate('Add Financial Year')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_financial_years" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Financial Year') }}</h5>
            </div>

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="md" width="5%">#</th>
                    <th>{{ translate('Year Name') }}</th>
                    <th>{{ translate('Start Date') }}</th>
                    <th>{{ translate('End Date') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th width="12%" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($financial_years as $key => $financial_year)
                <tr>
                    <td>{{ ($key+1) + ($financial_years->currentPage() - 1)*$financial_years->perPage() }}</td>
                    <td>{{ $financial_year->year_name }}</td>
                    <td>{{ $financial_year->start_date }}</td>
                    <td>{{ $financial_year->end_date }}</td>
                    <td>
                        @if($financial_year->status == 1 )
                            <span class="badge badge-soft-success w-50 mr-1">Active</span>
                        @else
                            @if($financial_year->is_close != 1)
                                <span class="badge badge-soft-danger w-50">Inactive</span>
                            @endif
                        @endif

                        @if($financial_year->is_close == 1)
                            <span class="badge badge-soft-danger w-50">Closed</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($financial_year->is_close == 1)
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('financial-years.edit', $financial_year->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @if($last_close_year == $financial_year->id)
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('financial-years.reverse', $financial_year->id) }}" title="{{ translate('Reverse') }}">
                                <i class="las la-undo"></i>
                            </a>
                            @endif
                        @else
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('financial-years.edit', $financial_year->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @if($financial_year->status == 1)
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{ route('financial-years.closing', $financial_year->id) }}" title="{{ translate('Closing') }}">
                                <i class="las la-check-square"></i>
                            </a>
                            @else
                            <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('financial-years.destroy', $financial_year->id) }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $financial_years->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')
@include('modals.common_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_financial_years(el) {
        $('#sort_financial_years').submit();
    }

    function change_status(el) {
        var status = 0;
        if (el.checked) {
            var status = 1;
        }
        $.post('{{ route("financial-years.change-status") }}', {
            _token: '{{ csrf_token() }}',
            id: el.value,
            status: status
        }, function(data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate("Change status successfully") }}');
            } else {
                AIZ.plugins.notify('danger', '{{ translate("Something went wrong") }}');
            }
        });
    }
</script>

<script type="text/javascript">
    function showYearClosingForm() {
        $('#common-modal .modal-title').html('');
        $('#common-modal .modal-body').html('');

        var name = 'Close Financial Year';
        $.ajax({
            type: "GET",
            url: "{{ route('financial-years.closing-years') }}",
            data: {},
            success: function(data) {
                $('#common-modal .modal-title').html(name);
                $('#common-modal .modal-body').html(data);
                $('#common-modal .modal-dialog').removeClass('modal-lg');
                $('#common-modal .modal-dialog').addClass('modal-md');
                $('#common-modal').modal('show');
            }
        });
    }

    function sort_campuses(el) {
        $('#sort_campuses').submit();
    }
</script>
@endsection