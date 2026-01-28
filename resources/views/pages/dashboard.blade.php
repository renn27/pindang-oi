@extends('layouts.dashboard')

@section('content')
    <div class="mb-5">
        @auth
            <p>SELAMAT DATANG <b>{{ Auth::user()->nama_pegawai }}</b></p>
            <p>
                Role :
                {{ Auth::user()->active_role ?? 'Anggota Tim' }}
            </p>

        @else
            <p>Belum login</p>
            <p>Gunakan <code>/login-as/{username}</code> untuk simulasi login</p>
        @endauth
    </div>

    <div class="grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12 xl:col-span-6">
            <x-profile.employe-work-target />
        </div>

        <div class="col-span-12 xl:col-span-6">
            <div class="mb-5 rounded-2xl space-6 border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-gray-900">
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
