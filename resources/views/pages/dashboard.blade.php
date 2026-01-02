@extends('layouts.app')

@section('content')
    @php
        $user = $user ?? Auth::user(); // fallback jika $user belum dikirim dari controller
    @endphp

    @if($user)
        <p>Login sebagai: <b>{{ $user->nama_pegawai }}</b></p>
        <p>Role: {{ $user->roles->pluck('nama_role')->implode(', ') ?: 'pegawai biasa' }}</p>

        @if($user->hasRole('admin'))
            <p>🛠 Menu Admin MUNCUL</p>
        @endif

        @if($user->hasRole('pimpinan'))
            <p>📊 Menu Pimpinan MUNCUL</p>
        @endif
    @endif


    <div class="grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12 xl:col-span-6">
            <x-profile.employe-work-target />
        </div>

        <div class="col-span-12 xl:col-span-6">

            <div class="mb-5 rounded-2xl space-6 border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <div class="mb-4">
                <x-profile.employe-rank-card />
            </div>
            <x-profile.employe-rank-card />
            </div>
            <x-profile.resume-kegiatan />
        </div>

        <div class="col-span-12 space-y-6 xl:col-span-6"></div>
        <div class="col-span-12">
            <x-calender-area />
        </div>
    </div>
@endsection
