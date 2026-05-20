<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class WilayahController extends Controller
{
    public function provinsi()
    {
        return response()->json(Provinsi::orderBy('nama')->get());
    }

    public function kabupaten($id_prov)
    {
        return response()->json(Kabupaten::where('provinsi_id', $id_prov)->orderBy('nama')->get());
    }

    public function kecamatan($id_kab)
    {
        return response()->json(Kecamatan::where('kabupaten_id', $id_kab)->orderBy('nama')->get());
    }

    public function kelurahan($id_kec)
    {
        return response()->json(Kelurahan::where('kecamatan_id', $id_kec)->orderBy('nama')->get());
    }

    public function parseNik($nik)
    {
        if (strlen($nik) !== 16) {
            return response()->json(['status' => false, 'message' => 'NIK harus 16 digit'], 400);
        }

        $prov_code = substr($nik, 0, 2);
        $kab_code = substr($nik, 0, 4);
        $kec_code = substr($nik, 0, 6);

        $prov = Provinsi::where('kode', $prov_code)->first();
        $kab = Kabupaten::where('kode', $kab_code)->first();
        $kec = Kecamatan::where('kode', $kec_code)->first();

        $datePart = (int) substr($nik, 6, 2);
        $monthPart = substr($nik, 8, 2);
        $yearPart = (int) substr($nik, 10, 2);

        $jenis_kelamin = 'L';
        if ($datePart > 40) {
            $jenis_kelamin = 'P';
            $datePart -= 40;
        }

        $dateStr = str_pad($datePart, 2, '0', STR_PAD_LEFT);
        
        $currentYear2Digits = (int) date('y');
        $fullYear = ($yearPart > $currentYear2Digits) ? 1900 + $yearPart : 2000 + $yearPart;

        $tanggal_lahir = "{$fullYear}-{$monthPart}-{$dateStr}";

        return response()->json([
            'status' => true,
            'data' => [
                'id_prov' => $prov ? $prov->id : null,
                'id_kab' => $kab ? $kab->id : null,
                'id_kec' => $kec ? $kec->id : null,
                'jenis_kelamin' => $jenis_kelamin,
                'tanggal_lahir' => $tanggal_lahir
            ]
        ]);
    }
}
