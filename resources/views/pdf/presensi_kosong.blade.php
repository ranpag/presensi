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
            <tr class="header-row second">
                @foreach ($hariList as $hari)
                    @forelse ($jadwalPerHari[$hari] as $jadwal)
                        <th>
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
