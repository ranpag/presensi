@php
    $path = storage_path('app/private/mts.jpg');
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Sanksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; }
        .header h2, .header h3 { margin: 0; color: green; }
        .header p { margin: 0; }
        .bold { font-weight: bold; }
        .signature { text-align: center; }
        .siswa { width: 80%; border-collapse: collapse; margin: 30px auto; }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td style="width: 30%; text-align: left;">
                <img src="{{ $base64 }}" alt="Logo Sekolah" width="100">
            </td>
            <td style="width: 70%; text-align: right;">
                <div class="header">
                    <h2>MTS.MIFTAHUL ULUM KANIGORO</h2>
                    <p>Jl. Sultan Agung No.07, Ngipek, Kanigoro, Kec. Pagelaran, Kabupaten Malang, Jawa Timur 65174 Telp. (0251) 8628924</p>
                </div>
            </td>
        </tr>
    </table>

    <hr>

    <div style="margin-top: 50px;">
        <p>Nomor : 041 / MTS / A-H / {{ date('Y') }}</p>
        <p>Perihal : <b>Surat Sanksi</b></p>
        <p>Kepada</p>
        <p class="bold">{{ $siswa->walimurid }}</p>
        <p>Di Mts.Miftahul Ulum Kanigoro</p>
        <p style="margin-bottom: 30px;margin-top: 30px;"><b>Assalamualaikum Warahmatullahi Wabarakatuh.</b></p>
        <p>
            Berdasarkan catatan absensi, saudara telah melakukan pelanggaran berupa tidak masuk sekolah tanpa keterangan
            (alfa) dibeberapa mata pelajaran. Oleh karena itu, sekolah memberikan sanksi sebagai bentuk pembinaan
            agar saudara lebih disiplin dalam menjalankan kewajiban sebagai siswa.
        </p>
        <table class="siswa">
            <tr>
                <td style="width: 30%; text-align: left;">Nama</td>
                <td style="width: 2%; text-align: left;">:</td>
                <td style="width: 80%; text-align: left;">{{ $siswa->nama }}</td>
            </tr>
            <tr>
                <td>Jenjang</td>
                <td>:</td>
                <td>{{ $siswa->kelas->tingkatan }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td>{{ $siswa->kelas->nama }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $siswa->alamat }}</td>
            </tr>
        </table>
        <p>
            Kami berharap dengan adanya surat sanksi ini, saudara dapat memperbaiki diri dan tidak mengulangi kesalahan serupa.
            Jika pelanggaran ini masih berlanjut, maka pihak sekolah akan mengambil tindakan lebih lanjut sesuai dengan peraturan sekolah.
        </p>
        <p>Demikian surat ini dibuat untuk dapat diperhatikan dan dipatuhi dengan baik.</p>
        <p style="margin-top: 30px;"><b>Wassalamualaikum Warahmatullahi Wabarakatuh.</b></p>
    </div>


    <table width="100%" style="margin-top: 70px;">
        <tr>
            <td style="width: 50%; text-align: left;">
                <div class="signature">
                    <p>Mengetahui,</p>
                    <p><b>Kepala Sekolah</b></p>
                    <br><br>
                    <p><b>Fuad Wahyudi, S.Ag.</b></p>
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="signature">
                    <p>Bogor, {{ date('d F Y') }}</p>
                    <p><b>Wali Kelas {{ $siswa->kelas->walas->nama }}</b></p>
                    <br><br>
                    <p><b>Muh Ridwan Firdaus, S.Pd.</b></p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
