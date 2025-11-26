@extends('backend.layouts.app')
@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-12 text-right">
            <a href="{{ route('payment_methods.create') }}" class="btn  btn-info">
                <i class="fa-solid fa-plus"></i> <span>{{translate('Add Payment Method')}}</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <form class="" id="sort_debits" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Payment Methods') }}</h5>
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
                    <th class="text-left">Payment Method Name</th>
                    <th>Payment Type</th>
                    <th>Details</th>
                    <th class="text-right">Action</th>

                </tr>
            </thead>
            <tbody>
                @foreach($paymentMethods as $paymentMethod)
                <tr>
                    @if($paymentMethod->trashed())
                    <td class="text-left text-muted ">{{ $paymentMethod->name }}</td>
                    <td class="text-muted">{{ $paymentMethod->type }}</td>
                    <td class="text-muted">{{ $paymentMethod->details }}</td>
                    @else
                    <td class="text-left font-weight-normal">{{ $paymentMethod->name }}</td>
                    <td class=" font-weight-normal">{{ $paymentMethod->type }}</td>
                    <td class=" font-weight-normal">{{ $paymentMethod->details }}</td>

                    @endif

                    <td class="text-right">

                        @if($paymentMethod->trashed())
                        <form action="{{ route('payment_methods.restore', ['id'=>$paymentMethod->id, 'name'=>$paymentMethod->name]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to Restore this payment method?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-success btn-icon btn-circle btn-sm" title="Restore Payment Method">
                                <i class="las la-redo-alt"></i>
                            </button>
                        </form>
                        @else
                        <a href="{{ route('payment_methods.edit', $paymentMethod->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="Edit Payment Method">
                            <i class="las la-edit"></i>
                        </a>
                        <form action="{{ route('payment_methods.destroy', $paymentMethod->id) }}" method="GET" style="display:inline;" onsubmit="return confirm('Are you sure you want to Delete this payment method?');">
                            @csrf
                            <button type="submit" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="Delete Payment Method">
                                <i class="las la-trash"></i>
                            </button>
                        </form>

                        @endif

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
</div>


@endsection