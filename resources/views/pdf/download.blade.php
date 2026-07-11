<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekomendasi Obat</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            margin: 24px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 8px 0 0;
            color: #475569;
            font-size: 11px;
        }

        .section-title {
            margin-top: 24px;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        table th,
        table td {
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            vertical-align: top;
        }

        table th {
            background: #f8fafc;
            text-align: left;
            color: #475569;
            width: 35%;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Laporan Rekomendasi Obat</h1>
        <p>Apotek24 &middot; Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <div class="section-title">Detail Rekomendasi</div>
    <table>
        <tr>
            <th>Nama</th>
            <td>{{ $detail['nama'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Keluhan</th>
            <td>{{ $detail['keluhan'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Obat</th>
            <td>{{ $detail['obat'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Similarity</th>
            <td>{{ number_format($detail['similarity'] ?? 0, 2) }}%</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $detail['admin_status'] ?? $detail['status'] ?? 'Menunggu Validasi' }}</td>
        </tr>
    </table>

    <div class="section-title">Informasi Tambahan</div>
    <table>
        <tr>
            <th>Usia</th>
            <td>{{ $detail['usia'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Durasi</th>
            <td>{{ $detail['durasi'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Riwayat</th>
            <td>{{ $detail['riwayat'] ?? '-' }}</td>
        </tr>
        <tr>
            <th>Catatan Admin</th>
            <td>{{ $detail['admin_conditions'] ?? 'Belum ada catatan dari Admin.' }}</td>
        </tr>
    </table>

    <div class="footer">Dokumen ini dihasilkan otomatis oleh sistem Apotek24.</div>

</body>

</html>