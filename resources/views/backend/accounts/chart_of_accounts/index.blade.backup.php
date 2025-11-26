@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Chart Of Accounts') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('contra-vouchers.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Tree View')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_contra_vouchers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Chart Of Accounts') }}</h5>
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
                    <th>{{ translate('Head Code') }}</th>
                    <th>{{ translate('PHead Name') }}</th>
                    <th>{{ translate('PHead') }}</th>
                    <th>{{ translate('Head Type') }}</th>
                    <th width="135px" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coas as $key => $coa)
                <tr>
                    <td>{{ $coa->head_code }}</td>
                    <td>{{ $coa->head_name }}</td>
                    <td>{{ $coa->pre_head_name }}</td>
                    <td>{{ $coa->head_type }}</td>
                    <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('contra-vouchers.show', $coa->id) }}" title="{{ translate('View') }}">
                            <i class="las la-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $coas->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection