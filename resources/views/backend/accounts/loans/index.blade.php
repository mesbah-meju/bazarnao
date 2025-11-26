@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Loans') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('loans.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Loan')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_loans" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Loans') }}</h5>
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
                    <th>{{ translate('Borrower Name') }}</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Loan Amount') }}</th>
                    <th>{{ translate('Loan Term (Years)') }}</th>
                    <th>{{ translate('Interest Rate (%)') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th width="12%" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $key => $loan)
                    <tr>
                        <td>{{ ($key+1) + ($loans->currentPage() - 1)*$loans->perPage() }}</td>
                        <td>
                            @if($loan->bank)
                                {{ $loan->bank->bank_name }}
                            @else
                                {{ translate('N/A') }}
                            @endif
                        <td>{{ $loan->start_date }}</td>
                        <td>{{ number_format($loan->loan_amount, 2) }}</td>
                        <td>{{ $loan->loan_term }}</td>
                        <td>{{ $loan->interest_rate }}%</td>
                        <td>{{ $loan->status }}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{ route('loans.show', $loan->id) }}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @if ($loan->status == "Pending")
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('loans.edit', $loan->id) }}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @endif
                            <a class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" href="{{ route('loans.destroy', $loan->id) }}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $loans->appends(request()->input())->links() }}
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
        $('#sort_loans').submit();
    }
</script>
@endsection
