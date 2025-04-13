<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Presensi Kosong</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        .text-left { text-align: left; }
        .header-row { background-color: #ddd; }
        small {
            font-size: 8px;
            display: block;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <h3>Presensi Kosong Kelas {{ $kelas->tingkatan }} - {{ $kelas->nama }}</h3>
    <p><strong>Wali Kelas:</strong> {{ $walas->nama }}</p>

    <table>
        <thead>
            <tr class="header-row">
                <th rowspan="2">No</th>
                <th rowspan="2">NIS</th>
                <th rowspan="2">Nama Siswa</th>
                @foreach ($hariList as $hari)
                    @php $jumlahGuru = count($jadwalPerHari[$hari]); @endphp
                    <th colspan="{{ $jumlahGuru > 0 ? $jumlahGuru : 1 }}">{{ $hari }}</th>
                @endforeach
            </tr>
            <tr class="header-row">
                @foreach ($hariList as $hari)
                    @forelse ($jadwalPerHari[$hari] as $jadwal)
                        <th>
                            {{ $jadwal->guru->nama }}<br>
                            <small>{{ str_replace('Bahasa', 'B.', $jadwal->mapel->nama) }}</small>
                        </th>
                    @empty
                        <th>-</th>
                    @endforelse
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($siswa as $index => $s)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $s->nis }}</td>
                    <td class="text-left">{{ $s->nama }}</td>
                    @foreach ($hariList as $hari)
                        @forelse ($jadwalPerHari[$hari] as $jadwal)
                            <td></td> {{-- Kolom kosong untuk presensi manual --}}
                        @empty
                            <td>-</td>
                        @endforelse
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
