<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Piket</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        td, th {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Jadwal Piket</h2>

    @php
        $rows = array_chunk($jadwal->toArray(), 3); // Tampilkan 3 kolom per baris
    @endphp

    <table>
        @foreach ($rows as $row)
            <tr>
                @foreach ($row as $item)
                    <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($row as $item)
                    <td>{{ \Carbon\Carbon::parse($item['mulai'])->format('H:i') }} - {{ \Carbon\Carbon::parse($item['selesai'])->format('H:i') }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>

</body>
</html>
