@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Opening Balance') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('opening-balances.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Opening Balance')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_opening_balances" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Opening Balance') }}</h5>
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
                    <th>{{ translate('Year') }}</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Account Name') }}</th>
                    <th>{{ translate('Sub Type') }}</th>
                    <th>{{ translate('Debit') }}</th>
                    <th>{{ translate('Credit') }}</th>
                    <th width="12%" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach ($opening_balances as $key => $opening_balance)
                <tr>
                    <td>{{ ($key+1) + ($opening_balances->currentPage() - 1)*$opening_balances->perPage() }}</td>
                    <td>{{ $opening_balance->financial_year->year_name }}</td>
                    <td>{{ $opening_balance->open_date }}</td>
                    <td>
                        @if($opening_balance->coa)
                        {{ $opening_balance->coa->head_name }}
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($opening_balance->subcode)
                        {{ $opening_balance->subcode->name }}
                        @else
                        -
                        @endif
                    </td>
                    <td>{{ $opening_balance->debit }}</td>
                    <td>{{ $opening_balance->credit }}</td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('opening-balances.edit', $opening_balance->id) }}" title="{{ __('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" href="{{ route('opening-balances.destroy', $opening_balance->id) }}" title="{{ __('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $opening_balances->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_opening_balances(el){
        $('#sort_opening_balances').submit();
    }
</script>
@endsection
