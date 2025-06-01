<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Per Hari</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 4px; text-align: center; }
    .text-left { text-align: left; }
    .header-row { background-color: #ddd; }

    /* Kurangi padding dan font size khusus kolom mapel dan baris kedua header */
    .header-row.second th {
        padding: 2px 3px;
        font-size: 8px;
        line-height: 1;
    }

    /* Kolom mapel di body juga padding kecil dan font kecil */
    tbody td.small-cell {
        padding: 2px 3px;
        font-size: 8px;
    }

    small {
        font-size: 7px;
        display: block;
        margin-top: 1px;
        line-height: 1;
    }
    </style>
</head>
<body>
    <h3>Rekap Presensi Kelas {{ $kelas->nama }}</h3>
    <p>Walas: {{ $kelas->walas->nama }}</p>

    <table>
        <thead>
            <tr class="header-row">
                <th rowspan="3">No</th>
                <th rowspan="3">NIS</th>
                <th rowspan="3">Nama Siswa</th>
                @foreach($jadwalPerHari as $hari => $jadwals)
                    <th colspan="{{ count($jadwals) }}">{{ $hari }}</th>
                @endforeach
            </tr>
            <tr class="header-row second">
                @foreach($jadwalPerHari as $jadwals)
                    @foreach($jadwals as $jadwal)
                        <th>
                            <small>{{ str_replace('Bahasa', 'B.', $jadwal->mapel->nama) }}</small>
                        </th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($kelas->siswa as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama }}</td>
                    @foreach($jadwalPerHari as $jadwals)
                        @foreach($jadwals as $jadwal)
                            @php
                                $status = $presensi
                                    ->where('siswa_id', $siswa->id)
                                    ->where('jadwal_id', $jadwal->id)
                                    ->first()
                                    ->kehadiran ?? '-';
                            @endphp
                            <td>
                                @if($status === 'Hadir')
                                    V
                                @elseif($status === 'Alfa')
                                    A
                                @elseif($status === 'Izin')
                                    I
                                @elseif($status === 'Sakit')
                                    S
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
