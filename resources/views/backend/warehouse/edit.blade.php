@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar mt-2 mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 h6">Edit Warehouse</h5>
    <a href="{{ route('warehouse.index') }}" class="btn btn-secondary btn-sm">← Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('warehouse.update', $warehouse->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Warehouse Name</label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $warehouse->name) }}" 
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select form-control aiz-selectpicker" required>
                    <option value="1" {{ $warehouse->status == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $warehouse->status == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
