<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = User::with(['badkom', 'pjutd', 'santri'])->orderBy('id', 'desc');

        if ($user->level === 'badkom_wilayah') {
            $query->where(function($q) use ($user) {
                $q->where('level', 'pjutd')
                  ->whereHas('pjutd', function($p) use ($user) {
                      $p->where('badkom_id', $user->badkom_id);
                  });
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'level' => 'required|in:admin,badkom_pusat,badkom_wilayah,pjutd,utd',
            'badkom_id' => 'nullable|exists:badkoms,id',
            'pjutd_id' => 'nullable|exists:pjutds,id',
            'santri_id' => 'nullable|exists:santris,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->load(['badkom', 'pjutd', 'santri']);

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        $user = User::with(['badkom', 'pjutd', 'santri'])->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => ['required', Rule::unique('users')->ignore($user->id)],
            'fullname' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'level' => 'required|in:admin,badkom_pusat,badkom_wilayah,pjutd,utd',
            'badkom_id' => 'nullable|exists:badkoms,id',
            'pjutd_id' => 'nullable|exists:pjutds,id',
            'santri_id' => 'nullable|exists:santris,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load(['badkom', 'pjutd', 'santri']);

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function resetPassword(Request $request, string $id)
    {
        $admin = $request->user();
        if (!in_array($admin->level, ['admin', 'badkom_pusat', 'badkom_wilayah'])) {
            return response()->json(['message' => 'Anda tidak diizinkan mereset password.'], 403);
        }

        $user = User::findOrFail($id);

        if ($admin->level === 'badkom_wilayah') {
            if ($user->level === 'admin' || $user->level === 'badkom_pusat' || $user->level === 'badkom_wilayah') {
                return response()->json(['message' => 'Badkom Wilayah hanya dapat mereset password PJ-UTD atau UT-D.'], 403);
            }
        }

        $user->update([
            'password' => Hash::make('Panyepen123')
        ]);

        return response()->json(['message' => 'Password berhasil direset ke: Panyepen123']);
    }
}
