<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal KBM</title>
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
            font-size: 11px;
            page-break-inside: auto;
        }

        tr {
    page-break-inside: avoid;
    page-break-after: auto;
}

        td, th {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .title {
            margin-bottom: 20px;
            margin-top: 30px;
            text-align: center;
            font-size: 16px;
        }

        .section-title {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 4px;
        }

    </style>
</head>
<body>
<div style="width: 100%; text-align: center;">
    <div style="display: inline-block; text-align: left;">
    <div class="title">
        <p><strong>Jadwal Pelajaran Kelas {{ $kelas->nama }}</strong></p>
    </div>
    <div>
        <p><strong>Wali Kelas:</strong> {{ $kelas->walas->nama }}</p>
    </div>

    
        <table>
        @foreach ($jadwal as $hari => $items)
                <tr>
                <th rowspan="3" class="section-title" style="text-align: center; vertical-align: middle;">{{ $hari }}</th>
                    <td>Jam</td>
                    @foreach ($items as $index => $item)
                    <td>{{ \Carbon\Carbon::parse($item['mulai'])->format('H:i') }} - {{ \Carbon\Carbon::parse($item['selesai'])->format('H:i') }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>Mapel</td>
                    @foreach ($items as $index => $item)
                    <td>{{ $item['mapel']->nama }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>Guru</td>
                    @foreach ($items as $index => $item)
                    <td>{{ $item['guru']->nama }}</td>
                    @endforeach
                </tr>
                @endforeach
        </table>
    </div>
</div>
    

</body>
</html>
