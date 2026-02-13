<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Vehicle List</h2>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search name or plate...">
                </div>
                <div class="col-md-6">
                    <select id="typeFilter" class="form-control" style="width: 100%;">
                        <option value="">-- All Types --</option>
                        {{-- Mengambil semua tipe unik dari database untuk dimasukkan ke dropdown --}}
                        @foreach($vehicles->pluck('type')->unique() as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Success Notification --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Notification --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <br>

        @if($vehicles->isEmpty())
            <p class="alert alert-warning">No vehicle data found.</p>
        @else
            <table class="table table-bordered table-striped" id="vehicleTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Plate</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $v)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset('image/' . ($v->image ?? 'default.png')) }}" alt="{{ $v->name }}" style="max-width: 100px; height: auto;">
                            </td>
                            <td><strong>{{ $v->name }}</strong></td>
                            <td class="text-center">
                                <span class="badge badge-secondary vehicle-type">{{ $v->type }}</span>
                            </td>
                            <td><strong>{{ $v->plate_number }}</strong></td>
                            <td class="text-center">
                                @if($v->status == 'available')
                                    <span class="badge badge-success">Available</span>
                                @else
                                    <span class="badge badge-danger">Rented</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('returns.create', $v->id) }}" class="btn btn-sm btn-info mr-2">Check</a>
                                    
                                    @if($v->status == 'available')
                                        <a href="{{ route('bookings.create', $v->id) }}" class="btn btn-sm btn-success mr-2">Rent</a>
                                    @else
                                        <button disabled class="btn btn-sm btn-secondary mr-2" style="cursor: not-allowed;">Unavailable</button>
                                    @endif

                                    @if($v->status == 'rented')
                                        @php
                                            $activeBooking = \App\Models\Booking::where('vehicle_id', $v->id)
                                                                            ->where('payment_status', 'pending') 
                                                                            ->latest()
                                                                            ->first();
                                        @endphp
                                        @if($activeBooking)
                                            <a href="{{ route('booking.pdf', $activeBooking->id) }}" class="btn btn-sm btn-danger">PDF</a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p id="noDataMessage" class="alert alert-warning d-none">No vehicle data matches your search.</p>
        @endif
    </div>

    <script>
        $(document).ready(function(){
            // Initialize Select2 on dropdown
            $('#typeFilter').select2({
                theme: "bootstrap",
                placeholder: "Search or select type...",
                allowClear: true
            });

            // Table Filter Function
            function filterTable() {
                var searchText = $('#searchInput').val().toLowerCase();
                var typeFilter = $('#typeFilter').val();
                var found = false;

                $("#vehicleTable tbody tr").each(function() {
                    var row = $(this);
                    var name = row.find("td:eq(1)").text().toLowerCase();
                    var plate = row.find("td:eq(3)").text().toLowerCase();
                    var type = row.find(".vehicle-type").text(); // Get text from badge

                    // Filter Logic
                    var matchText = name.indexOf(searchText) > -1 || plate.indexOf(searchText) > -1;
                    var matchType = typeFilter === "" || type === typeFilter;

                    if (matchText && matchType) {
                        row.show();
                        found = true;
                    } else {
                        row.hide();
                    }
                });

                // Show message if no rows match
                if (found) {
                    $('#noDataMessage').addClass('d-none');
                } else {
                    $('#noDataMessage').removeClass('d-none');
                }
            }

            // Event listeners
            $('#searchInput').on('keyup', filterTable);
            $('#typeFilter').on('change', filterTable);
        });
    </script>
</body>
</html>