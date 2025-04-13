<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Siswa</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .title {
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .info {
            margin-top: 10px;
        }

        .info td {
            border: none;
            padding: 4px 8px;
        }

    </style>
</head>
<body>

    <div class="title">REKAP PRESENSI SISWA</div>

    <table class="info">
        <tr>
            <td><strong>Nama</strong></td>
            <td>: {{ $siswa->nama }}</td>
        </tr>
        <tr>
            <td><strong>NIS</strong></td>
            <td>: {{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ $siswa->kelas->nama }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Kelamin</strong></td>
            <td>: {{ $siswa->gender }}</td>
        </tr>
        <tr>
            <td><strong>No. Telp</strong></td>
            <td>: {{ $siswa->no_telp }}</td>
        </tr>
        <tr>
            <td><strong>Wali Murid</strong></td>
            <td>: {{ $siswa->walimurid }}</td>
        </tr>
        <tr>
            <td><strong>Alamat</strong></td>
            <td>: {{ $siswa->alamat }}</td>
        </tr>
    </table>

    <h4 style="margin-top: 20px;">Total Presensi:</h4>
    <table>
        <thead>
            <tr>
                <th class="text-center">Alfa</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Izin</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $total_alfa }}</td>
                <td class="text-center">{{ $total_sakit }}</td>
                <td class="text-center">{{ $total_izin }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
