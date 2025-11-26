@extends('backend.layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-lg-12">
        <h3 class="fw-bold text-primary">{{ $title }}</h3>
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

        <form class="form-horizontal" action="{{ route('account.update_subaccount', $subAccount->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Laravel uses PUT method for updates -->
            <div class="form-group">
                <label for="subTypeId">Sub Type:</label>
                <select class="form-control" id="subTypeId" name="subTypeId">
                    <option value="">Select Sub Type</option>
                    @foreach($subType as $type)
                        <option value="{{ $type->id }}" {{ $subAccount->subTypeId == $type->id ? 'selected' : '' }}>
                            {{ $type->subtypeName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">Account Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $subAccount->name }}">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
