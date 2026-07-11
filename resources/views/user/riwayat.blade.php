@extends('layout')

@section('content')

<div class="container-fluid">

    <h3 class="mb-4">
        Riwayat Validasi Rekomendasi
    </h3>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-primary">

                    <tr>

                        <th>No</th>

                        <th>Tanggal</th>

                        <th>Nama</th>

                        <th>Keluhan</th>

                        <th>Obat</th>

                        <th>Similarity</th>

                        <th>Status</th>

                        <th>Admin</th>

                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($data as $i=>$row)

                    <tr>

                        <td>{{ $i+1 }}</td>

                        <td>{{ optional($row->display_timestamp)->format('d-m-Y H:i:s') ?? '-' }}</td>

                        <td>{{ $row->nama }}</td>

                        <td>{{ $row->keluhan }}</td>

                        <td>{{ $row->obat }}</td>

                        <td>{{ $row->similarity }}%</td>

                        <td>

                            @if($row->status=='Approved')

                            <span class="badge bg-success">
                                Approved
                            </span>

                            @elseif($row->status=='Rejected')

                            <span class="badge bg-danger">
                                Rejected
                            </span>

                            @else

                            <span class="badge bg-warning">
                                Pending
                            </span>

                            @endif

                        </td>

                        <td>

                            {{ $row->approved_by }}

                        </td>

                        <td>

                            <a
                                href="{{ route('validasi.detail',$row->id) }}"
                                class="btn btn-info btn-sm">

                                Detail

                            </a>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection