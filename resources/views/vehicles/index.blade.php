@extends('layouts.app')

@push('styles')
<style>
.container.mt-4 { max-width: 1400px; }
.section-title { position: relative; padding-bottom: 12px; margin-bottom: 24px; font-weight: 700; }
.section-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 4px; background: linear-gradient(90deg, #3b82f6, #60a5fa); border-radius: 2px; }
.table-container { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e5e7eb; margin-bottom: 2.5rem; }
.table-responsive-custom { max-height: 420px; overflow-y: auto; position: relative; }
.table thead th { position: sticky; top: 0; z-index: 10; background: linear-gradient(180deg, #1f2937, #374151); color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; border-bottom: none !important; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.table tbody tr { transition: all 0.18s ease; }
.table tbody tr:hover { background: #f1f5f9; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.table td, .table th { padding: 14px 16px !important; vertical-align: middle; border-color: #e5e7eb; }
.badge { padding: 0.5em 0.9em; font-weight: 500; border-radius: 1rem; font-size: 0.875rem; }
.btn-sm { padding: 0.35rem 0.75rem; font-size: 0.875rem; border-radius: 0.5rem; }
.empty-state { padding: 3rem 1rem; color: #6b7280; font-style: italic; text-align: center; background: #f9fafb; }
.table-responsive-custom::-webkit-scrollbar { width: 8px; }
.table-responsive-custom::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
.table-responsive-custom::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 10px; }
.table-responsive-custom::-webkit-scrollbar-thumb:hover { background: #6b7280; }
.btn-add-motor { background-color: #ffffff; color: #1f2937; border: 1px solid #d1d5db; font-weight: 600; padding: 0.6rem 1.25rem; border-radius: 0.6rem; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.06); }
.btn-add-motor:hover { background-color: #f9fafb; color: #111827; border-color: #9ca3af; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.12), 0 10px 24px rgba(0,0,0,0.08); }
.btn-add-motor:active { transform: translateY(0); box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.btn-add-motor i { font-size: 1.1rem; }
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Vehicle List</h2>
        <a href="{{ route('vehicles.create') }}" class="btn btn-add-motor shadow">
            <i class="fas fa-plus me-1"></i> Add Motor
        </a>
    </div>

    <div class="table-container">
        <div class="table-responsive-custom">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Plate</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $v)
                        <tr>
                            <td class="fw-medium">{{ $v->name }}</td>
                            <td><code>{{ $v->plate_number }}</code></td>
                            <td>
                                @if(strtolower($v->status) == 'available')
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($v->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @php $lastBooking = $v->bookings->last(); @endphp
                                @if($lastBooking && strtolower($v->status) == 'rented')
                                    <a href="{{ route('booking.pdf', $lastBooking->id) }}" class="btn btn-danger btn-sm shadow-sm">
                                        <i class="fa fa-file-pdf me-1"></i> PDF (ID: {{ $lastBooking->id }})
                                    </a>
                                @else
                                    <small class="text-muted fst-italic">ID Motor: {{ $v->id }} | No active booking</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No vehicles found in the database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="section-title mt-5 mb-4">Return Data</h2>

    <div class="table-container">
        <div class="table-responsive-custom">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Motor</th>
                        <th>Return Date</th>
                        <th>Late (days)</th>
                        <th>Penalty</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                        <tr>
                            <td>#{{ $return->booking_id }}</td>
                            <td>{{ optional($return->booking->vehicle)->name ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
                            <td>
                                @if($return->late_days > 0)
                                    <span class="text-danger fw-bold">{{ $return->late_days }}</span>
                                @else
                                    <span class="text-success">0</span>
                                @endif
                            </td>
                            <td>
                                @if($return->penalty > 0)
                                    <strong class="text-danger">Rp {{ number_format($return->penalty, 0, ',', '.') }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($return->vehicle_condition == 'Good')
                                    <span class="badge bg-success">Good</span>
                                @elseif($return->vehicle_condition == 'Minor Damage')
                                    <span class="badge bg-warning text-dark">Minor Damage</span>
                                @else
                                    <span class="badge bg-danger">Major Damage</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No return data available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection