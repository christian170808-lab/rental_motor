@extends('layouts.app')

@push('styles')
<style>
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .table thead th {
        background: #f3f4f6;
        color: #111827;
        font-weight: 700;
        border-bottom: 2px solid #d1d5db;
        padding: 1rem;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    .btn-outline-primary { color: #374151; border-color: #9ca3af; }
    .btn-outline-primary:hover { background: #374151; color: white; border-color: #374151; }
    .btn-outline-danger { color: #991b1b; border-color: #fca5a5; }
    .btn-outline-danger:hover { background: #991b1b; color: white; border-color: #991b1b; }
    .no-data { color: #6b7280; font-style: italic; }
</style>
@endpush

@section('content')
<div class="container-fluid p-4">

    <h2 class="mb-4" style="font-weight: 800; color: #111827;">Customer Data</h2>

    <div class="table-container">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Customer ID</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->customer_name }}</td>
                    <td>{{ $customer->customer_id }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-pencil-alt"></i> Edit
                        </a>
                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete this customer?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 no-data">
                        No customer data available yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection