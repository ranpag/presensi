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
    <title>Surat Peringatan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center;}
        .header h2 { margin: 0; color: green; }
        .header p { margin: 0; }
        .bold { font-weight: bold; }
        .signature { margin-top: 30px; width: 100%; display: flex; justify-content: space-between; }
        .signature div { text-align: center; width: 45%; }
    </style>
</head>
<body>

    <table width="100%">
        <tr>
            <td style="width: 30%; text-align: left;">
                <img src="{{ $base64 }}" alt="Logo Sekolah" width="150">
            </td>
            <td style="width: 70%; text-align: right;">
                <div class="header">
                    <h2>YAYASAN PENDIDIKAN ISLAM AL-HIKMAH</h2>
                    <h3>SMA AL-HIKMAH</h3>
                    <p>Jln. Abd. Fatah Tapos II Tenjolaya Bogor 16620 Telp. (0251) 8628924</p>                        <hr>
                </div>
            </td>
        </tr>
    </table>

    <br>

    <p>Nomor : 040 / SMA / A-H / X / 2017</p>
    <p>Perihal : <b>Surat Peringatan 1 (SP 1) / Teguran</b></p>

    <p>Kepada</p>
    <p class="bold">ABDUL HAMID</p>
    <p>Kelas X-2</p>
    <p>Di SMA Al-Hikmah</p>

    <p><b>Assalamualaikum Warahmatullahi Wabarakatuh.</b></p>

    <p>
        Sehubungan dengan adanya tindakan indisipliner yang saudara lakukan sebanyak yang tertera di bawah,
        maka pihak sekolah memberikan sanksi berupa surat teguran. Hal ini berdasarkan jumlah seringnya saudara 
        tidak masuk sekolah, tidak membawa atribut, dan tidak mengerjakan PR yang diberikan beberapa guru.
    </p>

    <p>
        Dengan dikeluarkannya surat teguran ini, diharapkan saudara dapat introspeksi diri dan memperbaiki diri dalam hal kedisiplinan.
        Surat teguran ini berlaku selama dua (2) minggu dimulai sejak tanggal ditetapkan. Selama itu diharapkan saudara dapat memperhatikan 
        dan memberikan tindakan perbaikan diri. Jika, selama atau sesudah surat teguran ini diberikan kepada saudara masih melakukan pelanggaran, 
        maka akan diberikan surat teguran yang kedua dan pemanggilan Orang tua/Wali Murid.
    </p>

    <p>Demikian Surat Teguran ini dibuat. Atas perhatian dan kerja sama saudara, kami mengucapkan terima kasih.</p>

    <p><b>Wassalamualaikum Warahmatullahi Wabarakatuh.</b></p>

    <div class="signature">
        <div>
            <p>Mengetahui,</p>
            <p><b>Kepala SMA Al-Hikmah</b></p>
            <br><br>
            <p><b>Fuad Wahyudi, S.Ag.</b></p>
        </div>
        <div>
            <p>Bogor, 26 Oktober 2017</p>
            <p><b>Wali Kelas X-2</b></p>
            <br><br>
            <p><b>Muh Ridwan Firdaus, S.Pd.</b></p>
        </div>
    </div>

</body>
</html>
