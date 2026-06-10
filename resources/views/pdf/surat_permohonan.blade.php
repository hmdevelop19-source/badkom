<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan {{ $surat->jenis_permohonan }}</title>
    <style>
        @page {
            margin: 20px 50px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-container {
            width: 100%;
            margin-bottom: 5px;
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
            font-size: 16px;
            text-align: center;
            padding: 15px;
            border: 1px solid #000;
            box-shadow: 8px 8px 0px rgba(0,0,0,0.5); /* shadow effect like in image */
        }
        .clear {
            clear: both;
        }
        .content {
            margin-top: 20px;
        }
        table.form-table {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        table.form-table td {
            vertical-align: top;
            padding: 1px 0;
        }
        .checkbox-item {
            margin-bottom: 2px;
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
            margin-top: 10px;
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
            <h3>BADKOM WILAYAH: {{ strtoupper($surat->badkom->nama_badkom ?? $surat->pjutd->badkom->nama_badkom ?? '') }}</h3>
            <div class="dotted-line"></div>
            <div class="disclaimer">
                Formulir ini sebagai bukti daftar untuk dimasukkan kedalam daftar pemohon ustadz tugas tahun {{ $surat->jenis_permohonan == 'Perpanjangan' && $surat->tahun_ajaran_tujuan ? $surat->tahun_ajaran_tujuan : ($surat->tahunAjaran->nama_tahun_ajaran ?? '144.../144...') }} H. Formulir ini bukan sebagai tanda jaminan untuk mendapatkan ustadz tugas, tanpa formulir ini pengurus Yayasan tidak mempunyai wewenang untuk mencantumkan nama lembaga/PJUT-D sebagai pemohon bantuan ustadz tugas Ya'mi.
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
                <td style="{{ empty($surat->pemohon_nama) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pemohon_nama }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td style="{{ empty($surat->pemohon_umur) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pemohon_umur }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td style="{{ empty($surat->pemohon_jabatan) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pemohon_jabatan }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="{{ empty($surat->pemohon_alamat) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pemohon_alamat }}</td>
            </tr>
        </table>

        <p>Dengan ini kami mohon agar lembaga kami diberi bantuan ustadz tugas untuk masa khidmat {{ $surat->jenis_permohonan == 'Perpanjangan' && $surat->tahun_ajaran_tujuan ? $surat->tahun_ajaran_tujuan : ($surat->tahunAjaran->nama_tahun_ajaran ?? '144.../144...') }} H. Adapun data madrasah / lembaga kami sebagai berikut:</p>

        <table class="form-table" style="width: 100%; margin-left: 20px;">
            <tr>
                <td width="150">Nama Lembaga</td>
                <td width="10">:</td>
                <td style="{{ empty($surat->pjutd_nama_lembaga) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pjutd_nama_lembaga }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td style="{{ empty($surat->pjutd_alamat) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pjutd_alamat }}</td>
            </tr>
            <tr>
                <td>Nama Kepala Lembaga</td>
                <td>:</td>
                <td style="{{ empty($surat->pjutd_nama_kepala) ? 'border-bottom: 1px dotted #000;' : '' }}">{{ $surat->pjutd_nama_kepala }}</td>
            </tr>
            <tr>
                <td>Kurikulum</td>
                <td>:</td>
                <td style="{{ empty($surat->pjutd_kurikulum) ? 'border-bottom: 1px dotted #000;' : '' }}"><i>Ala {{ $surat->pjutd_kurikulum ?? 'Pesantren/Depag/Diknas/Terpadu' }}</i></td>
            </tr>
        </table>

        <p style="font-weight: bold; font-style: italic;"><u>Kriteria Ustadz yang diinginkan (pilih satu saja):</u></p>
        
        <div class="checkbox-item">
            <span class="checkbox-box">{{ $surat->kriteria_ustadz == 'diniyah_umumiyah' ? 'V' : '' }}</span>
            1. Bisa membantu untuk pendidikan Diniyah dan Umumiyah
        </div>
        <div class="checkbox-item">
            <span class="checkbox-box">{{ $surat->kriteria_ustadz == 'diniyah' ? 'V' : '' }}</span>
            2. Bisa membantu untuk pendidikan Diniyah
        </div>
        <div class="checkbox-item">
            <span class="checkbox-box">{{ $surat->kriteria_ustadz == 'umumiyah' ? 'V' : '' }}</span>
            3. Bisa membantu untuk pendidikan Umumiyah
        </div>

        <p style="font-weight: bold; font-style: italic; margin-top: 10px;"><u>Fasilitas yang disediakan:</u></p>

        <table style="width: 100%;">
            <tr>
                <td width="60%">1. Tempat tinggal yang terpisah dari rumah PJUT-D</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_tempat_tinggal ? 'V' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_tempat_tinggal ? 'V' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>2. Kamar mandi dan tempat wudhu'</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_kamar_mandi ? 'V' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_kamar_mandi ? 'V' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>3. Tempat buang air kecil/besar (WC)</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_wc ? 'V' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_wc ? 'V' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>4. Bisyaroh setiap bulan sesuai dengan ketentuan</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_bisyaroh ? 'V' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_bisyaroh ? 'V' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
            <tr>
                <td>5. Konsumsi dan pengobatan</td>
                <td>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ $surat->fasilitas_konsumsi ? 'V' : '' }}</span> ada
                    </div>
                    <div class="checkbox-group">
                        <span class="checkbox-box">{{ !$surat->fasilitas_konsumsi ? 'V' : '' }}</span> tidak ada
                    </div>
                </td>
            </tr>
        </table>

        <div class="signature-container">
            <div class="sig-left">
                Pemohon:
                <br><br><br>
                <div class="sig-line" style="text-align: center; {{ !empty($surat->pemohon_nama) ? 'border-bottom: none;' : '' }}">{{ $surat->pemohon_nama }}</div><br>
                <i style="font-size: 11px;">(Nama, Tanda Tangan dan Stempel)</i>
            </div>
            <div class="sig-right" style="text-align: center; width: 250px; float: right;">
                Badkom Wilayah {{ $surat->badkom->nama_badkom ?? $surat->pjutd->badkom->nama_badkom ?? '' }}
                <br><br><br>
                @php
                    $namaPj = $surat->badkom->nama_pj ?? $surat->pjutd->badkom->nama_pj ?? '';
                @endphp
                <div style="width: 200px; margin: 0 auto; {{ empty($namaPj) ? 'border-bottom: 1px dashed #000;' : '' }}">{{ $namaPj }}</div>
            </div>
            <div class="clear"></div>
        </div>

        @if($surat->jenis_permohonan == 'Perpanjangan')
        <div style="margin-top: 10px; text-align: center;">
            <p style="font-weight: bold; margin-bottom: 5px;">TIPE UTAMA USTADZ TUGAS YANG DIINGINKAN<br>(BAKAT DAN KEMAMPUAN)</p>
            <div style="text-align: left; margin: 0 auto; width: 80%;">
                <table class="form-table">
                    @if(is_array($surat->bakat_kemampuan))
                        @foreach($surat->bakat_kemampuan as $index => $bakat)
                            @if(!empty(trim($bakat)))
                            <tr>
                                <td width="20">{{ $index + 1 }}.</td>
                                <td>{{ $bakat }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
        @endif
    </div>

</body>
</html>
