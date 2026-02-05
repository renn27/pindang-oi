@extends('layouts.dashboard')

@section('content')
    <div class="p-4 md:p-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="space-y-1">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Kalender Kegiatan
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400" id="current-month-year">
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </p>
            </div>

            {{-- Navigation --}}
            <div class="flex items-center gap-2 bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 p-1 shadow-sm">
                <button id="prev-month"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 group">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <p class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300" id="current-month-nav">
                    {{ \Carbon\Carbon::now()->translatedFormat('F') }}
                </p>

                <button id="next-month"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 group">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
                class="bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 p-4 rounded-2xl border border-blue-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Kegiatan</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="total-events">0</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-yellow-50 to-white dark:from-gray-800 dark:to-gray-900 p-4 rounded-2xl border border-yellow-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Sedang Berjalan</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="ongoing-events">0</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-900 p-4 rounded-2xl border border-green-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Selesai</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="completed-events">0</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div
            class="my-6 p-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl border dark:border-gray-700 shadow-sm">
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-400"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Belum Mulai</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-blue-500 to-blue-400"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Sedang Berjalan</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="h-3 w-6 rounded-lg bg-gradient-to-r from-green-500 to-green-400"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Selesai</span>
                </div>
            </div>
        </div>

        {{-- Calendar Container --}}
        <div class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-lg overflow-hidden">
            {{-- Calendar Header (Days) --}}
            <div class="grid grid-cols-7 border-b dark:border-gray-700">
                @php
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp
                @foreach ($days as $day)
                    <div
                        class="p-4 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-900/50">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div class="grid grid-cols-7 bg-gradient-to-b from-white to-gray-50/30 dark:from-gray-800 dark:to-gray-900/30"
                id="calendar-grid">
                {{-- Dates will be populated by JavaScript --}}
            </div>
        </div>

        {{-- Event Modal --}}
        <div id="event-modal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
                id="modal-content">
                <div class="relative">
                    <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-2xl"
                        id="modal-header-bar"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Kegiatan</h3>
                            <button id="close-modal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <div class="flex items-start gap-3 mb-4">
                                    <div id="event-color" class="h-12 w-2 rounded-full mt-1"></div>
                                    <div class="flex-1">
                                        <h4 id="event-title" class="font-bold text-xl text-gray-900 dark:text-white mb-2">
                                        </h4>
                                        <div class="flex items-center gap-4 text-sm">
                                            <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span id="event-date"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span id="event-duration"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h5
                                        class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Status
                                    </h5>
                                    <p id="event-status"
                                        class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    </p>
                                </div>

                                {{-- <div>
                                    <h5
                                        class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Deskripsi
                                    </h5>
                                    <p id="event-description"
                                        class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    </p>
                                </div> --}}

                                {{-- <div>
                                    <h5
                                        class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Lokasi
                                    </h5>
                                    <p id="event-location"
                                        class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    </p>
                                </div> --}}

                                <div>
                                    <h5
                                        class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 4.75v-4.5" />
                                        </svg>
                                        Peserta
                                    </h5>
                                    <div id="event-participants" class="flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
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

            // Event status categories with colors
            const eventStatuses = {
                'Belum Mulai': {
                    name: 'Belum Mulai',
                    color: 'from-yellow-500 to-yellow-400',
                    bgColor: 'bg-gradient-to-r from-yellow-100 to-yellow-50 dark:from-yellow-900/20 dark:to-yellow-800/10',
                    textColor: 'text-yellow-700 dark:text-yellow-400',
                    borderColor: 'border-yellow-200 dark:border-yellow-800/30'
                },
                'Berjalan': {
                    name: 'Sedang Berjalan',
                    color: 'from-blue-500 to-blue-400',
                    bgColor: 'bg-gradient-to-r from-blue-100 to-blue-50 dark:from-blue-900/20 dark:to-blue-800/10',
                    textColor: 'text-blue-700 dark:text-blue-400',
                    borderColor: 'border-blue-200 dark:border-blue-800/30'
                },
                'Selesai': {
                    name: 'Selesai',
                    color: 'from-green-500 to-green-400',
                    bgColor: 'bg-gradient-to-r from-green-100 to-green-50 dark:from-green-900/20 dark:to-green-800/10',
                    textColor: 'text-green-700 dark:text-green-400',
                    borderColor: 'border-green-200 dark:border-green-800/30'
                }
            };

            let events = @js($events);
            // console.log(events);

            // DOM Elements
            const calendarGrid = document.getElementById('calendar-grid');
            const currentMonthYear = document.getElementById('current-month-year');
            const currentMonthNav = document.getElementById('current-month-nav');
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            const eventModal = document.getElementById('event-modal');
            const closeModalBtn = document.getElementById('close-modal');
            const modalContent = document.getElementById('modal-content');

            // Modal event elements
            const eventTitle = document.getElementById('event-title');
            const eventDate = document.getElementById('event-date');
            const eventDuration = document.getElementById('event-duration');
            const eventStatus = document.getElementById('event-status');
            // const eventDescription = document.getElementById('event-description');
            // const eventLocation = document.getElementById('event-location');
            const eventParticipants = document.getElementById('event-participants');
            const eventColor = document.getElementById('event-color');
            const modalHeaderBar = document.getElementById('modal-header-bar');

            // Stats elements
            const totalEvents = document.getElementById('total-events');
            const ongoingEvents = document.getElementById('ongoing-events');
            const completedEvents = document.getElementById('completed-events');

             // Current selected event
            let selectedEvent = null;

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

            closeModalBtn.addEventListener('click', () => {
                closeModal(eventModal, modalContent);
            });

            eventModal.addEventListener('click', (e) => {
                if (e.target === eventModal) {
                    closeModal(eventModal, modalContent);
                }
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
                currentMonthNav.textContent = `${monthNames[month]}`;

                // Add empty cells for days before the first day of month
                for (let i = 0; i < startingDay; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className =
                        'min-h-32 p-3 bg-gradient-to-b from-gray-50/50 to-white dark:from-gray-800/50 dark:to-gray-900';
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
                    emptyCell.className =
                        'min-h-32 p-3 bg-gradient-to-b from-gray-50/50 to-white dark:from-gray-800/50 dark:to-gray-900';
                    calendarGrid.appendChild(emptyCell);
                }

                // Render events
                renderEventsOnCalendar(year, month);
            }

            function createDateCell(date, day) {
                const cell = document.createElement('div');
                const isToday = isSameDate(date, new Date());
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;

                cell.className =
                    `min-h-36 p-3 relative group transition-all duration-200 hover:bg-gradient-to-b hover:from-blue-50/30 hover:to-white dark:hover:from-gray-700/30 dark:hover:to-gray-800 ${isWeekend ? 'bg-gradient-to-b from-gray-50/30 to-white dark:from-gray-800/30 dark:to-gray-900' : 'bg-white dark:bg-gray-800'} border-r border-b border-gray-100 dark:border-gray-700`;

                // Date header
                const dateHeader = document.createElement('div');
                dateHeader.className = `flex justify-between items-start mb-2`;

                const dayNumber = document.createElement('span');
                dayNumber.className =
                    `inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-bold transition-all duration-200 ${isToday ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400'}`;
                dayNumber.textContent = day;

                dateHeader.appendChild(dayNumber);
                cell.appendChild(dateHeader);

                // Events container for this date
                const eventsContainer = document.createElement('div');
                eventsContainer.className = 'space-y-1.5 mt-2 overflow-y-auto max-h-28 scrollbar-thin';
                cell.appendChild(eventsContainer);

                // Add click event to show events for this date
                cell.addEventListener('click', (e) => {
                    showEventsForDate(date);
                });

                return cell;
            }

            // function renderEventsOnCalendar(year, month) {
            //     // Get all date cells
            //     const dateCells = document.querySelectorAll('#calendar-grid > div');

            //     events.forEach(event => {
            //         const eventStart = new Date(event.start_date);
            //         const eventEnd = new Date(event.end_date);

            //         // Check if event is in current month
            //         if (eventStart.getFullYear() === year && eventStart.getMonth() === month) {
            //             const startDay = eventStart.getDate();
            //             const endDay = eventEnd.getDate();

            //             // For multi-day events
            //             for (let day = startDay; day <= endDay && day <= new Date(year, month + 1, 0)
            //                 .getDate(); day++) {
            //                 const cellIndex = getDayCellIndex(year, month, day);
            //                 if (cellIndex < dateCells.length) {
            //                     const cell = dateCells[cellIndex];
            //                     const eventsContainer = cell.querySelector('div:last-child');

            //                     if (eventsContainer) {
            //                         const eventElement = createEventElement(event, day === startDay);
            //                         eventsContainer.appendChild(eventElement);
            //                     }
            //                 }
            //             }
            //         }
            //     });
            // }
            function renderEventsOnCalendar(year, month) {
                const dateCells = document.querySelectorAll('#calendar-grid > div');

                const monthStart = new Date(year, month, 1);
                const monthEnd   = new Date(year, month + 1, 0);

                events.forEach(event => {
                    const eventStart = new Date(event.start_date);
                    const eventEnd   = new Date(event.end_date);

                    // ✅ CEK OVERLAP RANGE
                    if (eventEnd < monthStart || eventStart > monthEnd) {
                        return; // tidak overlap bulan ini
                    }

                    // Tentukan start & end yang jatuh di bulan ini
                    const startDay = eventStart < monthStart ? 1 : eventStart.getDate();
                    const endDay   = eventEnd > monthEnd ? monthEnd.getDate() : eventEnd.getDate();

                    for (let day = startDay; day <= endDay; day++) {
                        const cellIndex = getDayCellIndex(year, month, day);

                        if (cellIndex < dateCells.length) {
                            const cell = dateCells[cellIndex];
                            const eventsContainer = cell.querySelector('div:last-child');

                            if (eventsContainer) {
                                const eventElement = createEventElement(
                                    event,
                                    day === startDay // hanya tampilkan title di hari pertama
                                );
                                eventsContainer.appendChild(eventElement);
                            }
                        }
                    }
                });
            }


            function createEventElement(event, showTitle = true) {
                const eventDiv = document.createElement('div');
                const status = eventStatuses[event.status] || eventStatuses['Belum Mulai'];

                eventDiv.className =
                    `rounded-lg px-2.5 py-1.5 text-xs cursor-pointer truncate ${status.bgColor} border-l-4 border-l-transparent hover:shadow-sm transition-all duration-200 group`;
                eventDiv.style.borderLeftColor = 'var(--event-color)';
                eventDiv.setAttribute('data-color', status.color);

                if (showTitle) {
                    eventDiv.innerHTML = `
                <div class="flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full" style="background: linear-gradient(to right, ${status.color.split('from-')[1].split(' ')[0]}, ${status.color.split('to-')[1]})"></div>
                    <span class="font-medium ${status.textColor}">${event.title}</span>
                </div>
            `;
                } else {
                    eventDiv.innerHTML = `
                <div class="flex items-center gap-1">
                    <div class="h-1.5 w-1.5 rounded-full" style="background: linear-gradient(to right, ${status.color.split('from-')[1].split(' ')[0]}, ${status.color.split('to-')[1]})"></div>
                    <span class="${status.textColor}">⋯</span>
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
                const status = eventStatuses[event.status] || eventStatuses['Belum Mulai'];

                // Format date
                const startDate = new Date(event.start_date);
                const endDate = new Date(event.end_date);
                const dateFormatOptions = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };

                let dateText = startDate.toLocaleDateString('id-ID', dateFormatOptions);
                if (!isSameDate(startDate, endDate)) {
                    dateText += ' s/d ' + endDate.toLocaleDateString('id-ID', dateFormatOptions);
                }

                // Calculate duration
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end date
                const durationText = diffDays === 1 ? '1 hari' : `${diffDays} hari`;

                // Update modal content
                eventTitle.textContent = event.title;
                eventDate.textContent = dateText;
                eventDuration.textContent = durationText;
                eventStatus.textContent = status.name;
                // eventDescription.textContent = event.description || 'Tidak ada deskripsi';
                // eventLocation.textContent = event.location || 'Tidak ditentukan';
                eventColor.className = `h-12 w-2 rounded-full bg-gradient-to-b ${status.color}`;
                modalHeaderBar.className =
                    `absolute top-0 left-0 right-0 h-2 rounded-t-2xl bg-gradient-to-r ${status.color}`;

                // Update participants
                eventParticipants.innerHTML = '';
                if (event.participants && event.participants.length > 0) {
                    event.participants.forEach(participant => {
                        const badge = document.createElement('span');
                        badge.className =
                            'inline-flex items-center px-3 py-1.5 rounded-full text-xs bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600';
                        badge.textContent = participant;
                        eventParticipants.appendChild(badge);
                    });
                } else {
                    eventParticipants.innerHTML =
                        '<span class="text-gray-500 dark:text-gray-400 italic">Tidak ada peserta</span>';
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
                }
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

                // Count events by status
                const ongoingCount = events.filter(event => event.status === 'Berjalan').length;
                const completedCount = events.filter(event => event.status === 'Selesai').length;

                ongoingEvents.textContent = ongoingCount;
                completedEvents.textContent = completedCount;
            }
        });
    </script>
@endsection
