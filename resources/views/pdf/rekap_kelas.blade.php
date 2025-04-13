<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Per Hari</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h3>Rekap Presensi Kelas {{ $kelas->nama }}</h3>
    <p>Walas: {{ $kelas->walas->nama }}</p>

    <table>
        <thead>
            <tr>
                <th rowspan="3">No</th>
                <th rowspan="3">NIS</th>
                <th rowspan="3">Nama Siswa</th>
                @foreach($jadwalPerHari as $hari => $jadwals)
                    <th colspan="{{ count($jadwals) }}">{{ $hari }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($jadwalPerHari as $jadwals)
                    @foreach($jadwals as $jadwal)
                        <th>{{ $jadwal->guru->nama }}</th>
                    @endforeach
                @endforeach
            </tr>
            <tr>
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
