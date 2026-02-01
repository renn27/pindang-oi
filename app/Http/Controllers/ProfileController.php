<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // dd($request->all());
        $user = $request->user();
        $data = $request->validated();

        // mapping name → nama_pegawai
        if (isset($data['name'])) {
            $data['nama_pegawai'] = $data['name'];
            unset($data['name']);
        }

        /**
            * Handle password dulu (jangan ikut fill)
        */
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        /**
         * Reset verifikasi email jika berubah
         */
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        /**
         * FOTO PROFIL
         */
        if ($request->hasFile('photo')) {

            // 🔥 Hapus foto lama dulu
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')
                ->store('foto-profil', 'public');

            $data['photo'] = $path;
        }

        /**
         * Update user
         */
        $user->fill($data);

        $user->save();

        return Redirect::route('profile')
            ->with('success', 'Profil berhasil diubah');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
