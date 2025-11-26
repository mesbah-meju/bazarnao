@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Offers')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('offer.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Offer')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
  <div class="card-header">
      <h5 class="mb-0 h6">{{translate('Offer Information')}}</h5>
  </div>
  <div class="card-body">
      <table class="table aiz-table p-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Title')}}</th>
                    <th>{{translate('Banner')}}</th>
                    <th>{{translate('Type')}}</th>
                    <th>{{translate('Discount')}}</th>
                    <th>{{translate('Start Date')}}</th>
                    <th>{{translate('End Date')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $key => $offer)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$offer->title}}</td>
                        <td><img src="{{ uploaded_asset($offer->banner) }}" alt="banner" class="h-50px"></td>
                        <td>@if ($offer->type == 'cart_base')
                                {{ translate('Cart Base') }}
                            @elseif ($offer->type == 'product_base')
                                {{ translate('Product Base') }}
                        @endif</td>
                        <td>{{$offer->discount}}</td>
                        <td>{{ date('d-m-Y', $offer->start_date) }}</td>
                        <td>{{ date('d-m-Y', $offer->end_date) }}</td>
						<td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('offer.edit', encrypt($offer->id) )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('offer.destroy', $offer->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
