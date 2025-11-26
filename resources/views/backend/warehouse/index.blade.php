@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar mt-2 mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 h6">Warehouses</h5>
    <a href="{{ route('warehouse.create') }}" class="btn btn-primary btn-sm">
        + Add Warehouse
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($warehouses as $key => $warehouse)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $warehouse->name }}</td>
                        <td>{{ $warehouse->code }}</td>
                        <td>
                            @if ($warehouse->status == 1)
                            <span class="badge bg-success rounded-5 px-4 py-2">Active</span>
                            @else
                            <span class="badge bg-danger rounded-5 px-4 py-2">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $warehouse->created_at->format('d M, Y h:i A') }}</td>
                        <td class="text-right">
                            <a href="{{ route('warehouse.edit', $warehouse->id) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No warehouses found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection