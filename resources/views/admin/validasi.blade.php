@extends('layout-admin')

@section('content')

<div class="container py-5">
    @if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

    @endif

    @if(session('error'))

    <div class="alert alert-danger">

        {{ session('error') }}

    </div>

    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Riwayat Validasi Pengguna</h2>
            <p class="text-muted">Lihat detail validasi pengguna terlebih dahulu sebelum melakukan konfirmasi.</p>
        </div>
    </div>
    @php
    use Illuminate\Support\Str;
    @endphp

    @if($data->count() > 0)
    <div class="card border-0 shadow rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Waktu</th>
                            <th>Nama</th>
                            <th>Usia</th>
                            <th>Keluhan</th>
                            <th>Durasi</th>
                            <th>Riwayat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $index => $item)
                        <tr>
                            <td>{{ $data->firstItem() + $index }}</td>
                            <td>{{ optional($item->display_timestamp)->format('d-m-Y H:i:s') ?? '-' }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->usia }}</td>
                            <td>{{ Str::limit($item->keluhan, 40) }}</td>
                            <td>{{ $item->durasi }}</td>
                            <td>{{ Str::limit($item->riwayat, 40) }}</td>
                            <td>
                                @php
                                $status = $item->admin_status ?? ($item->status ?? 'Menunggu');
                                @endphp

                                <span class="badge {{ $status == 'Disetujui Admin'
                                            ? 'bg-success'
                                            : ($status == 'Ditolak Admin'
                                                ? 'bg-danger'
                                                : 'bg-warning text-dark') }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin.detail', $item->id) }}" class="btn btn-sm btn-primary">Konfirmasi</a>
                                <form action="{{ route('admin.validasi.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus validasi ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center">
        Belum ada data validasi pengguna.
    </div>
    @endif

</div>

@endsection