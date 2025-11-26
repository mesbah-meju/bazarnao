@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Banks') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('banks.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Bank')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_banks" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Banks') }}</h5>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="md" width="5%">#</th>
                    <th>{{ translate('Bank Name') }}</th>
                    <th>{{ translate('Account Name') }}</th>
                    <th>{{ translate('Account No') }}</th>
                    <th>{{ translate('Branch') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th width="12%" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banks as $key => $bank)
                    <tr>
                        <td>{{ ($key+1) + ($banks->currentPage() - 1)*$banks->perPage() }}</td>
                        <td>{{ $bank->bank_name }}</td>
                        <td>{{ $bank->ac_name }}</td>
                        <td>{{ $bank->ac_number}}</td>
                        <td>{{ $bank->branch }}</td>
                        <td>{{ $bank->status }}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{ route('banks.show', $bank->id) }}" title="{{ __('Show') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('banks.edit', $bank->id) }}" title="{{ __('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('banks.destroy', $bank->id) }}" title="{{ __('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $banks->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_loans(el){
        $('#sort_banks').submit();
    }
</script>
@endsection




