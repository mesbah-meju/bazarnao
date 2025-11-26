@extends('backend.layouts.app')

@section('content')

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ isset($paymentMethod) ? 'Edit' : 'Add' }} Payment Method</h5>

            <form action="{{ isset($paymentMethod) ? route('payment_methods.update', $paymentMethod->id) : route('payment_methods.store') }}" method="POST">
                @csrf


                <div class="mb-3">
                    <label for="name" class="form-label">Payment Method Name:</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ isset($paymentMethod) ? $paymentMethod->name : '' }}" required>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Payment Type:</label>
                    <select id="type" name="type" class="form-control">
                        <option value="Digital wallets" {{ (isset($paymentMethod) && $paymentMethod->type == 'Digital wallets') ? 'selected' : '' }}>Digital wallets</option>
                        <option value="Bank Transfer" {{ (isset($paymentMethod) && $paymentMethod->type == 'Bank Transfer') ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Mobile Banking" {{ (isset($paymentMethod) && $paymentMethod->type == 'Mobile Banking') ? 'selected' : '' }}>Mobile Banking</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">Details:</label>
                    <textarea id="details" name="details" class="form-control" required>{{ isset($paymentMethod) ? $paymentMethod->details : '' }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">{{ isset($paymentMethod) ? 'Update' : 'Add' }} Payment Method</button>
            </form>
            <a href="{{ route('payment_methods.index') }}" class="btn btn-secondary mt-3">Back</a>
        </div>
    </div>
</div>

@endsection