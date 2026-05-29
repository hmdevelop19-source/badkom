<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Laporan Insidental</title>
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
            font-size: 20pt;
            margin: 0;
            color: #000080;
            letter-spacing: 2px;
        }
        .header-title p {
            margin: 0;
            font-size: 9pt;
            color: #000080;
        }
        .surat-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
        }
        table.biodata {
            margin-left: 0px;
            margin-bottom: 15px;
            width: 100%;
        }
        table.biodata td {
            vertical-align: top;
            padding: 3px 0;
        }
        .ttd {
            width: 100%;
            margin-top: 40px;
        }
        .ttd-kanan {
            float: right;
            text-align: center;
            width: 300px;
        }
        .isi-laporan {
            min-height: 200px;
            border-bottom: 1px dotted #000;
            margin-bottom: 10px;
        }
        .footer-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30px;
            background-color: #000080;
            color: white;
            font-size: 8pt;
            text-align: right;
            padding-right: 20px;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    @if(isset($kopBase64) && $kopBase64)
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px;">
            <img src="{{ $kopBase64 }}" style="width: 100%; height: auto; max-height: 180px; object-fit: contain;">
        </div>
    @else
        <div class="header">
            <div class="header-title">
                <h1 style="display:inline;">BADKOM</h1>
                <p>Badan Komunikasi Pendidikan dan Dakwah</p>
                <p><strong>YAYASAN AL-MIFTAH PP. Miftahul Ulum Panyeppen</strong></p>
                <div style="text-align: right; margin-top: -45px; font-style: italic; font-size: 9pt;">
                    Ustadz Tugas & Da'i Zakat<br>
                    Penanggung Jawab Ustadz Tugas & Da'i<br>
                    Madrasah Ranting<br>
                    Pendidikan & Dakwah
                </div>
            </div>
        </div>
    @endif

    <div class="surat-title">
        SURAT LAPORAN INSIDENTAL
    </div>

    <div class="content">
        <p style="margin-bottom: 0;">Kepada Yth.</p>
        <p style="font-weight: bold; margin-top: 0; margin-bottom: 0;">Badan Komunikasi Yayasan Al-Miftah</p>
        <p style="font-weight: bold; margin-top: 0; margin-bottom: 0;">PP. Miftahul Ulum Panyeppen</p>
        <p style="margin-top: 0;">di Tempat</p>

        <p style="text-align: center; font-style: italic;">Assalamu'alaikum Wr. Wb.</p>

        <p style="text-indent: 40px;">
            Dengan hormat, bersama ini kami menyampaikan laporan kejadian yang terjadi di lembaga sebagai berikut:
        </p>
        
        <table class="biodata">
            <tr>
                <td width="150">Nama PJ UT-D</td>
                <td width="10">:</td>
                <td>{{ $namaPjutd ?? '......................................................................' }}</td>
            </tr>
            <tr>
                <td>Nama Lembaga</td>
                <td>:</td>
                <td>{{ $namaLembaga ?? '......................................................................' }}</td>
            </tr>
            <tr>
                <td>Alamat Lembaga</td>
                <td>:</td>
                <td>{{ $alamatLembaga ?? '......................................................................' }}</td>
            </tr>
            <tr>
                <td>Nama UT-D</td>
                <td>:</td>
                <td>{{ $namaUtd ?? '......................................................................' }}</td>
            </tr>
            <tr>
                <td>Badkom Wilayah</td>
                <td>:</td>
                <td>{{ $badkomWilayah ?? '......................................................................' }}</td>
            </tr>
        </table>

        <p>Isi Laporan:</p>
        <div class="isi-laporan">
            {!! nl2br(e($isiLaporan)) !!}
            <br><br><br>
            .........................................................................................................................................................<br>
            .........................................................................................................................................................<br>
            .........................................................................................................................................................
        </div>

        <p style="text-indent: 40px;">
            Demikian laporan ini kami sampaikan untuk menjadi perhatian dan tindak lanjut sebagaimana mestinya. Atas perhatian dan kerja samanya kami sampaikan terima kasih.
        </p>
        
        <p style="margin-bottom: 0;">Wassalamu'alaikum Wr. Wb.</p>

        <div class="ttd">
            <div class="ttd-kanan">
                {{ $lokasi ?? '.....................................' }}, {{ $tanggal }}<br><br>
                Hormat kami,<br>
                {{ $gelarPenandatangan }}<br><br><br><br><br>
                ( {{ $namaPenandatangan }} )
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

</body>
</html>
