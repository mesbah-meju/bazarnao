@extends('backend.layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold text-primary">{{ $title }}</h3>
        <a href="{{ route('account.subaccount') }}" class="btn btn-success btn-sm d-flex align-items-center">
            <i class="las la-list mr-2"></i> Sub Account List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 bg-white p-4 shadow-sm rounded">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="form-horizontal" action="{{ route('accounts.store_subaccount') }}" method="POST">
            @csrf

            <!-- Sub Type Dropdown -->
            <div class="form-group">
                <label for="subTypeId">Sub Type:</label>
                <select class="form-control" id="subTypeId" name="subTypeId">
                    <option value="">Select Sub Type</option>
                    @foreach($subType as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <!-- Account Name Input -->
            <div class="form-group">
                <label for="name">Account Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Account Name" value="{{ old('name') }}">
            </div>
        
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection
