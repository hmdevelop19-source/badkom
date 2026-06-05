<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        if ($user->level === 'utd') {
            $user->load('santri.wali');
        }
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => 'sometimes|required|string|unique:users,username,' . $user->id,
            'fullname' => 'sometimes|required|string',
            'old_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            'santri_nis' => 'nullable|string',
            'santri_nik' => 'nullable|string',
            'santri_tempat_lahir' => 'nullable|string',
            'santri_tanggal_lahir' => 'nullable|date',
            'santri_alamat' => 'nullable|string',
            'santri_id_prov' => 'nullable|integer',
            'santri_id_kab' => 'nullable|integer',
            'santri_id_kec' => 'nullable|integer',
            'santri_id_kel' => 'nullable|integer',
            
            'wali_nik' => 'nullable|string',
            'wali_nama' => 'nullable|string',
            'wali_no_hp' => 'nullable|string',
        ]);

        $updateData = [];
        if (isset($validated['username'])) $updateData['username'] = $validated['username'];
        if (isset($validated['fullname'])) $updateData['fullname'] = $validated['fullname'];

        // Check if password change is requested
        if (!empty($validated['password'])) {
            if (empty($validated['old_password'])) {
                return response()->json([
                    'message' => 'Password lama wajib diisi untuk mengubah password.',
                    'errors' => ['old_password' => ['Password lama wajib diisi.']]
                ], 422);
            }

            if (!Hash::check($validated['old_password'], $user->password)) {
                return response()->json([
                    'message' => 'Password lama tidak sesuai.',
                    'errors' => ['old_password' => ['Password lama tidak sesuai.']]
                ], 422);
            }

            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('foto_profil')) {
            // Delete old photo if exists
            if ($user->foto_profil && Storage::disk('public')->exists(str_replace('/storage/', '', $user->foto_profil))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->foto_profil));
            }

            $path = $request->file('foto_profil')->store('profiles', 'public');
            $updateData['foto_profil'] = '/storage/' . $path;
        }

        $user->update($updateData);

        if ($user->level === 'utd' && $user->santri_id) {
            $santri = \App\Models\Santri::find($user->santri_id);
            if ($santri) {
                if ($santri->wali_id) {
                    $wali = \App\Models\Wali::find($santri->wali_id);
                    if ($wali) {
                        $wali->update([
                            'nik' => $validated['wali_nik'] ?? $wali->nik,
                            'nama_wali' => $validated['wali_nama'] ?? $wali->nama_wali,
                            'no_hp' => $validated['wali_no_hp'] ?? $wali->no_hp,
                        ]);
                    }
                } else if (!empty($validated['wali_nama'])) {
                    $wali = \App\Models\Wali::create([
                        'nik' => $validated['wali_nik'] ?? null,
                        'nama_wali' => $validated['wali_nama'],
                        'no_hp' => $validated['wali_no_hp'] ?? null,
                    ]);
                    $santri->wali_id = $wali->id;
                }

                $santriUpdates = [
                    'nis' => $validated['santri_nis'] ?? $santri->nis,
                    'nik' => $validated['santri_nik'] ?? $santri->nik,
                    'tempat_lahir' => $validated['santri_tempat_lahir'] ?? $santri->tempat_lahir,
                    'tanggal_lahir' => $validated['santri_tanggal_lahir'] ?? $santri->tanggal_lahir,
                    'alamat' => $validated['santri_alamat'] ?? $santri->alamat,
                ];
                
                if (array_key_exists('santri_id_prov', $validated)) $santriUpdates['id_prov'] = $validated['santri_id_prov'];
                if (array_key_exists('santri_id_kab', $validated)) $santriUpdates['id_kab'] = $validated['santri_id_kab'];
                if (array_key_exists('santri_id_kec', $validated)) $santriUpdates['id_kec'] = $validated['santri_id_kec'];
                if (array_key_exists('santri_id_kel', $validated)) $santriUpdates['id_kel'] = $validated['santri_id_kel'];
                
                if (isset($validated['fullname'])) {
                    $santriUpdates['nama'] = $validated['fullname'];
                }
                
                $santri->update($santriUpdates);
            }
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user
        ]);
    }
}
