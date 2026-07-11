<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>

Laporan Validasi Obat

</title>

<style>

body{

font-family: DejaVu Sans, sans-serif;

font-size:13px;

color:#333;

}

h2{

text-align:center;

margin-bottom:5px;

}

.subtitle{

text-align:center;

font-size:12px;

color:#666;

margin-bottom:25px;

}

table{

width:100%;

border-collapse:collapse;

margin-top:10px;

margin-bottom:20px;

}

table th{

background:#f4f4f4;

text-align:left;

padding:8px;

border:1px solid #ddd;

width:35%;

}

table td{

padding:8px;

border:1px solid #ddd;

}

.badge{

padding:5px 8px;

border-radius:5px;

font-size:11px;

}

.footer{

margin-top:40px;

font-size:11px;

text-align:center;

color:#777;

}

</style>

</head>

<body>

<h2>

LAPORAN HASIL VALIDASI REKOMENDASI OBAT

</h2>

<div class="subtitle">

Apotek 24

<br>

Tanggal Cetak :

{{ date('d-m-Y H:i:s') }}

</div>

<h3>

Data Pengguna

</h3>

<table>

<tr>

<th>

Tanggal

</th>

<td>

{{ $detail['tanggal'] ?? '-' }}

</td>

</tr>

<tr>

<th>

Nama

</th>

<td>

{{ $detail['nama'] ?? '-' }}

</td>

</tr>

<tr>

<th>

Usia

</th>

<td>

{{ $detail['usia'] ?? '-' }}

</td>

</tr>

<tr>

<th>

Keluhan

</th>

<td>

{{ $detail['keluhan'] ?? '-' }}

</td>

</tr>

<tr>

<th>

Durasi

</th>

<td>

{{ $detail['durasi'] ?? '-' }}

</td>

</tr>

<tr>

<th>

Riwayat Penyakit

</th>

<td>

{{ $detail['riwayat'] ?? '-' }}

</td>

</tr>

</table>
<h3>

Hasil Rekomendasi

</h3>

<table>

<tr>

<th>

Obat Dipilih

</th>

<td>

<strong>

{{ $detail['obat'] ?? '-' }}

</strong>

</td>

</tr>

<tr>

<th>

Similarity

</th>

<td>

{{ number_format($detail['similarity'] ?? 0,2) }} %

</td>

</tr>

<tr>

<th>

Confidence

</th>

<td>

{{ $detail['confidence'] ?? '-' }}

</td>

</tr>

<tr>

<th>

Status Validasi

</th>

<td>

{{ $detail['admin_status'] ?? 'Menunggu Validasi' }}

</td>

</tr>

<tr>

<th>

Status PDF

</th>

<td>

@if($detail['pdf_ready'] ?? false)

Siap Diunduh

@else

Belum Tersedia

@endif

</td>

</tr>

</table>

<h3>

Obat Yang Disetujui Admin

</h3>

@if(!empty($detail['approved_meds']))

<table>

<thead>

<tr>

<th style="width:8%;">

No

</th>

<th>

Nama Obat

</th>

</tr>

</thead>

<tbody>

@foreach($detail['approved_meds'] as $index => $obat)

<tr>

<td>

{{ $index + 1 }}

</td>

<td>

{{ $obat }}

</td>

</tr>

@endforeach

</tbody>

</table>

@else

<p>

Belum ada obat yang disetujui Admin.

</p>

@endif

<h3>

Catatan Admin

</h3>

<table>

<tr>

<td>

{{ $detail['admin_conditions'] ?? 'Tidak ada catatan.' }}

</td>

</tr>

</table>
<br><br>

<table style="border:0; margin-top:50px;">

<tr style="border:0;">

<td style="border:0; width:50%; text-align:center;">

Mengetahui,

<br><br><br><br><br>

_________________________

<br>

<b>Administrator</b>

</td>

<td style="border:0; width:50%; text-align:center;">

Penerima,

<br><br><br><br><br>

_________________________

<br>

<b>{{ $detail['nama'] ?? 'Pengguna' }}</b>

</td>

</tr>

</table>

<hr>

<table style="border:0; margin-top:10px;">

<tr style="border:0;">

<td style="border:0;">

<b>ID Laporan</b>

</td>

<td style="border:0;">

:

{{ $detail['key'] ?? '-' }}

</td>

</tr>

<tr style="border:0;">

<td style="border:0;">

<b>Tanggal Cetak</b>

</td>

<td style="border:0;">

:

{{ date('d-m-Y H:i:s') }}

</td>

</tr>

<tr style="border:0;">

<td style="border:0;">

<b>Status</b>

</td>

<td style="border:0;">

:

{{ $detail['admin_status'] ?? 'Menunggu Validasi' }}

</td>

</tr>

</table>

<div class="footer">

<hr>

<p>

Dokumen ini dihasilkan secara otomatis oleh

<b>Sistem Rekomendasi Obat Apotek 24</b>

menggunakan metode

<b>TF-IDF</b>

dan

<b>Cosine Similarity</b>.

</p>

<p>

© {{ date('Y') }} Apotek 24

</p>

</div>

</body>

</html>