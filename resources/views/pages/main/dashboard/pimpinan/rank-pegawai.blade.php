@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard Visualisasi Data" />
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
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">
            Top 5 Pegawai Terbaik
        </h2>

        @foreach($rankPegawai as $pegawai)

            <div class="flex items-center justify-between py-3 border-b">

                <div class="flex items-center gap-4">

                    <div class="
                        w-10 h-10 flex items-center justify-center
                        rounded-full bg-gray-100 font-bold
                    ">
                        {{ $loop->iteration }}
                    </div>

                    <div>
                        <p class="font-semibold">
                            {{ $pegawai->nama_pegawai }}
                        </p>

                        <p class="text-sm text-gray-400">
                            RR: {{ $pegawai->total_rr }}
                            ⭐ {{ number_format($pegawai->avg_rating,2) }}
                        </p>
                    </div>
                </div>

                <div class="font-bold text-lg">
                    {{ number_format($pegawai->score,2) }}
                </div>

            </div>

        @endforeach
    </div>

@endsection
