<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lulus Tugas - {{ $santri->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            position: relative;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }
        .header-title {
            margin-left: 90px;
            text-align: left;
        }
        .header-title h1 {
            font-size: 16pt;
            margin: 0;
            color: #000080;
        }
        .header-title p {
            margin: 0;
            font-size: 10pt;
            color: #000080;
        }
        .surat-title {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .surat-title span {
            text-decoration: underline;
        }
        .surat-nomor {
            font-weight: normal;
        }
        .content {
            text-align: justify;
        }
        table.biodata {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        table.biodata td {
            vertical-align: top;
        }
        table.tugas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.tugas th, table.tugas td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        table.tugas th {
            font-weight: bold;
        }
        .ttd {
            width: 100%;
            margin-top: 30px;
        }
        .ttd-kanan {
            float: right;
            text-align: center;
            width: 300px;
        }
        table.keterangan-nilai {
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 9pt;
        }
        table.keterangan-nilai th, table.keterangan-nilai td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
        }
        .footer-note {
            font-size: 8pt;
            color: red;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <!-- Assume logo is stored in public/images/logo.png, but DomPDF requires absolute path or base64. Let's omit the actual image src for now or use a placeholder if not available. -->
        <div class="header-title">
            <h1>BADKOM</h1>
            <p>Badan Komunikasi dan Hubungan Masyarakat</p>
            <p>YAYASAN AL-MIFTAH</p>
            <div style="text-align: right; margin-top: -45px; font-style: italic;">
                Alamat Tugas & Da'i<br>
                Penanggung Jawab Markaz Tugas & Da'i<br>
                Madrasah Diniyah<br>
                Pendidikan & Dakwah
            </div>
        </div>
    </div>

    <div class="surat-title">
        <span>SURAT KETERANGAN LULUS TUGAS</span><br>
        <div class="surat-nomor">Nomor : ...... /UT-D/BADKOM/YALMI/XII/1447/H</div>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Koordinator Ustadz Tugas & Da'i (UT-D) Yayasan Al-Miftah Pondok Pesantren Miftahul Ulum Panyeppen Pamekasan, menerangkan bahwa :</p>
        
        <table class="biodata">
            <tr>
                <td width="150">Nama</td>
                <td width="10">:</td>
                <td>{{ $santri->nama }}</td>
            </tr>
            <tr>
                <td>Tetala</td>
                <td>:</td>
                <td>{{ $santri->tempat_lahir }}, {{ \Carbon\Carbon::parse($santri->tanggal_lahir)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td>{{ $umur }}</td>
            </tr>
            <tr>
                <td>Wali (Ayah / Ibu)</td>
                <td>:</td>
                <td>{{ $santri->nama_ortu ?? '........................' }}</td>
            </tr>
            <tr>
                <td>Alamat Lengkap</td>
                <td>:</td>
                <td>{{ $alamatLengkap }}</td>
            </tr>
            <tr>
                <td>Tahun Mondok</td>
                <td>:</td>
                <td>........................</td>
            </tr>
            <tr>
                <td>Tahun Tugas</td>
                <td>:</td>
                <td>........................</td>
            </tr>
            <tr>
                <td>Pernah menjalani tugas</td>
                <td>:</td>
                <td>Wajib <strong>{{ $wajibCount }}</strong> kali dan Tathowwu <strong>{{ $tathowwuCount }}</strong> kali</td>
            </tr>
        </table>

        <p>Data tempat tugas yang bersangkutan sebagai berikut:</p>
        <table class="tugas">
            <thead>
                <tr>
                    <th width="30">No.</th>
                    <th>Tempat dan Tahun Tugas</th>
                    <th width="80">Nilai</th>
                    <th width="100">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allTugas as $index => $tugas)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">
                        {{ $tugas->pjutd->nama_pjutd }}<br>
                        Tahun Pendidikan {{ $tugas->tahunAjaran->nama_tahun_ajaran }}
                    </td>
                    <td>{{ $tugas->penilaian->predikat ?? '-' }}</td>
                    <td>{{ $tugas->penilaian ? ($tugas->penilaian->predikat == 'A' ? 'Sangat Baik' : ($tugas->penilaian->predikat == 'B' ? 'Baik' : ($tugas->penilaian->predikat == 'C' ? 'Cukup' : 'Kurang'))) : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">Belum ada data tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <p>
            Berdasarkan hasil yang dicapai selama melaksanakan Tugas/Dakwah, maka yang bersangkutan dinyatakan <strong>LULUS</strong>, dengan predikat <strong>{{ $predikatAkhir }} ({{ $keteranganPredikat }})</strong>. Semoga pengabdian yang bersangkutan dapat bernilai ibadah di sisi Allah SWT. Amin.
        </p>
        <p>
            Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="ttd">
            <div class="ttd-kanan">
                Pamekasan, {{ $tanggal }}<br><br>
                Koordinator Ustadz Tugas & Da'i<br>
                BADKOM Yayasan Al-Miftah<br>
                PP. Miftahul Ulum Panyeppen<br><br><br><br><br>
                <strong>{{ $namaKoordinator }}</strong>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div>
            Keterangan Nilai
            <table class="keterangan-nilai">
                <tr><th>No</th><th>Predikat</th><th>Nilai</th></tr>
                <tr><td>1</td><td>A</td><td>91 - 100</td></tr>
                <tr><td>2</td><td>B</td><td>81 - 90</td></tr>
                <tr><td>3</td><td>C</td><td>60 - 80</td></tr>
                <tr><td>4</td><td>D</td><td>0 - 59</td></tr>
            </table>
        </div>

        <div class="footer-note">
            Catatan : Apabila terdapat kesalahan dalam pembuatan nilai atau Predikat maka akan ditinjau kembali di kemudian hari.
        </div>
    </div>

</body>
</html>
