@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Sub Accounts') }}</h1>
		</div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('sub-accounts.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Sub Account')}}</span>
            </a>
        </div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_sub_accounts" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Sub Accounts') }}</h5>
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
                    <th>{{ translate('Sub Type') }}</th>
                    <th>{{ translate('Account') }}</th>
                    <th>{{ translate('Created At') }}</th>
                    <th width="97px" class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1 @endphp
                @foreach ($sub_accounts as $key => $sub_account)
                <tr>
                    <td>{{ ($key+1) + ($sub_accounts->currentPage() - 1)*$sub_accounts->perPage() }}</td>
                    <td>
                        @if($sub_account->subtype)
                        {{ $sub_account->subtype->name }}
                        @else
                        -
                        @endif
                    </td>
                    <td>{{ $sub_account->name }}</td>
                    <td>{{ $sub_account->created_at }}</td>
                    <td class="text-right">
                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('sub-accounts.edit', $sub_account->id) }}" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                        <a class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" href="javascript:void(0);" data-href="{{ route('sub-accounts.destroy', $sub_account->id) }}" title="{{ translate('Delete') }}">
                            <i class="las la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $sub_accounts->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_sub_accounts(el){
        $('#sort_sub_accounts').submit();
    }
</script>
@endsection
