<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Laporan Admin</title>

    <style>

        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

    </style>

</head>

<body>

    <h2>
        Laporan Admin Apotek24
    </h2>

    <p>
        Total Produk: {{ $totalProduk }}
    </p>

    <p>
        Total User: {{ $totalUser }}
    </p>

    <p>
        Total Rekomendasi: {{ $totalRekomendasi }}
    </p>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Nama Obat</th>
                <th>Kategori</th>
                <th>Harga</th>

            </tr>

        </thead>

        <tbody>

            @foreach($produk as $index => $item)

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $item['nama'] ?? '-' }}
                    </td>

                    <td>
                        {{ $item['kategori'] ?? '-' }}
                    </td>

                    <td>
                        Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
</html>