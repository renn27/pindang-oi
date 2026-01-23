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

        // Update field selain password
        $user->fill(
            collect($data)
                ->except('password')
                ->toArray()
        );

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Jika email berubah, reset verifikasi
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        //foto
        if ($request->hasFile('photo')) {

            // hapus foto lama
            if ($user->photo && Storage::disk('public')->exists('profile/' . $user->photo)) {
                Storage::disk('public')->delete('profile/' . $user->photo);
            }

            // buat nama file unik
            $filename = uniqid() . '.' . $request->photo->extension();

            // simpan foto
            $request->photo->storeAs('profile', $filename, 'public');

            // simpan ke DB
            $user->photo = $filename;
        }

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
