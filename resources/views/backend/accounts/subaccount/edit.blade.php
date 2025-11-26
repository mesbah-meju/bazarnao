@extends('backend.layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-lg-12">
        <h3 class="fw-bold text-primary">Edit Sub Account</h3>
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

        <form class="form-horizontal" action="{{ route('sub-accounts.update', $sub_account->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="sub_type_id">Sub Type:</label>
                <select class="form-control" id="sub_type_id" name="sub_type_id">
                    <option value="">Select Sub Type</option>
                    @foreach($subtypes as $subtype)
                    <option value="{{ $subtype->id }}" {{ $sub_account->sub_type_id == $subtype->id ? 'selected' : '' }}>
                        {{ $subtype->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">Account Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $sub_account->name }}">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection