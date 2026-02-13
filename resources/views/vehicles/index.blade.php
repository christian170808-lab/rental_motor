<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
</head>
<body>
    <div class="container mt-4">
        {{-- Judul Halaman --}}
        <h2>Daftar Kendaraan</h2>

        {{-- Alert Notifikasi Error --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Pencarian dan Filter --}}
        <form action="{{ route('vehicles.index') }}" method="GET" class="search-form">
            <div class="form-wrapper d-flex gap-2">
                {{-- Input teks untuk pencarian --}}
                <input type="text" name="search" placeholder="Search vehicle name..." value="{{ request('search') }}" class="form-control">

                {{-- Dropdown Filter Tipe dengan Live Search --}}
                {{-- Gunakan class 'selectpicker' dan atribut 'data-live-search="true"' --}}
                <select name="type" class="form-control selectpicker" data-live-search="true" title="All Types">
                    <option value="">Semua Tipe</option>
                    {{-- Value tetap 'skuter' tapi teksnya bahasa Inggris --}}
                    <option value="skuter" {{ request('type') == 'skuter' ? 'selected' : '' }}>Scooter</option>
                    <option value="sport" {{ request('type') == 'sport' ? 'selected' : '' }}>Sport Motorcycle</option>
                    <option value="trail" {{ request('type') == 'trail' ? 'selected' : '' }}>Trail / Adventure</option>
                </select>

                {{-- Tombol Submit Pencarian --}}
                <button type="submit" class="btn btn-primary">
                    Search
                </button>
            </div>
        </form>
        
        <br>

        {{-- Pengecekan Data Kendaraan --}}
        @if($vehicles->isEmpty())
            <p class="no-data-message alert alert-warning">Data kendaraan tidak ditemukan.</p>
        @else
            {{-- Tabel Data Kendaraan --}}
            <table class="table table-bordered table-striped">
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
                    {{-- Looping Data Kendaraan --}}
                    @foreach($vehicles as $v)
                        <tr>
                            {{-- Kolom Gambar --}}
                            <td class="text-center">
                                <img
                                    src="{{ asset('image/' . ($v->image ?? 'default.png')) }}"
                                    class="vehicle-img"
                                    alt="{{ $v->name }}"
                                    style="max-width: 100px; height: auto;"
                                >
                            </td>

                            {{-- Kolom Nama dan ID --}}
                            <td>
                                <strong>{{ $v->name }}</strong><br>
                                <small class="text-muted">ID: {{ $v->id }}</small>
                            </td>

                            {{-- Kolom Tipe --}}
                            <td class="text-center">
                                <span class="badge badge-secondary">
                                    {{ $v->type }}
                                </span>
                            </td>

                            {{-- Kolom Plat Nomor --}}
                            <td><strong>{{ $v->plate_number }}</strong></td>

                            {{-- Kolom Status Badge --}}
                            <td class="text-center">
                                @if($v->status == 'available')
                                    <span class="badge badge-success">Available</span>
                                @else
                                    <span class="badge badge-danger">Rented</span>
                                @endif
                            </td>

                            {{-- Kolom Aksi Tombol --}}
                            <td class="text-center">
                                {{-- Tombol Periksa/Kembali --}}
                                <a
                                    href="{{ route('returns.create', $v->id) }}"
                                    class="btn btn-sm btn-info"
                                >
                                    Check
                                </a>

                                {{-- Logika Tombol Pinjam --}}
                                @if($v->status == 'available')
                                    <a
                                        href="/booking/create/{{ $v->id }}"
                                        class="btn btn-sm btn-success"
                                    >
                                        Rent
                                    </a>
                                @else
                                    {{-- Tombol Nonaktif --}}
                                    <button disabled class="btn btn-sm btn-secondary">
                                        Unavailable
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

    {{-- Script untuk mengaktifkan fitur pencarian di dropdown --}}
    <script>
        $(document).ready(function () {
            // Ini akan menginisialisasi plugin Bootstrap Select pada dropdown
            $('.selectpicker').selectpicker();
        });
    </script>
</body>
</html>