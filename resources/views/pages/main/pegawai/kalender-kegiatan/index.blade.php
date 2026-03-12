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
                class="bg-gradient-to-br from-yellow-50 to-white dark:from-gray-800 dark:to-gray-900 p-4 rounded-2xl border border-yellow-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Belum Mulai</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="pending-events">0</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 p-4 rounded-2xl border border-blue-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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

        {{-- Smart Modal Detail Kegiatan --}}
        <x-ui.smart-modal id="modal-detail-kegiatan" :isOpen="false" :showCloseButton="true" class="max-w-md">
            <div class="relative">
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-3xl" id="modal-header-bar"></div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 mt-2">Detail Kegiatan</h3>
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-start gap-3 mb-4">
                                <div id="event-color" class="h-12 w-2 rounded-full mt-1"></div>
                                <div class="flex-1">
                                    <h4 id="event-title" class="font-bold text-xl text-gray-900 dark:text-white mb-2"></h4>
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span id="event-date"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span id="event-duration"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Asal Bidang
                                </h5>
                                <p id="event-bidang" class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg font-medium">
                                </p>
                            </div>
                            
                            <div>
                                <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Status
                                </h5>
                                <span id="event-status" class="inline-block px-3 py-1 text-sm font-medium rounded border"></span>
                            </div>

                            <div>
                                <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 4.75v-4.5" />
                                    </svg>
                                    Peserta
                                </h5>
                                <div id="event-participants" class="flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.smart-modal>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            let currentMonth = {{ count($events) > 0 ? $currentMonth : date('n') }};
            let currentYear = {{ count($events) > 0 ? $currentYear : date('Y') }};
            let jsMonth = currentMonth - 1; // 0-11
            let events = @js($events);
            
            // Re-assign variable scope safely if not set:
            if(typeof currentMonth === 'undefined' || !currentMonth) { currentMonth = new Date().getMonth() + 1; }
            if(typeof currentYear === 'undefined' || !currentYear) { currentYear = new Date().getFullYear(); }
            
            const calendarGrid = document.getElementById('calendar-grid');
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            const currentMonthYear = document.getElementById('current-month-year');
            const currentMonthNav = document.getElementById('current-month-nav');
            
            const eventTitle = document.getElementById('event-title');
            const eventBidang = document.getElementById('event-bidang');
            const eventDate = document.getElementById('event-date');
            const eventDuration = document.getElementById('event-duration');
            const eventStatus = document.getElementById('event-status');
            const eventParticipants = document.getElementById('event-participants');
            const eventColor = document.getElementById('event-color');
            const modalHeaderBar = document.getElementById('modal-header-bar');

            const pendingEvents = document.getElementById('pending-events');
            const ongoingEvents = document.getElementById('ongoing-events');
            const completedEvents = document.getElementById('completed-events');

            // Beautiful Colors for Bidang coding
            const bidangColors = [
                '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', 
                '#06B6D4', '#EAB308', '#22C55E', '#F97316', '#6366F1', '#14B8A6',
                '#A855F7', '#84CC16', '#D946EF', '#0EA5E9', '#F43F5E', '#64748B'
            ];

            function getColorForBidang(bidangName) {
                let hash = 0;
                for (let i = 0; i < bidangName.length; i++) {
                    hash = bidangName.charCodeAt(i) + ((hash << 5) - hash);
                }
                return bidangColors[Math.abs(hash) % bidangColors.length];
            }

            // Fetch Holidays
            let holidays = [];
            try {
                // Fetch public holidays API
                const response = await fetch(`https://dayoffapi.vercel.app/api?month=${currentMonth}&year=${currentYear}`);
                const data = await response.json();
                holidays = data.filter(h => h.is_national_holiday).map(h => {
                     return new Date(h.holiday_date).getDate();
                });
            } catch(e) {
                console.warn('Gagal memuat info API Hari Libur', e);
            }

            renderCalendar(currentYear, jsMonth);
            updateStats();

            prevMonthBtn.addEventListener('click', () => {
                let newMonth = currentMonth - 1;
                let newYear = currentYear;
                if (newMonth < 1) { newMonth = 12; newYear--; }
                window.location.href = `?month=${newMonth}&year=${newYear}`;
            });

            nextMonthBtn.addEventListener('click', () => {
                let newMonth = currentMonth + 1;
                let newYear = currentYear;
                if (newMonth > 12) { newMonth = 1; newYear++; }
                window.location.href = `?month=${newMonth}&year=${newYear}`;
            });

            function renderCalendar(year, month) {
                calendarGrid.innerHTML = '';
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay();

                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                currentMonthYear.textContent = `${monthNames[month]} ${year}`;
                currentMonthNav.textContent = `${monthNames[month]}`;

                // Padding blank cells before first day
                for (let i = 0; i < startingDay; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'min-h-32 bg-gray-50/50 dark:bg-gray-800/10 border-r border-b border-gray-100 dark:border-gray-700';
                    calendarGrid.appendChild(emptyCell);
                }

                // Days blocks
                for (let day = 1; day <= daysInMonth; day++) {
                    const cell = createDateCell(year, month, day);
                    calendarGrid.appendChild(cell);
                }

                // Padding blank cells after last day
                const totalCells = Math.ceil((startingDay + daysInMonth) / 7) * 7;
                const emptyCellsAfter = totalCells - (startingDay + daysInMonth);
                for (let i = 0; i < emptyCellsAfter; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'min-h-32 bg-gray-50/50 dark:bg-gray-800/10 border-r border-b border-gray-100 dark:border-gray-700';
                    calendarGrid.appendChild(emptyCell);
                }

                // Draw actual bars
                renderEventsOnCalendar(year, month, startingDay);
            }

            function createDateCell(year, month, day) {
                const date = new Date(year, month, day);
                const cell = document.createElement('div');
                
                const isToday = isSameDate(date, new Date());
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const isHoliday = holidays.includes(day);

                let bgClass = isWeekend || isHoliday 
                    ? 'bg-red-50/20 dark:bg-red-900/10' 
                    : 'bg-white dark:bg-gray-800';

                cell.className = `min-h-36 py-1 flex flex-col items-stretch group transition-all duration-200 ${bgClass} border-r border-b border-gray-100 dark:border-gray-700 relative overflow-hidden`;

                // Date Number Header
                const dateHeader = document.createElement('div');
                dateHeader.className = `flex justify-end items-start mt-1 px-1 min-h-[1.75rem] z-10`;

                const dayNumber = document.createElement('span');
                let dayColor = 'text-gray-700 dark:text-gray-300';
                if (isToday) {
                    dayColor = 'bg-blue-600 dark:bg-blue-500 text-white shadow-lg';
                } else if (isHoliday || date.getDay() === 0) { // Minggu juga merah
                    dayColor = 'text-red-500 dark:text-red-400 font-bold'; 
                }

                dayNumber.className = `inline-flex items-center justify-center h-7 w-7 rounded-full text-sm font-medium transition-all duration-200 ${dayColor}`;
                dayNumber.textContent = day;

                dateHeader.appendChild(dayNumber);
                cell.appendChild(dateHeader);

                // Container untuk event slot rows pada hari ini
                const eventsContainer = document.createElement('div');
                eventsContainer.className = 'flex flex-col flex-1 pb-1 px-0 relative z-10 gap-0.5';
                
                // Tambahkan minimum skeleton slot yang cukup tinggi
                // Biar tinggi cell merata saat grid tidak penuh acara
                for(let slot=0; slot<5; slot++) {
                     let row = document.createElement('div');
                     row.className = 'h-5 w-full';
                     eventsContainer.appendChild(row);
                }
                
                cell.appendChild(eventsContainer);
                return cell;
            }

            function renderEventsOnCalendar(year, month, startingDay) {
                const dateCells = document.querySelectorAll('#calendar-grid > div');
                const monthStart = new Date(year, month, 1);
                const monthEnd   = new Date(year, month + 1, 0);
                
                // Array track ketersediaan baris. slots[day][row] = isOccupied
                const slotMap = [];
                for(let i=0; i<31; i++) slotMap.push({});

                // Sort events (Event panjang pertama)
                events.sort((a, b) => {
                    const spanA = new Date(a.end_date) - new Date(a.start_date);
                    const spanB = new Date(b.end_date) - new Date(b.start_date);
                    return spanB - spanA; // Descending length
                });

                events.forEach(event => {
                    const eventStart = new Date(event.start_date);
                    const eventEnd   = new Date(event.end_date);

                    if (eventEnd < monthStart || eventStart > monthEnd) return; 

                    const startDay = eventStart < monthStart ? 1 : eventStart.getDate();
                    const endDay   = eventEnd > monthEnd ? monthEnd.getDate() : eventEnd.getDate();
                    const color = getColorForBidang(event.bidang || 'Umum');

                    // Cari row slot yang kosong berturut-turut dari startDay ke endDay
                    let slotAssigned = -1;
                    for (let slot = 0; slot < 10; slot++) { // Max 10 tumpuk visualnya
                        let canFit = true;
                        for (let day = startDay; day <= endDay; day++) {
                            if (slotMap[day-1][slot]) { canFit = false; break; }
                        }
                        if (canFit) {
                            slotAssigned = slot;
                            break;
                        }
                    }
                    if (slotAssigned === -1) slotAssigned = 10; // Fallback jika penuh

                    // Tandai row slot menjadi occupied
                    for (let day = startDay; day <= endDay; day++) {
                        slotMap[day-1][slotAssigned] = true;
                        
                        const cellIndex = startingDay + day - 1;
                        if (cellIndex < dateCells.length && dateCells[cellIndex]) {
                            const eventsContainer = dateCells[cellIndex].querySelector('div:last-child');
                            
                            if (eventsContainer && slotAssigned < eventsContainer.children.length) {
                                
                                const isFirstDay = (day === startDay) || (new Date(year, month, day).getDay() === 1); // print text di senin juga
                                const isRealFirstDay = (day === startDay);
                                const isLastDay = (day === endDay);
                                
                                const targetSlot = eventsContainer.children[slotAssigned];
                                
                                // Buat komponen batangnya
                                targetSlot.className = `h-5 flex items-center px-1 text-[10px] leading-none cursor-pointer text-white overflow-hidden`;
                                targetSlot.style.backgroundColor = color;
                                
                                if (isRealFirstDay && isLastDay) {
                                    targetSlot.classList.add('rounded-md', 'mx-1');
                                } else if (isRealFirstDay) {
                                    targetSlot.classList.add('rounded-l-md', 'ml-1');
                                } else if (isLastDay) {
                                    targetSlot.classList.add('rounded-r-md', 'mr-1');
                                }

                                if (isFirstDay || isRealFirstDay) {
                                    targetSlot.innerHTML = `<span class="font-semibold truncate w-full z-20">${event.title}</span>`;
                                } else {
                                    targetSlot.innerHTML = `<span></span>`; 
                                }

                                targetSlot.title = event.title + ` (${event.bidang})`;
                                
                                // Event Listener Modal
                                targetSlot.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    showEventDetails(event, color);
                                });
                                targetSlot.addEventListener('mouseenter', (e) => {
                                    targetSlot.style.filter = 'brightness(0.8)';
                                });
                                targetSlot.addEventListener('mouseleave', (e) => {
                                    targetSlot.style.filter = 'brightness(1)';
                                });
                            }
                        }
                    }
                });
            }

            function showEventDetails(event, color) {
                const startDate = new Date(event.start_date);
                const endDate = new Date(event.end_date);
                const dateFormatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

                let dateText = startDate.toLocaleDateString('id-ID', dateFormatOptions);
                if (!isSameDate(startDate, endDate)) {
                    dateText += ' s/d ' + endDate.toLocaleDateString('id-ID', dateFormatOptions);
                }

                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                eventTitle.textContent = event.title;
                if(eventBidang) eventBidang.textContent = event.bidang || 'Umum';
                if(eventDate) eventDate.textContent = dateText;
                if(eventDuration) eventDuration.textContent = diffDays === 1 ? '1 hari' : `${diffDays} hari`;
                
                if(eventColor) eventColor.style.backgroundColor = color;
                if(modalHeaderBar) Object.assign(modalHeaderBar.style, { background: color }); // ubah header color
                
                const statusMap = {
                    'Belum Mulai': 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'Berjalan': 'bg-blue-50 text-blue-700 border-blue-200',
                    'Selesai': 'bg-green-50 text-green-700 border-green-200'
                };
                if(eventStatus) {
                    eventStatus.className = `inline-block px-3 py-1 text-xs font-semibold rounded-md border ${statusMap[event.status] || ''}`;
                    eventStatus.textContent = event.status;
                }

                if(eventParticipants) {
                    eventParticipants.innerHTML = '';
                    if (event.participants && event.participants.length > 0) {
                        event.participants.forEach(participant => {
                            const badge = document.createElement('span');
                            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700';
                            badge.textContent = participant;
                            eventParticipants.appendChild(badge);
                        });
                    } else {
                        eventParticipants.innerHTML = '<span class="text-gray-500 dark:text-gray-400 italic text-sm">Tidak ada peserta yang disematkan</span>';
                    }
                }

                // Pancarkan Custom Event Alpine JS
                window.dispatchEvent(new CustomEvent('open-smart-modal', {
                    detail: { modalId: 'modal-detail-kegiatan' }
                }));
            }

            function isSameDate(d1, d2) {
                return d1.getFullYear() === d2.getFullYear() &&
                    d1.getMonth() === d2.getMonth() &&
                    d1.getDate() === d2.getDate();
            }

            function updateStats() {
                pendingEvents.textContent = events.filter(e => e.status === 'Belum Mulai').length;
                ongoingEvents.textContent = events.filter(e => e.status === 'Berjalan').length;
                completedEvents.textContent = events.filter(e => e.status === 'Selesai').length;
            }
        });
    </script>
@endsection
