<?php

namespace App\Http\Controllers;

use App\Models\Wali;
use Illuminate\Http\Request;

class WaliController extends Controller
{
    public function byNik($nik)
    {
        $wali = Wali::where('nik', $nik)->first();
        if ($wali) {
            return response()->json(['status' => true, 'data' => $wali]);
        }
        return response()->json(['status' => false, 'message' => 'Wali tidak ditemukan']);
    }
}
