<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'fullname' => 'required|string',
            'old_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $updateData = [
            'username' => $validated['username'],
            'fullname' => $validated['fullname'],
        ];

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

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user
        ]);
    }
}
