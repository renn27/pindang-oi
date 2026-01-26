@extends('layouts.dashboard')

@section('content')
<div class="p-4 md:p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div class="space-y-1">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
                Kalender Kegiatan
            </h2>
            <p class="text-sm text-gray-500" id="current-month-year">
                {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            </p>
        </div>

        {{-- Navigation --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex items-center gap-2 bg-white rounded-xl border p-1 shadow-sm">
                <button id="prev-month" class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <button id="today-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                    Hari Ini
                </button>

                <button id="next-month" class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <div class="flex gap-2">
                <div class="relative">
                    <select id="view-select" class="appearance-none border rounded-xl px-4 py-2.5 text-sm bg-white shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
                        <option value="month">Bulan</option>
                        <option value="week">Minggu</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <button id="add-event-btn" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kegiatan
                </button>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mb-6 p-4 bg-gradient-to-r from-gray-50 to-white rounded-2xl border shadow-sm">
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-blue-500 to-blue-400"></div>
                <span class="text-gray-700 font-medium">Rapat</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-green-500 to-green-400"></div>
                <span class="text-gray-700 font-medium">Dinas Luar</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-purple-500 to-purple-400"></div>
                <span class="text-gray-700 font-medium">Pelatihan</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-400"></div>
                <span class="text-gray-700 font-medium">Cuti</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-red-500 to-red-400"></div>
                <span class="text-gray-700 font-medium">Deadline</span>
            </div>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div class="bg-white border rounded-2xl shadow-lg overflow-hidden">
        {{-- Calendar Header (Days) --}}
        <div class="grid grid-cols-7 border-b">
            @php
                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            @endphp
            @foreach($days as $day)
                <div class="p-4 text-center text-sm font-semibold text-gray-600 bg-gray-50/50">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7 bg-gradient-to-b from-white to-gray-50/30" id="calendar-grid">
            {{-- Dates will be populated by JavaScript --}}
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-white p-4 rounded-2xl border border-blue-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-xl">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Kegiatan</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-events">5</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-white p-4 rounded-2xl border border-green-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-xl">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Peserta Terlibat</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-participants">15</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-white p-4 rounded-2xl border border-purple-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-xl">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Rata-rata Durasi</p>
                    <p class="text-2xl font-bold text-gray-900">2.4 jam</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-50 to-white p-4 rounded-2xl border border-yellow-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-yellow-100 rounded-xl">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900" id="month-events">5</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Event Modal --}}
    <div id="event-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="modal-content">
            <div class="relative">
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-2xl" id="modal-header-bar"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Detail Kegiatan</h3>
                        <button id="close-modal" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <div class="flex items-start gap-3 mb-4">
                                <div id="event-color" class="h-12 w-2 rounded-full mt-1"></div>
                                <div class="flex-1">
                                    <h4 id="event-title" class="font-bold text-xl text-gray-900 mb-2"></h4>
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="flex items-center gap-1.5 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span id="event-date"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span id="event-time"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h5 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Deskripsi
                                </h5>
                                <p id="event-description" class="text-gray-600 bg-gray-50 p-3 rounded-lg"></p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lokasi
                                </h5>
                                <p id="event-location" class="text-gray-600 bg-gray-50 p-3 rounded-lg"></p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 4.75v-4.5"/>
                                    </svg>
                                    Peserta
                                </h5>
                                <div id="event-participants" class="flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button id="edit-event" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <button id="delete-event" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-500 text-white rounded-xl hover:from-red-700 hover:to-red-600 font-medium transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Event Modal --}}
    <div id="add-event-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0" id="add-modal-content">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Tambah Kegiatan Baru</h3>
                </div>

                <form id="event-form" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Kegiatan *</label>
                            <input type="text" name="title" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan judul kegiatan" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kegiatan</label>
                            <select name="category" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none">
                                <option value="meeting" data-color="bg-gradient-to-r from-blue-500 to-blue-400">Rapat</option>
                                <option value="dinas" data-color="bg-gradient-to-r from-green-500 to-green-400">Dinas Luar</option>
                                <option value="training" data-color="bg-gradient-to-r from-purple-500 to-purple-400">Pelatihan</option>
                                <option value="leave" data-color="bg-gradient-to-r from-yellow-500 to-yellow-400">Cuti</option>
                                <option value="deadline" data-color="bg-gradient-to-r from-red-500 to-red-400">Deadline</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai *</label>
                            <input type="date" name="start_date" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai *</label>
                            <input type="date" name="end_date" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Mulai</label>
                            <input type="time" name="start_time" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Selesai</label>
                            <input type="time" name="end_time" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                        <input type="text" name="location" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan lokasi kegiatan">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Tambahkan deskripsi kegiatan"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Peserta</label>
                        <div class="flex flex-wrap gap-2 mb-3 min-h-12 p-2 border-2 border-gray-200 rounded-xl" id="participants-list"></div>
                        <div class="flex gap-2">
                            <input type="text" id="participant-input" placeholder="Nama peserta" class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <button type="button" id="add-participant" class="px-5 py-3 bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 rounded-xl hover:from-gray-200 hover:to-gray-100 font-medium transition-all duration-200 border border-gray-300">
                                Tambah
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" id="cancel-add" class="px-5 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-all duration-200">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 font-medium transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                            Simpan Kegiatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Current date
    let currentDate = new Date();
    let currentYear = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();

    // Event categories with colors
    const eventCategories = {
        'meeting': {
            name: 'Rapat',
            color: 'from-blue-500 to-blue-400',
            bgColor: 'bg-gradient-to-r from-blue-100 to-blue-50',
            textColor: 'text-blue-700',
            borderColor: 'border-blue-200'
        },
        'dinas': {
            name: 'Dinas Luar',
            color: 'from-green-500 to-green-400',
            bgColor: 'bg-gradient-to-r from-green-100 to-green-50',
            textColor: 'text-green-700',
            borderColor: 'border-green-200'
        },
        'training': {
            name: 'Pelatihan',
            color: 'from-purple-500 to-purple-400',
            bgColor: 'bg-gradient-to-r from-purple-100 to-purple-50',
            textColor: 'text-purple-700',
            borderColor: 'border-purple-200'
        },
        'leave': {
            name: 'Cuti',
            color: 'from-yellow-500 to-yellow-400',
            bgColor: 'bg-gradient-to-r from-yellow-100 to-yellow-50',
            textColor: 'text-yellow-700',
            borderColor: 'border-yellow-200'
        },
        'deadline': {
            name: 'Deadline',
            color: 'from-red-500 to-red-400',
            bgColor: 'bg-gradient-to-r from-red-100 to-red-50',
            textColor: 'text-red-700',
            borderColor: 'border-red-200'
        }
    };

    // Sample events data (from database in real app)
    let events = [
        {
            id: 1,
            title: "Rapat Tim Teknis",
            start_date: new Date(currentYear, currentMonth, 15),
            end_date: new Date(currentYear, currentMonth, 15),
            start_time: "09:00",
            end_time: "11:00",
            category: "meeting",
            description: "Rapat pembahasan proyek Q3 dengan tim teknis",
            location: "Ruang Rapat Lantai 3",
            participants: ["Ahmad", "Budi", "Citra", "Dian"]
        },
        {
            id: 2,
            title: "Dinas Luar ke Klien",
            start_date: new Date(currentYear, currentMonth, 18),
            end_date: new Date(currentYear, currentMonth, 19),
            start_time: "08:00",
            end_time: "17:00",
            category: "dinas",
            description: "Kunjungan ke PT. Maju Bersama untuk presentasi proposal",
            location: "Kantor Klien - Jl. Sudirman No. 123",
            participants: ["Eka", "Fajar"]
        },
        {
            id: 3,
            title: "Pelatihan Leadership",
            start_date: new Date(currentYear, currentMonth, 22),
            end_date: new Date(currentYear, currentMonth, 24),
            start_time: "09:00",
            end_time: "16:00",
            category: "training",
            description: "Pelatihan leadership untuk manajer level menengah",
            location: "Hotel Grand Indonesia",
            participants: ["Gita", "Hadi", "Indra", "Joko", "Kartika"]
        },
        {
            id: 4,
            title: "Deadline Laporan Bulanan",
            start_date: new Date(currentYear, currentMonth, 28),
            end_date: new Date(currentYear, currentMonth, 28),
            start_time: "23:59",
            end_time: "23:59",
            category: "deadline",
            description: "Batas akhir pengumpulan laporan kinerja bulanan",
            location: "Online",
            participants: ["Semua Divisi"]
        },
        {
            id: 5,
            title: "Cuti Tahunan",
            start_date: new Date(currentYear, currentMonth, 5),
            end_date: new Date(currentYear, currentMonth, 7),
            category: "leave",
            description: "Cuti tahunan pegawai",
            location: "-",
            participants: ["Lina"]
        }
    ];

    // DOM Elements
    const calendarGrid = document.getElementById('calendar-grid');
    const currentMonthYear = document.getElementById('current-month-year');
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today-btn');
    const eventModal = document.getElementById('event-modal');
    const addEventModal = document.getElementById('add-event-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelAddBtn = document.getElementById('cancel-add');
    const addEventBtn = document.getElementById('add-event-btn');
    const eventForm = document.getElementById('event-form');
    const participantInput = document.getElementById('participant-input');
    const addParticipantBtn = document.getElementById('add-participant');
    const participantsList = document.getElementById('participants-list');
    const modalContent = document.getElementById('modal-content');
    const addModalContent = document.getElementById('add-modal-content');

    // Modal event elements
    const eventTitle = document.getElementById('event-title');
    const eventDate = document.getElementById('event-date');
    const eventTime = document.getElementById('event-time');
    const eventDescription = document.getElementById('event-description');
    const eventLocation = document.getElementById('event-location');
    const eventParticipants = document.getElementById('event-participants');
    const eventColor = document.getElementById('event-color');
    const modalHeaderBar = document.getElementById('modal-header-bar');

    // Stats elements
    const totalEvents = document.getElementById('total-events');
    const monthEvents = document.getElementById('month-events');
    const totalParticipants = document.getElementById('total-participants');

    // Current selected event
    let selectedEvent = null;
    let selectedDate = null;
    let eventParticipantsList = [];

    // Initialize calendar
    renderCalendar(currentYear, currentMonth);
    updateStats();

    // Event Listeners
    prevMonthBtn.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentYear, currentMonth);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentYear, currentMonth);
    });

    todayBtn.addEventListener('click', () => {
        currentDate = new Date();
        currentYear = currentDate.getFullYear();
        currentMonth = currentDate.getMonth();
        renderCalendar(currentYear, currentMonth);
    });

    closeModalBtn.addEventListener('click', () => {
        closeModal(eventModal, modalContent);
    });

    cancelAddBtn.addEventListener('click', () => {
        closeModal(addEventModal, addModalContent);
        resetEventForm();
    });

    addEventBtn.addEventListener('click', () => {
        eventParticipantsList = [];
        participantsList.innerHTML = '';
        openModal(addEventModal, addModalContent);

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="start_date"]').value = today;
        document.querySelector('input[name="end_date"]').value = today;
    });

    addParticipantBtn.addEventListener('click', () => {
        const participant = participantInput.value.trim();
        if (participant && !eventParticipantsList.includes(participant)) {
            eventParticipantsList.push(participant);
            updateParticipantsList();
            participantInput.value = '';
            participantInput.focus();
        }
    });

    participantInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addParticipantBtn.click();
        }
    });

    eventForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(eventForm);
        const newEvent = {
            id: events.length + 1,
            title: formData.get('title'),
            start_date: new Date(formData.get('start_date')),
            end_date: new Date(formData.get('end_date')),
            start_time: formData.get('start_time'),
            end_time: formData.get('end_time'),
            category: formData.get('category'),
            description: formData.get('description'),
            location: formData.get('location'),
            participants: [...eventParticipantsList]
        };

        events.push(newEvent);
        closeModal(addEventModal, addModalContent);
        resetEventForm();
        renderCalendar(currentYear, currentMonth);
        updateStats();
    });

    // Close modals when clicking outside
    [eventModal, addEventModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                const modalContent = modal.querySelector('.bg-white');
                closeModal(modal, modalContent);
                if (modal === addEventModal) {
                    resetEventForm();
                }
            }
        });
    });

    // Functions
    function renderCalendar(year, month) {
        calendarGrid.innerHTML = '';

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay();

        // Update month-year display
        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        currentMonthYear.textContent = `${monthNames[month]} ${year}`;

        // Add empty cells for days before the first day of month
        for (let i = 0; i < startingDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'min-h-32 p-3 bg-gradient-to-b from-gray-50/50 to-white';
            calendarGrid.appendChild(emptyCell);
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const cell = createDateCell(date, day);
            calendarGrid.appendChild(cell);
        }

        // Calculate and add empty cells after the last day
        const totalCells = Math.ceil((startingDay + daysInMonth) / 7) * 7;
        const emptyCellsAfter = totalCells - (startingDay + daysInMonth);

        for (let i = 0; i < emptyCellsAfter; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'min-h-32 p-3 bg-gradient-to-b from-gray-50/50 to-white';
            calendarGrid.appendChild(emptyCell);
        }

        // Render events
        renderEventsOnCalendar(year, month);
    }

    function createDateCell(date, day) {
        const cell = document.createElement('div');
        const isToday = isSameDate(date, new Date());
        const isWeekend = date.getDay() === 0 || date.getDay() === 6;
        
        cell.className = `min-h-36 p-3 relative group transition-all duration-200 hover:bg-gradient-to-b hover:from-blue-50/30 hover:to-white ${isWeekend ? 'bg-gradient-to-b from-gray-50/30 to-white' : 'bg-white'} border-r border-b border-gray-100`;

        // Date header
        const dateHeader = document.createElement('div');
        dateHeader.className = `flex justify-between items-start mb-2`;

        const dayNumber = document.createElement('span');
        dayNumber.className = `inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-bold transition-all duration-200 ${isToday ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-700 group-hover:text-blue-600'}`;
        dayNumber.textContent = day;

        const addButton = document.createElement('button');
        addButton.className = 'opacity-0 group-hover:opacity-100 text-gray-400 hover:text-blue-600 hover:bg-blue-50 h-7 w-7 rounded-full flex items-center justify-center transition-all duration-200';
        addButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>';
        addButton.onclick = (e) => {
            e.stopPropagation();
            selectedDate = date;
            openAddEventModalForDate(date);
        };

        dateHeader.appendChild(dayNumber);
        dateHeader.appendChild(addButton);
        cell.appendChild(dateHeader);

        // Events container for this date
        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'space-y-1.5 mt-2 overflow-y-auto max-h-28 scrollbar-thin';
        cell.appendChild(eventsContainer);

        // Add click event to show events for this date
        cell.addEventListener('click', (e) => {
            if (!e.target.closest('button')) {
                showEventsForDate(date);
            }
        });

        return cell;
    }

    function renderEventsOnCalendar(year, month) {
        // Get all date cells
        const dateCells = document.querySelectorAll('#calendar-grid > div');

        events.forEach(event => {
            const eventStart = new Date(event.start_date);
            const eventEnd = new Date(event.end_date);

            // Check if event is in current month
            if (eventStart.getFullYear() === year && eventStart.getMonth() === month) {
                const startDay = eventStart.getDate();
                const endDay = eventEnd.getDate();

                // For multi-day events
                for (let day = startDay; day <= endDay && day <= new Date(year, month + 1, 0).getDate(); day++) {
                    const cellIndex = getDayCellIndex(year, month, day);
                    if (cellIndex < dateCells.length) {
                        const cell = dateCells[cellIndex];
                        const eventsContainer = cell.querySelector('div:last-child');

                        if (eventsContainer) {
                            const eventElement = createEventElement(event, day === startDay);
                            eventsContainer.appendChild(eventElement);
                        }
                    }
                }
            }
        });
    }

    function createEventElement(event, showTitle = true) {
        const eventDiv = document.createElement('div');
        const category = eventCategories[event.category];

        eventDiv.className = `rounded-lg px-2.5 py-1.5 text-xs cursor-pointer truncate ${category.bgColor} border-l-4 border-l-transparent hover:shadow-sm transition-all duration-200 group`;
        eventDiv.style.borderLeftColor = 'var(--event-color)';
        eventDiv.setAttribute('data-color', category.color);
        
        if (showTitle) {
            eventDiv.innerHTML = `
                <div class="flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full" style="background: linear-gradient(to right, ${category.color.split('from-')[1].split(' ')[0]}, ${category.color.split('to-')[1]})"></div>
                    <span class="font-medium ${category.textColor}">${event.title}</span>
                </div>
            `;
        } else {
            eventDiv.innerHTML = `
                <div class="flex items-center gap-1">
                    <div class="h-1.5 w-1.5 rounded-full" style="background: linear-gradient(to right, ${category.color.split('from-')[1].split(' ')[0]}, ${category.color.split('to-')[1]})"></div>
                    <span class="${category.textColor}">⋯</span>
                </div>
            `;
        }

        eventDiv.title = event.title;

        eventDiv.addEventListener('click', (e) => {
            e.stopPropagation();
            showEventDetails(event);
        });

        return eventDiv;
    }

    function showEventDetails(event) {
        selectedEvent = event;
        const category = eventCategories[event.category];

        // Format date
        const startDate = new Date(event.start_date);
        const endDate = new Date(event.end_date);
        const dateFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

        let dateText = startDate.toLocaleDateString('id-ID', dateFormatOptions);
        if (!isSameDate(startDate, endDate)) {
            dateText += ' s/d ' + endDate.toLocaleDateString('id-ID', dateFormatOptions);
        }

        // Format time
        let timeText = '';
        if (event.start_time && event.end_time) {
            timeText = `${event.start_time} - ${event.end_time}`;
        }

        // Update modal content
        eventTitle.textContent = event.title;
        eventDate.textContent = dateText;
        eventTime.textContent = timeText || 'Sepanjang hari';
        eventDescription.textContent = event.description || 'Tidak ada deskripsi';
        eventLocation.textContent = event.location || 'Tidak ditentukan';
        eventColor.className = `h-12 w-2 rounded-full bg-gradient-to-b ${category.color}`;
        modalHeaderBar.className = `absolute top-0 left-0 right-0 h-2 rounded-t-2xl bg-gradient-to-r ${category.color}`;

        // Update participants
        eventParticipants.innerHTML = '';
        if (event.participants && event.participants.length > 0) {
            event.participants.forEach(participant => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs bg-gradient-to-r from-gray-100 to-gray-50 text-gray-800 border border-gray-200';
                badge.textContent = participant;
                eventParticipants.appendChild(badge);
            });
        } else {
            eventParticipants.innerHTML = '<span class="text-gray-500 italic">Tidak ada peserta</span>';
        }

        // Show modal with animation
        openModal(eventModal, modalContent);
    }

    function showEventsForDate(date) {
        const dayEvents = events.filter(event => {
            const eventStart = new Date(event.start_date);
            const eventEnd = new Date(event.end_date);
            return date >= eventStart && date <= eventEnd;
        });

        if (dayEvents.length > 0) {
            // Show first event details
            showEventDetails(dayEvents[0]);
        } else {
            // If no events, show add event modal
            openAddEventModalForDate(date);
        }
    }

    function openAddEventModalForDate(date) {
        eventParticipantsList = [];
        participantsList.innerHTML = '';

        const dateStr = date.toISOString().split('T')[0];
        document.querySelector('input[name="start_date"]').value = dateStr;
        document.querySelector('input[name="end_date"]').value = dateStr;

        openModal(addEventModal, addModalContent);
    }

    function updateParticipantsList() {
        participantsList.innerHTML = '';
        eventParticipantsList.forEach(participant => {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm bg-gradient-to-r from-blue-50 to-blue-100 text-blue-800 border border-blue-200';
            badge.innerHTML = `
                ${participant}
                <button type="button" class="text-blue-600 hover:text-blue-800 transition-colors" onclick="removeParticipant('${participant}')">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            participantsList.appendChild(badge);
        });
    }

    function resetEventForm() {
        eventForm.reset();
        eventParticipantsList = [];
        participantsList.innerHTML = '';
    }

    function getDayCellIndex(year, month, day) {
        const firstDay = new Date(year, month, 1).getDay();
        return firstDay + day - 1;
    }

    function isSameDate(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    function openModal(modal, content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.style.transform = 'scale(1)';
            content.style.opacity = '1';
        }, 10);
    }

    function closeModal(modal, content) {
        content.style.transform = 'scale(0.95)';
        content.style.opacity = '0';
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function updateStats() {
        totalEvents.textContent = events.length;
        
        // Count events in current month
        const currentMonthEvents = events.filter(event => {
            const eventDate = new Date(event.start_date);
            return eventDate.getFullYear() === currentYear && eventDate.getMonth() === currentMonth;
        }).length;
        monthEvents.textContent = currentMonthEvents;
        
        // Count total participants
        const total = events.reduce((sum, event) => sum + (event.participants ? event.participants.length : 0), 0);
        totalParticipants.textContent = total;
    }

    // Make removeParticipant available globally
    window.removeParticipant = function(participant) {
        eventParticipantsList = eventParticipantsList.filter(p => p !== participant);
        updateParticipantsList();
    };
});
</script>

<style>
:root {
    --blue-500: #3b82f6;
    --blue-400: #60a5fa;
    --green-500: #10b981;
    --green-400: #34d399;
    --purple-500: #8b5cf6;
    --purple-400: #a78bfa;
    --yellow-500: #f59e0b;
    --yellow-400: #fbbf24;
    --red-500: #ef4444;
    --red-400: #f87171;
}

.min-h-36 {
    min-height: 9rem;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

#calendar-grid > div {
    border-right: 1px solid #f3f4f6;
    border-bottom: 1px solid #f3f4f6;
}

#calendar-grid > div:nth-child(7n) {
    border-right: none;
}

#calendar-grid > div:nth-last-child(-n+7) {
    border-bottom: none;
}

/* Smooth transitions */
* {
    transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
}

/* Gradient text for future enhancements */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

@media (max-width: 768px) {
    .min-h-36 {
        min-height: 7rem;
    }
    
    #calendar-grid > div {
        padding: 0.5rem;
    }
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
}

/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endsection