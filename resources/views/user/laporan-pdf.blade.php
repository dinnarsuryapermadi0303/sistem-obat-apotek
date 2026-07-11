<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Validasi Obat</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 14px;
            color: #555;
        }

        .section {
            margin-bottom: 18px;
        }

        .section h2 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .section p,
        .section ul {
            margin: 0;
            line-height: 1.6;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #198754;
            color: white;
            border-radius: 4px;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Validasi Obat</h1>
        <p>{{ date('Y-m-d H:i:s') }}</p>
    </div>

    <div class="section">
        <h2>Data Pengguna</h2>
        <table class="table">
            <tr>
                <th>Nama</th>
                <td>{{ $report['nama'] }}</td>
            </tr>
            <tr>
                <th>Usia</th>
                <td>{{ $report['usia'] }}</td>
            </tr>
            <tr>
                <th>Keluhan</th>
                <td>{{ $report['keluhan'] }}</td>
            </tr>
            <tr>
                <th>Durasi</th>
                <td>{{ $report['durasi'] }}</td>
            </tr>
            <tr>
                <th>Riwayat</th>
                <td>{{ $report['riwayat'] }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $report['status'] }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Obat Pilihan Pengguna</h2>
        @if(!empty($report['selected_meds']))
        <ul>
            @foreach($report['selected_meds'] as $medicine)
            <li>{{ $medicine }}</li>
            @endforeach
        </ul>
        @else
        <p>Tidak ada obat yang dipilih.</p>
        @endif
    </div>

    <div class="section">
        <h2>Obat Disetujui Admin</h2>
        @if(!empty($report['approved_meds']))
        <ul>
            @foreach($report['approved_meds'] as $medicine)
            <li>{{ $medicine }}</li>
            @endforeach
        </ul>
        @else
        <p>Tidak ada obat yang disetujui.</p>
        @endif
    </div>

    <div class="section">
        <h2>Ketentuan Admin</h2>
        <p>{{ $report['admin_conditions'] ?: 'Tidak ada catatan tambahan.' }}</p>
    </div>

    <div class="section">
        <h2>Status Pengguna</h2>
        <p>{{ $report['user_status'] ?: 'Tidak tersedia' }}</p>
    </div>
</body>

</html>