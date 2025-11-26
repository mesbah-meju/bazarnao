@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Happy Hours')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('happy_hours.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Create New Happy Hour')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Happy Hours')}}</h5>
        <div class="pull-right clearfix">
            <form class="" id="sort_happy_hours" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div class="" style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Title')}}</th>
                    <th data-breakpoints="lg">{{ translate('Banner') }}</th>
                    <th data-breakpoints="lg">{{ translate('Start Date') }}</th>
                    <th data-breakpoints="lg">{{ translate('End Date') }}</th>
                    <th data-breakpoints="lg">{{ translate('Status') }}</th>
                    <th data-breakpoints="lg">{{ translate('Featured') }}</th>
                    <th data-breakpoints="lg">{{ translate('Page Link') }}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($happy_hours as $key => $happy_hour)
                    <tr>
                        <td>{{ ($key+1) + ($happy_hours->currentPage() - 1)*$happy_hours->perPage() }}</td>
                        <td>{{ $happy_hour->getTranslation('title') }}</td>
                        <td><img src="{{ uploaded_asset($happy_hour->banner) }}" alt="banner" class="h-50px"></td>
                        <td>{{ date('d-m-Y H:i:s', $happy_hour->start_date) }}</td>
                        <td>{{ date('d-m-Y H:i:s', $happy_hour->end_date) }}</td>
                        <td>
							<label class="aiz-switch aiz-switch-success mb-0">
								<input onchange="update_happy_hour_status(this)" value="{{ $happy_hour->id }}" type="checkbox" @if($happy_hour->status == 1) checked @endif>
								<span class="slider round"></span>
							</label>
						</td>
						<td>
							<label class="aiz-switch aiz-switch-success mb-0">
								<input onchange="update_happy_hour_feature(this)" value="{{ $happy_hour->id }}" type="checkbox" @if($happy_hour->featured == 1) checked @endif>
								<span class="slider round"></span>
							</label>
						</td>
						<td>{{ url('happy-hour/'.$happy_hour->slug) }}</td>
						<td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('happy_hours.edit', ['id'=>$happy_hour->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('happy_hours.destroy', $happy_hour->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $happy_hours->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function update_happy_hour_status(el) {
            let status = el.checked ? 1 : 0;
            $.post('{{ route('happy_hours.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    location.reload();
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
        function update_happy_hour_feature(el) {
            let featured = el.checked ? 1 : 0;
            $.post('{{ route('happy_hours.update_featured') }}', {_token:'{{ csrf_token() }}', id:el.value, featured:featured}, function(data){
                if(data == 1){
                    location.reload();
                } else {
                    location.reload();
                    AIZ.plugins.notify('danger', '{{ translate('Please change the status to active before updating the featured status.') }}');
                }
            });
        }
    </script>
@endsection
