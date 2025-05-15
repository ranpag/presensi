<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Piket</title>
    <style>
    @page {
        size: A4;
        margin: 1cm;
    }

    body {
        font-family: sans-serif;
        font-size: 12px;
        margin: 0 auto;
        padding: 1cm;
        box-sizing: border-box;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        table-layout: fixed;
        font-size: 11px;
        page-break-inside: auto;
    }

    td, th {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
        word-wrap: break-word;
    }

    h2 {
        text-align: center;
    }
</style>

</head>
<body>

    <div style="width: 100%; text-align: center;">
        <div style="display: inline-block; text-align: left;"   >
        <h2 style="text-align: center;">Jadwal Piket</h2>
    <div>
        <p><strong>Nama:</strong> {{ $guru->nama }}</p>
    </div>

    @php
        $rows = array_chunk($jadwal->toArray(), 3);
    @endphp

    <table>
        @foreach ($rows as $row)
        <tr>
            <td>Tanggal</td>
            <td>Jam</td>
        </tr>
        @foreach ($row as $item)
        
            <tr>
                
                    <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item['mulai'])->format('H:i') }} - {{ \Carbon\Carbon::parse($item['selesai'])->format('H:i') }}</td>
                
            </tr>
            @endforeach
        @endforeach
    </table>
        </div>
    </div>

</body>
</html>
