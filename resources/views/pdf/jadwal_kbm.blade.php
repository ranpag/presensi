<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal KBM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2 {
            margin-bottom: 0;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            word-wrap: break-word;
            text-align: center;
        }
        .jadwal-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="info">
        <h2>Info Kelas</h2>
        <p><strong>Kelas:</strong> {{ $kelas->nama }}</p>
        <p><strong>Wali Kelas:</strong> {{ $kelas->walas->nama }}</p>
    </div>

    <h2>Jadwal KBM</h2>

    <table>
        <thead>
            <tr>
                @foreach ($jadwal as $hari => $items)
                    <th>{{ $hari }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(array_map(fn($item) => count($item), $jadwal));
            @endphp

            @for ($i = 0; $i < $maxRows; $i++)
                <tr>
                    @foreach ($jadwal as $hari => $items)
                        <td>
                            @if (isset($items[$i]))
                                <div class="jadwal-item">
                                    <strong>{{ $items[$i]['mapel']->nama }}</strong><br>
                                    {{ \Carbon\Carbon::parse($items[$i]['mulai'])->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($items[$i]['selesai'])->format('H:i') }}
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>
