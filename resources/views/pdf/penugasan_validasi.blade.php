<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Penempatan UT-D</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header-title {
            font-size: 14pt;
        }
        .header-subtitle {
            font-size: 12pt;
        }
        .wilayah-header {
            background-color: #000;
            color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            padding: 5px 0;
            margin-top: 5px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        td {
            text-transform: uppercase;
        }
        .text-center {
            text-align: center;
        }
        .group-container {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

    @foreach($groupedUtds as $wilayahName => $utds)
    <div class="group-container">
        @if(isset($kopBase64) && $kopBase64)
            <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px;">
                <img src="{{ $kopBase64 }}" style="width: 100%; height: auto; max-height: 150px; object-fit: contain;">
            </div>
        @else
            <div class="header">
                <div class="header-title">VALIDASI PENEMPATAN UT-D YAYASAN AL-MIFTAH</div>
                <div class="header-subtitle">PP. MIFTAHUL ULUM PANYEPPEN PALENGAAN PAMEKASAN MADURA</div>
                <div class="header-subtitle">MASA BAKTI {{ $tahunAjaran->nama_tahun_ajaran }}</div>
            </div>
        @endif

        <div class="wilayah-header">
            WILAYAH : {{ $wilayahName }}
        </div>

        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="8%">Kode Lembaga</th>
                    <th width="12%">Pengasuh / PJUT-D</th>
                    <th width="15%">Lembaga</th>
                    <th width="15%">Alamat</th>
                    <th width="15%">Nama UT-D</th>
                    <th width="12%">Wali UT-D</th>
                    <th width="15%">Alamat UT-D</th>
                    <th width="5%">Tugas Ke</th>
                </tr>
            </thead>
            <tbody>
                @foreach($utds as $index => $utd)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $utd->pjutd->kode_lembaga ?? '-' }}</td>
                    <td>{{ $utd->pjutd->nama_pjutd ?? '-' }}</td>
                    <td>{{ $utd->pjutd->nama_madrasah ?? ($utd->pjutd->nama_pjutd ?? '-') }}</td>
                    <td>{{ $utd->pjutd->alamat ?? '-' }}</td>
                    <td>{{ $utd->santri->nama ?? '-' }}</td>
                    <td>{{ $utd->santri->wali->nama_wali ?? '-' }}</td>
                    <td>{{ $utd->santri->alamat ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            // Calculate "Tugas Ke" by counting all utd records for this santri up to this year
                            // Assuming $utd->santri->utds is eager loaded and ordered, but a simple count of the relation is enough for now.
                            $tugasKe = $utd->santri ? $utd->santri->utds->count() : 1;
                        @endphp
                        {{ $tugasKe }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

</body>
</html>
