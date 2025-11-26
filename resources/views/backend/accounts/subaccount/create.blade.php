@extends('backend.layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold text-primary">Create Sub Account</h3>
        <a href="{{ route('sub-accounts.index') }}" class="btn btn-success btn-sm d-flex align-items-center">
            <i class="las la-list mr-2"></i> Sub Accounts
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

        <form class="form-horizontal" action="{{ route('sub-accounts.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="sub_type_id">Sub Type:</label>
                <select class="form-control" id="sub_type_id" name="sub_type_id">
                    <option value="">Select Sub Type</option>
                    @foreach($subtypes as $subtype)
                    <option value="{{ $subtype->id }}" {{ old('sub_type_id') == $subtype->id ? 'selected' : '' }}>
                        {{ $subtype->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="name">Account Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Account Name" value="{{ old('name') }}">
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@endsection