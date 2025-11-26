@extends('backend.layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold text-primary">{{ $title }}</h3>
        <a  href="{{ route('account.create_subaccount') }}" class="btn btn-success btn-sm d-flex align-items-center">
            <i class="las la-plus-circle mr-2"></i> Add Sub Account
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 bg-white p-4 shadow-sm rounded">
        <table class="datatable table table-striped table-bordered table-hover">
            <thead class="bg-secondary text-white">
                <tr>
                    <th>{{ __('SL') }}</th>
                    <th>{{ __('Sub Type') }}</th>
                    <th>{{ __('Account') }}</th>
                    <th>{{ __('Created Date') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach ($subCode as $acc)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $acc->subtypeName }}</td>
                        <td>{{ $acc->name }}</td>
                        <td>{{ $acc->created_at->format('d-m-Y') }}</td>

                        <td class="d-flex">
                            <!-- Edit Button -->
                            <a href="{{ route('account.edit_subaccount', $acc->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm mr-2" title="{{ __('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            
                        
                            <!-- Delete Button -->
                            <form action="{{ route('account.delete_subaccount', $acc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ __('Delete') }}">
                                    <i class="las la-trash"></i>
                                </button>
                            </form>
                        </td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
