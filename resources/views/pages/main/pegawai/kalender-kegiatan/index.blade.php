@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-wide">
                Kalender Kegiatan
            </h2>
            <p class="text-sm text-gray-500" id="current-month-year">
                {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            </p>
        </div>

        {{-- Navigation --}}
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <button id="prev-month" class="p-2 rounded-lg border hover:bg-gray-50">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <button id="today-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-lg hover:bg-gray-50">
                    Hari Ini
                </button>

                <button id="next-month" class="p-2 rounded-lg border hover:bg-gray-50">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <div class="flex gap-2">
                <select id="view-select" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="month">Bulan</option>
                    <option value="week">Minggu</option>
                </select>

                <button id="add-event-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kegiatan
                </button>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mb-4 flex flex-wrap gap-4 text-sm">
        <div class="flex items-center gap-2">
            <div class="h-3 w-6 rounded bg-blue-500"></div>
            <span class="text-gray-600">Rapat</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-3 w-6 rounded bg-green-500"></div>
            <span class="text-gray-600">Dinas Luar</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-3 w-6 rounded bg-purple-500"></div>
            <span class="text-gray-600">Pelatihan</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-3 w-6 rounded bg-yellow-500"></div>
            <span class="text-gray-600">Cuti</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-3 w-6 rounded bg-red-500"></div>
            <span class="text-gray-600">Deadline</span>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        {{-- Calendar Header (Days) --}}
        <div class="grid grid-cols-7 border-b bg-gray-50">
            @php
                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            @endphp
            @foreach($days as $day)
                <div class="p-3 text-center text-sm font-semibold text-gray-600">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7" id="calendar-grid">
            {{-- Dates will be populated by JavaScript --}}
        </div>
    </div>

    {{-- Event Modal --}}
    <div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Detail Kegiatan</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div id="event-color" class="h-4 w-4 rounded"></div>
                            <span id="event-title" class="font-bold text-lg text-gray-800"></span>
                        </div>
                        <div id="event-date" class="text-sm text-gray-500 mb-1"></div>
                        <div id="event-time" class="text-sm text-gray-500"></div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Deskripsi</h4>
                        <p id="event-description" class="text-gray-600"></p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Lokasi</h4>
                        <p id="event-location" class="text-gray-600"></p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Peserta</h4>
                        <div id="event-participants" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button id="edit-event" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 font-medium">
                        Edit
                    </button>
                    <button id="delete-event" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Event Modal --}}
    <div id="add-event-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6">Tambah Kegiatan Baru</h3>

                <form id="event-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kegiatan</label>
                        <input type="text" name="title" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                            <input type="time" name="start_time" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                            <input type="time" name="end_time" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kegiatan</label>
                        <select name="category" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="meeting" data-color="bg-blue-500">Rapat</option>
                            <option value="dinas" data-color="bg-green-500">Dinas Luar</option>
                            <option value="training" data-color="bg-purple-500">Pelatihan</option>
                            <option value="leave" data-color="bg-yellow-500">Cuti</option>
                            <option value="deadline" data-color="bg-red-500">Deadline</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input type="text" name="location" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peserta</label>
                        <div class="flex flex-wrap gap-2 mb-2" id="participants-list"></div>
                        <div class="flex gap-2">
                            <input type="text" id="participant-input" placeholder="Nama peserta" class="flex-1 border rounded-lg px-4 py-2">
                            <button type="button" id="add-participant" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Tambah
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="cancel-add" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
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
            color: 'bg-blue-500',
            textColor: 'text-blue-600',
            borderColor: 'border-blue-200',
            bgColor: 'bg-blue-50'
        },
        'dinas': {
            name: 'Dinas Luar',
            color: 'bg-green-500',
            textColor: 'text-green-600',
            borderColor: 'border-green-200',
            bgColor: 'bg-green-50'
        },
        'training': {
            name: 'Pelatihan',
            color: 'bg-purple-500',
            textColor: 'text-purple-600',
            borderColor: 'border-purple-200',
            bgColor: 'bg-purple-50'
        },
        'leave': {
            name: 'Cuti',
            color: 'bg-yellow-500',
            textColor: 'text-yellow-600',
            borderColor: 'border-yellow-200',
            bgColor: 'bg-yellow-50'
        },
        'deadline': {
            name: 'Deadline',
            color: 'bg-red-500',
            textColor: 'text-red-600',
            borderColor: 'border-red-200',
            bgColor: 'bg-red-50'
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

    // Modal event elements
    const eventTitle = document.getElementById('event-title');
    const eventDate = document.getElementById('event-date');
    const eventTime = document.getElementById('event-time');
    const eventDescription = document.getElementById('event-description');
    const eventLocation = document.getElementById('event-location');
    const eventParticipants = document.getElementById('event-participants');
    const eventColor = document.getElementById('event-color');

    // Current selected event
    let selectedEvent = null;
    let selectedDate = null;
    let eventParticipantsList = [];

    // Initialize calendar
    renderCalendar(currentYear, currentMonth);

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
        eventModal.classList.add('hidden');
    });

    cancelAddBtn.addEventListener('click', () => {
        addEventModal.classList.add('hidden');
        resetEventForm();
    });

    addEventBtn.addEventListener('click', () => {
        eventParticipantsList = [];
        participantsList.innerHTML = '';
        addEventModal.classList.remove('hidden');

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
        addEventModal.classList.add('hidden');
        resetEventForm();
        renderCalendar(currentYear, currentMonth);
    });

    // Close modals when clicking outside
    [eventModal, addEventModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
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
            emptyCell.className = 'min-h-32 border p-2 bg-gray-50';
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
            emptyCell.className = 'min-h-32 border p-2 bg-gray-50';
            calendarGrid.appendChild(emptyCell);
        }

        // Render events
        renderEventsOnCalendar(year, month);
    }

    function createDateCell(date, day) {
        const cell = document.createElement('div');
        cell.className = 'min-h-32 border p-2 relative hover:bg-gray-50 transition-colors';

        const isToday = isSameDate(date, new Date());
        const dateHeader = document.createElement('div');
        dateHeader.className = `flex justify-between items-start mb-1`;

        const dayNumber = document.createElement('span');
        dayNumber.className = `inline-flex items-center justify-center h-7 w-7 rounded-full text-sm font-medium ${isToday ? 'bg-blue-600 text-white' : 'text-gray-700'}`;
        dayNumber.textContent = day;

        const addButton = document.createElement('button');
        addButton.className = 'text-gray-400 hover:text-blue-600 hover:bg-blue-50 h-6 w-6 rounded-full flex items-center justify-center';
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
        eventsContainer.className = 'space-y-1 mt-1 overflow-y-auto max-h-24';
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

        eventDiv.className = `rounded px-2 py-1 text-xs cursor-pointer truncate ${category.bgColor} ${category.borderColor} border-l-4 ${category.color} hover:opacity-90`;
        eventDiv.textContent = showTitle ? event.title : '⋯';
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
        eventTime.textContent = timeText;
        eventDescription.textContent = event.description || 'Tidak ada deskripsi';
        eventLocation.textContent = event.location || 'Tidak ditentukan';
        eventColor.className = `h-4 w-4 rounded ${category.color}`;

        // Update participants
        eventParticipants.innerHTML = '';
        if (event.participants && event.participants.length > 0) {
            event.participants.forEach(participant => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800';
                badge.textContent = participant;
                eventParticipants.appendChild(badge);
            });
        } else {
            eventParticipants.innerHTML = '<span class="text-gray-500">Tidak ada peserta</span>';
        }

        // Show modal
        eventModal.classList.remove('hidden');
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

        addEventModal.classList.remove('hidden');
    }

    function updateParticipantsList() {
        participantsList.innerHTML = '';
        eventParticipantsList.forEach(participant => {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800';
            badge.innerHTML = `
                ${participant}
                <button type="button" class="text-blue-600 hover:text-blue-800" onclick="removeParticipant('${participant}')">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    // Make removeParticipant available globally
    window.removeParticipant = function(participant) {
        eventParticipantsList = eventParticipantsList.filter(p => p !== participant);
        updateParticipantsList();
    };
});
</script>

<style>
.min-h-32 {
    min-height: 8rem;
}

@media (max-width: 768px) {
    .min-h-32 {
        min-height: 6rem;
    }

    #calendar-grid > div {
        padding: 0.25rem;
    }
}

#calendar-grid > div {
    border-right: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
}

#calendar-grid > div:nth-child(7n) {
    border-right: none;
}

#calendar-grid > div:nth-last-child(-n+7) {
    border-bottom: none;
}
</style>
@endsection
