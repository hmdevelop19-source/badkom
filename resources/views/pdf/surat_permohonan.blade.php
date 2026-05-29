<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan {{ $surat->jenis_permohonan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            margin: 20px 40px;
        }
        .header-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .kop-kiri {
            float: left;
            width: 50%;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        .kop-kiri h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .kop-kiri .dotted-line {
            border-bottom: 2px dotted #000;
            margin: 10px 20px;
            height: 1px;
        }
        .kop-kiri .disclaimer {
            font-size: 9px;
            text-align: left;
            line-height: 1.2;
            color: red; /* Text color is red in image, but standard print is black. I'll use black/red depending on the need. Image has some red squiggly lines but that's just spell check. The actual text in image is black with red squiggly. Wait, looking closely, some text might be red or black. The red squiggles are from Word. I will use black */
            font-style: italic;
        }
        .kop-kanan {
            float: right;
            width: 40%;
            position: relative;
        }
        .badge {
            background-color: {{ $surat->jenis_permohonan == 'Baru' ? '#5b9bd5' : '#70ad47' }};
            color: #000;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            padding: 20px;
            border: 1px solid #000;
            box-shadow: 10px 10px 0px rgba(0,0,0,0.5); /* shadow effect like in image */
        }
        .clear {
            clear: both;
        }
        .content {
            margin-top: 40px;
        }
        table.form-table {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        table.form-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .checkbox-item {
            margin-bottom: 5px;
        }
        .checkbox-box {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            vertical-align: middle;
            margin-right: 10px;
            text-align: center;
            line-height: 15px;
            font-size: 12px;
        }
        .checkbox-group {
            display: inline-block;
            margin-right: 20px;
        }
        .signature-container {
            width: 100%;
            margin-top: 50px;
        }
        .sig-left {
            float: left;
            width: 50%;
            text-align: left;
        }
        .sig-right {
            float: right;
            width: 50%;
            text-align: right;
        }
        .sig-line {
            display: inline-block;
            border-bottom: 1px dashed #000;
            width: 200px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="kop-kiri">
            <h3>BADKOM WILAYAH:</h3>
            <div class="dotted-line"></div>
            <div class="disclaimer">
                Formulir ini sebagai bukti daftar untuk dimasukkan kedalam daftar pemohon ustadz tugas tahun {{ $surat->tahunAjaran->nama_tahun_ajaran ?? '144.../144...' }} H. Formulir ini bukan sebagai tanda jaminan untuk mendapatkan ustadz tugas, tanpa formulir ini pengurus Yayasan tidak mempunyai wewenang untuk mencantumkan nama lembaga/PJUT-D sebagai pemohon bantuan ustadz tugas Ya'mi.
            </div>
        </div>
        <div class="kop-kanan">
            <div class="badge">
                FORMULIR<br>PERMOHONAN<br>{{ strtoupper($surat->jenis_permohonan) }}
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="content">
        <p>Yang bertanda tangan dibawah ini:</p>
        
        <table class="form-table" style="width: 80%; margin-left: 20px;">
            <tr>
                <td width="150">Nama</td>
                <td width="10">:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pemohon_nama }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pemohon_umur }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pemohon_jabatan }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pemohon_alamat }}</td>
            </tr>
        </table>

        <p>Dengan ini kami mohon agar lembaga kami diberi bantuan ustadz tugas untuk masa khidmat {{ $surat->tahunAjaran->nama_tahun_ajaran ?? '144.../144...' }} H. Adapun data madrasah / lembaga kami sebagai berikut:</p>

        <table class="form-table" style="width: 100%; margin-left: 20px;">
            <tr>
                <td width="150">Nama Lembaga</td>
                <td width="10">:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pjutd_nama_lembaga }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pjutd_alamat }}</td>
            </tr>
            <tr>
                <td>Nama Kepala Lembaga</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;">{{ $surat->pjutd_nama_kepala }}</td>
            </tr>
            <tr>
                <td>Kurikulum</td>
                <td>:</td>
                <td style="border-bottom: 1px dotted #000;"><i>Ala {{ $surat->pjutd_kurikulum ?? 'Pesantren/Depag/Diknas/Terpadu' }}</i></td>
            </tr>
        </table>

        <p style="font-weight: bold; font-style: italic;"><u>Kriteria Ustadz yang diinginkan (pilih satu saja):</u></p>
        
        <div class="checkbox-item">
            <span class="checkbox-box">{{ $surat->kriteria_ustadz == 'diniyah_umumiyah' ? '✓' : '' }}</span>
            1. Bisa membantu untuk pendidikan Diniyah dan Umumiyah
        </div>
        <div class="checkbox-item">
            <span class="checkbox-box">{{ $surat->kriteria_ustadz == 'diniyah' ? '✓' : '' }}</span>
            2. Bisa membantu untuk pendidikan Diniyah
        </div>
        <div class="checkbox-item">
            <span class="checkbox-box">{{ $surat->kriteria_ustadz == 'umumiyah' ? '✓' : '' }}</span>
            3. Bisa membantu untuk pendidikan Umumiyah
        </div>

        <br>
        <p style="font-weight: bold; font-style: italic;"><u>Fasilitas yang disediakan:</u></p>

        <table style="width: 100%;">
            <tr>
                <td width="60%">1. Tempat tinggal yang terpisah dari rumah PJUT-D</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_tempat_tinggal ? '✓' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_tempat_tinggal ? '✓' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>2. Kamar mandi dan tempat wudhu'</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_kamar_mandi ? '✓' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_kamar_mandi ? '✓' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>3. Tempat buang air kecil/besar (WC)</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_wc ? '✓' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_wc ? '✓' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>4. Bisyaroh setiap bulan sesuai dengan ketentuan</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_bisyaroh ? '✓' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_bisyaroh ? '✓' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>5. Konsumsi dan pengobatan</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_konsumsi ? '✓' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_konsumsi ? '✓' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
        </table>

        <div class="signature-container">
            <div class="sig-left">
                Pemohon:
                <br><br><br><br>
                <div class="sig-line"></div><br>
                <i style="font-size: 11px;">(Nama, Tanda Tangan dan Stempel)</i>
            </div>
            <div class="sig-right" style="text-align: center; width: 250px; float: right;">
                Badkom Wilayah
                <br><br><br><br>
                <div style="border-bottom: 1px dashed #000; width: 200px; margin: 0 auto;"></div>
            </div>
            <div class="clear"></div>
        </div>

        @if($surat->jenis_permohonan == 'Perpanjangan')
        <div style="margin-top: 40px; text-align: center;">
            <p style="font-weight: bold; margin-bottom: 20px;">TIPE UTAMA USTADZ TUGAS YANG DIINGINKAN<br>(BAKAT DAN KEMAMPUAN)</p>
            <div style="text-align: left; margin: 0 auto; width: 80%;">
                <table class="form-table">
                    <tr>
                        <td width="20">1.</td>
                        <td style="border-bottom: 1px dotted #000;">{{ $surat->bakat_kemampuan_1 }}</td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td style="border-bottom: 1px dotted #000;">{{ $surat->bakat_kemampuan_2 }}</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td style="border-bottom: 1px dotted #000;">{{ $surat->bakat_kemampuan_3 }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>

</body>
</html>
