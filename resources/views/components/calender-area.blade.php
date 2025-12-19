<div>
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="custom-calendar">
            <div id="calendar" class="min-h-screen"></div>
        </div>
    </div>

    <!-- Modal -->
    <div class="fixed inset-0 z-99999 flex items-center justify-center p-5 modal hidden" id="eventModal">

        <!-- overlay -->
        <div class="modal-close-btn fixed inset-0 bg-gray-400/50 backdrop-blur-[2px]"></div>

        <!-- modal card (FIX) -->
        <div
            class="relative z-10 flex w-full max-w-[700px] flex-col
               rounded-3xl bg-white dark:bg-gray-900
               max-h-[90vh]">

            <!-- Close Button -->
            <button
                class="modal-close-btn absolute top-5 right-5 z-20 flex h-8 w-8
                   items-center justify-center rounded-full bg-gray-100
                   text-gray-400 hover:bg-gray-200 hover:text-gray-600
                   sm:h-11 sm:w-11 dark:bg-white/[0.05] dark:text-gray-400">
                ✕
            </button>

            <!-- HEADER (FIX) -->
            <div class="px-6 pt-6 lg:px-11">
                <h5 class="mb-2 font-semibold text-gray-800 text-theme-xl lg:text-2xl dark:text-white/90">
                    Add Event
                </h5>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Plan your next big moment: schedule or edit an event to stay on track
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div
                class="my-6 flex-1 overflow-y-auto px-6 lg:px-11 custom-scrollbar">

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Event Title -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Event Title
                        </label>
                        <input id="event-title" type="text" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Enter event title" />
                    </div>

                    <!-- Event Color -->
                    <div class="mt-6">
                        <label class="block mb-4 text-sm font-medium text-gray-700 dark:text-gray-400">
                            Event Color
                        </label>
                        <div class="flex flex-wrap items-center gap-4 sm:gap-5">

                            <!-- Danger -->
                            <div class="n-chk">
                                <div class="form-check form-check-danger form-check-inline">
                                    <label class="flex items-center text-sm text-gray-700 form-check-label dark:text-gray-400" for="modalDanger">
                                        <span class="relative">
                                            <input class="sr-only form-check-input" type="radio" name="event-level" value="Danger" id="modalDanger" />
                                            <span class="flex items-center justify-center w-5 h-5 mr-2 border border-gray-300 rounded-full box dark:border-gray-700">
                                            </span>
                                        </span>
                                        Danger
                                    </label>
                                </div>
                            </div>

                            <!-- Success -->
                            <div class="n-chk">
                                <div class="form-check form-check-success form-check-inline">
                                    <label class="flex items-center text-sm text-gray-700 form-check-label dark:text-gray-400" for="modalSuccess">
                                        <span class="relative">
                                            <input class="sr-only form-check-input" type="radio" name="event-level" value="Success" id="modalSuccess" />
                                            <span class="flex items-center justify-center w-5 h-5 mr-2 border border-gray-300 rounded-full box dark:border-gray-700">
                                            </span>
                                        </span>
                                        Success
                                    </label>
                                </div>
                            </div>

                            <!-- Primary -->
                            <div class="n-chk">
                                <div class="form-check form-check-primary form-check-inline">
                                    <label class="flex items-center text-sm text-gray-700 form-check-label dark:text-gray-400" for="modalPrimary">
                                        <span class="relative">
                                            <input class="sr-only form-check-input" type="radio" name="event-level" value="Primary" id="modalPrimary" />
                                            <span class="flex items-center justify-center w-5 h-5 mr-2 border border-gray-300 rounded-full box dark:border-gray-700">
                                            </span>
                                        </span>
                                        Primary
                                    </label>
                                </div>
                            </div>

                            <!-- Warning -->
                            <div class="n-chk">
                                <div class="form-check form-check-warning form-check-inline">
                                    <label class="flex items-center text-sm text-gray-700 form-check-label dark:text-gray-400" for="modalWarning">
                                        <span class="relative">
                                            <input class="sr-only form-check-input" type="radio" name="event-level" value="Warning" id="modalWarning" />
                                            <span class="flex items-center justify-center w-5 h-5 mr-2 border border-gray-300 rounded-full box dark:border-gray-700">
                                            </span>
                                        </span>
                                        Warning
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="mt-6">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Enter Start Date
                        </label>
                        <div class="relative">
                            <input id="event-start-date" type="date" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="mt-6">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Enter End Date
                        </label>
                        <div class="relative">
                            <input id="event-end-date" type="date" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" onclick="this.showPicker()" />
                            <span class="absolute top-1/2 right-3.5 -translate-y-1/2 pointer-events-none">
                                <svg class="fill-gray-700 dark:fill-gray-400" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.33317 0.0830078C4.74738 0.0830078 5.08317 0.418794 5.08317 0.833008V1.24967H8.9165V0.833008C8.9165 0.418794 9.25229 0.0830078 9.6665 0.0830078C10.0807 0.0830078 10.4165 0.418794 10.4165 0.833008V1.24967L11.3332 1.24967C12.2997 1.24967 13.0832 2.03318 13.0832 2.99967V4.99967V11.6663C13.0832 12.6328 12.2997 13.4163 11.3332 13.4163H2.6665C1.70001 13.4163 0.916504 12.6328 0.916504 11.6663V4.99967V2.99967C0.916504 2.03318 1.70001 1.24967 2.6665 1.24967L3.58317 1.24967V0.833008C3.58317 0.418794 3.91896 0.0830078 4.33317 0.0830078ZM4.33317 2.74967H2.6665C2.52843 2.74967 2.4165 2.8616 2.4165 2.99967V4.24967H11.5832V2.99967C11.5832 2.8616 11.4712 2.74967 11.3332 2.74967H9.6665H4.33317ZM11.5832 5.74967H2.4165V11.6663C2.4165 11.8044 2.52843 11.9163 2.6665 11.9163H11.3332C11.4712 11.9163 11.5832 11.8044 11.5832 11.6663V5.74967Z" fill="" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER (FIX) -->
            <div
                class="flex items-center gap-3 border-t px-6 py-4
                   sm:justify-end lg:px-11 dark:border-gray-700">

                <button type="button"
                    class="modal-close-btn flex w-full justify-center rounded-lg
                       border border-gray-300 bg-white px-4 py-2.5 text-sm
                       font-medium text-gray-700 hover:bg-gray-50 sm:w-auto
                       dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    Close
                </button>

                <button type="button"
                    class="btn btn-add-event bg-brand-500 hover:bg-brand-600
                       flex w-full justify-center rounded-lg px-4 py-2.5
                       text-sm font-medium text-white sm:w-auto">
                    Add Event
                </button>
            </div>
        </div>
    </div>
</div>