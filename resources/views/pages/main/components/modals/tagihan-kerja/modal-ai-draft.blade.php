<x-ui.smart-modal id="modal-ai-draft" class="max-w-5xl" :showCloseButton="true">
    <div
        x-data="aiDraftAssistant()"
        @open-smart-modal.window="if ($event.detail.modalId === 'modal-ai-draft') initFromModal($event.detail)"
        class="relative flex max-h-[90vh] w-full flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900"
    >
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <div class="flex items-start gap-3 pr-12">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white">AI Assistant Global</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Ketik apa yang ingin dibuat. Jika kegiatan, sub kegiatan, anggota, atau jenisnya belum jelas, AI akan menanyakan atau menampilkan pilihan.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid min-h-0 flex-1 grid-cols-1 overflow-y-auto lg:grid-cols-12">
            <div class="flex min-h-[520px] flex-col border-b border-gray-200 bg-gray-50/60 dark:border-gray-800 dark:bg-gray-950/20 lg:col-span-5 lg:border-b-0 lg:border-r">
                <div class="shrink-0 border-b border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="contextLabel()"></div>
                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400" x-text="contextHint()"></div>
                        </div>
                        <button
                            type="button"
                            x-show="result"
                            @click="prompt = ''; result = null; draft = {}; messages = initialMessages()"
                            class="shrink-0 rounded-full border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            Reset
                        </button>
                    </div>
                </div>

                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-5">
                    <template x-for="(message, index) in messages" :key="index">
                        <div class="flex gap-2.5" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">
                            <div
                                x-show="message.role !== 'user'"
                                class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div
                                class="max-w-[82%] rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm"
                                :class="message.role === 'user'
                                    ? 'rounded-br-md bg-brand-600 text-white'
                                    : 'rounded-bl-md border border-gray-200 bg-white text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200'"
                            >
                                <p class="whitespace-pre-line" x-text="message.text"></p>
                            </div>
                        </div>
                    </template>

                    <div x-show="loading" class="flex gap-2.5">
                        <div class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </div>
                        <div class="rounded-2xl rounded-bl-md border border-gray-200 bg-white px-4 py-3 text-sm text-gray-500 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                            Memahami instruksi...
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p x-show="error" x-text="error" class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300"></p>
                    <div class="flex items-end gap-2 rounded-2xl border border-gray-300 bg-white p-2 shadow-sm transition focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800">
                        <textarea
                            x-model="prompt"
                            rows="2"
                            maxlength="2000"
                            @keydown.enter.exact.prevent="generate()"
                            placeholder="Tulis instruksi..."
                            class="max-h-32 min-h-[44px] flex-1 resize-none border-0 bg-transparent px-2 py-2 text-sm text-gray-800 outline-none placeholder:text-gray-400 focus:ring-0 dark:text-gray-100"
                        ></textarea>
                        <button
                            type="button"
                            @click="generate()"
                            :disabled="loading || !prompt.trim()"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                            :title="result ? 'Kirim revisi' : 'Kirim'"
                        >
                            <svg x-show="!loading" class="h-5 w-5 -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                            <svg x-show="loading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-gray-400 dark:text-gray-500">
                        <span>Enter untuk kirim</span>
                        <span>Shift + Enter untuk baris baru</span>
                    </div>
                </div>
            </div>

            <div class="p-6 lg:col-span-7">
                <template x-if="!result">
                    <div class="flex h-full min-h-[360px] items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50 text-center dark:border-gray-700 dark:bg-gray-950/30">
                        <div class="max-w-sm px-6">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Draft akan muncul di sini</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">AI akan menentukan form yang tepat, menampilkan pilihan konteks bila ambigu, lalu mengisi draft untuk Anda cek.</p>
                        </div>
                    </div>
                </template>

                <template x-if="result">
                    <div class="space-y-4">
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Draft Form</p>
                                    <h5 class="mt-1 text-lg font-bold text-gray-900 dark:text-white" x-text="formTitle()"></h5>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="result.summary || 'Silakan cek dan lengkapi draft berikut.'"></p>
                                </div>
                                <span class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300" x-text="confidenceLabel()"></span>
                            </div>
                        </div>

                        <template x-if="draftItems().length > 1">
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-950/40">
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Multi Draft</p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${activeItemIndex + 1} dari ${draftItems().length}`"></span>
                                </div>
                                <div class="flex gap-2 overflow-x-auto pb-1">
                                    <template x-for="(item, index) in draftItems()" :key="index">
                                        <button
                                            type="button"
                                            @click="selectDraftItem(index)"
                                            class="shrink-0 rounded-xl border px-3 py-2 text-left text-xs transition"
                                            :class="activeItemIndex === index
                                                ? 'border-brand-400 bg-brand-50 text-brand-700 dark:border-brand-500/60 dark:bg-brand-500/10 dark:text-brand-200'
                                                : 'border-gray-200 bg-white text-gray-600 hover:border-brand-200 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300'"
                                        >
                                            <span class="block font-semibold" x-text="`${index + 1}. ${shortFormTitle(item.target_form)}`"></span>
                                            <span class="block max-w-44 truncate text-gray-400" x-text="item.summary || previewDraftName(item)"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="result.warnings?.length">
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-500/30 dark:bg-amber-500/10">
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Perlu dicek</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-sm text-amber-700 dark:text-amber-200">
                                    <template x-for="warning in result.warnings" :key="warning">
                                        <li x-text="warning"></li>
                                    </template>
                                </ul>
                            </div>
                        </template>

                        <template x-if="result.choices?.length">
                            <div class="space-y-3">
                                <template x-for="choice in result.choices" :key="choice.field">
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/40">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="choice.label"></p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <template x-for="option in choice.options" :key="choice.field + option.value">
                                                <button
                                                    type="button"
                                                    @click="choose(choice.field, option)"
                                                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-left text-xs text-gray-700 transition hover:border-brand-300 hover:text-brand-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-brand-500"
                                                >
                                                    <span class="block font-semibold" x-text="option.label"></span>
                                                    <span class="block text-gray-400" x-show="option.reason" x-text="option.reason"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <template x-for="field in editableFields()" :key="field.name">
                                <label class="block rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                                    <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400" x-text="field.label"></span>
                                    <template x-if="field.type === 'textarea'">
                                        <textarea x-model="draft[field.name]" @input="syncActiveItem()" rows="2" class="w-full resize-none rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 outline-none focus:border-brand-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
                                    </template>
                                    <template x-if="field.type === 'switch'">
                                        <button
                                            type="button"
                                            @click="draft[field.name] = Number(draft[field.name]) === 1 ? 0 : 1; syncActiveItem()"
                                            class="flex w-full items-center justify-between rounded-lg border px-3 py-2.5 text-sm transition"
                                            :class="Number(draft[field.name]) === 1
                                                ? 'border-brand-300 bg-brand-50 text-brand-700 dark:border-brand-500/50 dark:bg-brand-500/10 dark:text-brand-200'
                                                : 'border-gray-300 bg-white text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                                        >
                                            <span x-text="Number(draft[field.name]) === 1 ? 'Aktif' : 'Tidak aktif'"></span>
                                            <span
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition"
                                                :class="Number(draft[field.name]) === 1 ? 'bg-brand-600' : 'bg-gray-300 dark:bg-gray-700'"
                                            >
                                                <span
                                                    class="inline-block h-5 w-5 rounded-full bg-white shadow transition"
                                                    :class="Number(draft[field.name]) === 1 ? 'translate-x-5' : 'translate-x-1'"
                                                ></span>
                                            </span>
                                        </button>
                                    </template>
                                    <template x-if="!['textarea', 'switch'].includes(field.type)">
                                        <input :type="field.type" x-model="draft[field.name]" @input="syncActiveItem()" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 outline-none focus:border-brand-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                    </template>
                                </label>
                            </template>
                        </div>

                        <template x-if="missingFields().length">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950/40 dark:text-gray-300">
                                <span class="font-semibold">Belum lengkap:</span>
                                <span x-text="missingFields().join(', ')"></span>
                            </div>
                        </template>

                        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-4 dark:border-gray-800 sm:flex-row sm:justify-end">
                            <button type="button" @click="result = null" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Reset Draft</button>
                            <button
                                type="button"
                                @click="applyDraft()"
                                :disabled="!canApply()"
                                class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Terapkan ke Form
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-ui.smart-modal>

<script>
    function aiDraftAssistant() {
        return {
            prompt: '',
            type: 'auto',
            context: {},
            result: null,
            draft: {},
            activeItemIndex: 0,
            messages: [],
            loading: false,
            error: '',

            initFromModal(detail) {
                this.type = detail.type || 'auto';
                this.context = detail.context || {};
                this.prompt = detail.prompt || '';
                this.result = null;
                this.draft = {};
                this.activeItemIndex = 0;
                this.error = '';
                this.messages = this.initialMessages();
            },

            initialMessages() {
                return [{
                    role: 'assistant',
                    text: 'Tulis instruksi bebas. Kalau konteksnya belum jelas, saya akan kasih pilihan yang bisa langsung diklik.'
                }];
            },

            compactHistory() {
                return this.messages
                    .filter(message => ['user', 'assistant'].includes(message.role) && message.text)
                    .slice(-10)
                    .map(message => ({
                        role: message.role,
                        text: String(message.text).slice(0, 1200),
                    }));
            },

            currentDraftState() {
                this.syncActiveItem();
                return {
                    active_index: this.activeItemIndex,
                    active_target_form: this.result?.target_form || null,
                    active_draft: { ...this.draft },
                    items: this.draftItems().map(item => ({
                        target_form: item.target_form || null,
                        summary: item.summary || '',
                        draft: { ...(item.draft || {}) },
                        missing_fields: item.missing_fields || [],
                    })),
                };
            },

            async generate() {
                this.loading = true;
                this.error = '';
                const userPrompt = this.prompt.trim();
                if (userPrompt) {
                    this.messages.push({ role: 'user', text: userPrompt });
                }

                try {
                    const response = await fetch(@js(route('ai.draft-form')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
                        },
                        body: JSON.stringify({
                            type: this.type,
                            prompt: userPrompt,
                            bidang_slug: this.context.bidang_slug || null,
                            kegiatan_id: this.context.id_kegiatan || null,
                            sub_kegiatan_id: this.context.id_sub_kegiatan || null,
                            history: this.compactHistory(),
                            draft_state: this.currentDraftState(),
                        })
                    });

                    const payload = await response.json();
                    if (!response.ok || !payload.ok) {
                        throw new Error(payload.message || 'AI belum bisa membuat draft.');
                    }

                    this.result = payload.result;
                    this.context = { ...this.context, ...(payload.context?.bidang || {}), server_context: payload.context };
                    this.activeItemIndex = 0;
                    this.selectDraftItem(0, false);
                    this.messages.push({
                        role: 'assistant',
                        text: this.result?.follow_up_question || this.result?.summary || this.multiDraftMessage()
                    });
                    this.prompt = '';
                } catch (e) {
                    this.error = e.message || 'Gagal menghubungi AI.';
                    this.messages.push({ role: 'assistant', text: this.error });
                } finally {
                    this.loading = false;
                }
            },

            contextLabel() {
                if (this.type === 'penugasan') return 'Mode penugasan anggota';
                if (this.type === 'sub_kegiatan') return 'Mode sub kegiatan';
                if (this.type === 'kegiatan') return 'Mode kegiatan';
                return 'Mode otomatis';
            },

            contextHint() {
                if (this.context.nama_sub_kegiatan) return `Konteks: ${this.context.nama_sub_kegiatan}`;
                if (this.context.nama_rk_kegiatan) return `Konteks: ${this.context.nama_rk_kegiatan}`;
                if (this.context.bidang_slug) return `Bidang: ${this.context.bidang_slug}`;
                return 'AI akan menentukan form yang paling sesuai dari prompt.';
            },

            formTitle() {
                const form = this.result?.target_form;
                if (form === 'penugasan') return 'Draft Penugasan';
                if (form === 'sub_kegiatan') return 'Draft Sub Kegiatan';
                if (form === 'kegiatan') return 'Draft Kegiatan';
                return 'Perlu Klarifikasi';
            },

            shortFormTitle(form) {
                if (form === 'penugasan') return 'Penugasan';
                if (form === 'sub_kegiatan') return 'Sub Kegiatan';
                if (form === 'kegiatan') return 'Kegiatan';
                return 'Klarifikasi';
            },

            confidenceLabel() {
                const value = Math.round((this.result?.confidence || 0) * 100);
                return `Keyakinan ${value}%`;
            },

            draftItems() {
                return Array.isArray(this.result?.items) && this.result.items.length
                    ? this.result.items
                    : (this.result ? [this.result] : []);
            },

            selectDraftItem(index, announce = true) {
                const item = this.draftItems()[index];
                if (!item) return;

                this.activeItemIndex = index;
                this.result = {
                    ...this.result,
                    ...item,
                    items: this.result?.items || [],
                    item_count: this.result?.item_count || this.draftItems().length,
                };
                this.draft = { ...(item.draft || {}) };

                if (announce) {
                    this.messages.push({
                        role: 'assistant',
                        text: `Draft ${index + 1} dipilih: ${item.summary || this.shortFormTitle(item.target_form)}.`
                    });
                }
            },

            syncActiveItem() {
                if (!Array.isArray(this.result?.items) || !this.result.items[this.activeItemIndex]) return;
                this.result.items[this.activeItemIndex] = {
                    ...this.result.items[this.activeItemIndex],
                    target_form: this.result.target_form,
                    intent: this.result.intent,
                    draft: { ...this.draft },
                    choices: this.result.choices || [],
                    missing_fields: this.result.missing_fields || [],
                    warnings: this.result.warnings || [],
                    confidence: this.result.confidence || 0,
                };
            },

            previewDraftName(item) {
                const draft = item?.draft || {};
                return draft.nama_rk_kegiatan || draft.nama_sub_kegiatan || draft.nama_anggota || draft.jenis_kegiatan || 'Draft';
            },

            multiDraftMessage() {
                const count = this.draftItems().length;
                return count > 1
                    ? `Saya membuat ${count} draft. Pilih salah satu draft di kanan, lalu terapkan ke form sesuai kebutuhan.`
                    : 'Saya sudah buat draft. Cek pilihan dan form di kanan.';
            },

            choose(field, option) {
                if (!this.result) return;

                if (field === 'target_form') {
                    this.result.target_form = option.value;
                    this.result.intent = `create_${option.value}`;
                    this.removeChoice(field);
                    this.syncActiveItem();
                    this.messages.push({ role: 'assistant', text: `Oke, saya pakai form ${option.label}.` });
                    return;
                }

                this.draft[field] = option.value;
                this.draft[`${field}_label`] = option.label;

                if (field === 'id_anggota') this.draft.nama_anggota = option.label;
                if (field === 'id_jenis_kegiatan') this.draft.jenis_kegiatan = option.label;
                if (field === 'id_penanggung_jawab') this.draft.nama_penanggung_jawab = option.label;
                if (field === 'rk_jpt') {
                    this.draft.iki_jpt = '';
                    this.draft.iki_jpt_label = '';
                }
                if (field === 'id_kegiatan') {
                    this.context.id_kegiatan = option.value;
                    this.context.nama_rk_kegiatan = option.label;
                }
                if (field === 'id_sub_kegiatan') {
                    this.context.id_sub_kegiatan = option.value;
                    this.context.nama_sub_kegiatan = option.label;
                }

                this.removeChoice(field);
                this.removeMissing(field);
                this.syncActiveItem();
                this.messages.push({ role: 'assistant', text: `${option.label} dipilih untuk ${this.fieldLabel(field)}.` });
            },

            removeChoice(field) {
                if (!this.result?.choices) return;
                this.result.choices = this.result.choices.filter(choice => choice.field !== field);
            },

            removeMissing(field) {
                if (!this.result?.missing_fields) return;
                this.result.missing_fields = this.result.missing_fields.filter(item => item !== field);
            },

            fieldLabel(field) {
                const labels = {
                    target_form: 'jenis form',
                    id_anggota: 'anggota',
                    id_jenis_kegiatan: 'jenis kegiatan',
                    id_kegiatan: 'kegiatan',
                    id_sub_kegiatan: 'sub kegiatan',
                    rk_jpt: 'RK JPT',
                    iki_jpt: 'IKI JPT',
                };

                return labels[field] || field;
            },

            editableFields() {
                const form = this.result?.target_form;
                const fields = {
                    kegiatan: [
                        ['nama_rk_kegiatan', 'Nama Kegiatan', 'text'],
                        ['tahun_kegiatan', 'Tahun', 'number'],
                        ['rk_jpt_label', 'RK JPT Terpilih', 'text'],
                        ['iki_jpt_label', 'IKI JPT Terpilih', 'text'],
                    ],
                    sub_kegiatan: [
                        ['nama_sub_kegiatan', 'Nama Sub Kegiatan', 'text'],
                        ['target', 'Target', 'number'],
                        ['satuan_target', 'Satuan Target', 'text'],
                        ['tanggal_mulai', 'Tanggal Mulai', 'date'],
                        ['tanggal_selesai', 'Tanggal Selesai', 'date'],
                    ],
                    penugasan: [
                        ['nama_anggota', 'Nama Anggota', 'text'],
                        ['jenis_kegiatan', 'Jenis Kegiatan', 'text'],
                        ['target', 'Target', 'number'],
                        ['satuan_target', 'Satuan Target', 'text'],
                        ['tanggal_mulai', 'Tanggal Mulai', 'date'],
                        ['tanggal_selesai', 'Tanggal Selesai', 'date'],
                        ['butuh_dl', 'Butuh DL', 'switch'],
                        ['butuh_translok', 'Butuh Translok', 'switch'],
                    ],
                };

                return (fields[form] || []).map(([name, label, type]) => ({ name, label, type }));
            },

            missingFields() {
                const required = {
                    kegiatan: ['nama_rk_kegiatan', 'tahun_kegiatan', 'rk_jpt', 'iki_jpt'],
                    sub_kegiatan: ['nama_sub_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'],
                    penugasan: ['id_anggota', 'id_jenis_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'],
                };

                let fields = [...(this.result?.missing_fields || []), ...(required[this.result?.target_form] || [])];
                if (this.result?.target_form === 'sub_kegiatan' && !this.context.id_kegiatan && !this.resultContext('kegiatan')) {
                    fields.push('id_kegiatan');
                }
                if (this.result?.target_form === 'penugasan' && !this.context.id_sub_kegiatan && !this.resultContext('sub_kegiatan')) {
                    fields.push('id_sub_kegiatan');
                }

                return [...new Set(fields)].filter(field => !this.draft[field] && !this.context[field]);
            },

            canApply() {
                return ['kegiatan', 'sub_kegiatan', 'penugasan'].includes(this.result?.target_form)
                    && this.missingFields().length === 0;
            },

            applyDraft() {
                this.syncActiveItem();
                const form = this.result?.target_form;
                const draft = { ...this.draft };

                if (form === 'penugasan') {
                    const serverSub = this.resultContext('sub_kegiatan');
                    const sub = {
                        id_sub_kegiatan: this.context.id_sub_kegiatan || serverSub?.id_sub_kegiatan || '',
                        nama_sub_kegiatan: this.context.nama_sub_kegiatan || serverSub?.nama_sub_kegiatan || '',
                        min_date: this.context.min_date || serverSub?.tanggal_mulai || '',
                        max_date: this.context.max_date || serverSub?.tanggal_selesai || '',
                    };

                    window.dispatchEvent(new CustomEvent('open-smart-modal', {
                        detail: {
                            modalId: 'modal-penugasan-anggota',
                            mode: 'create',
                            data: {
                                ...sub,
                                ...draft,
                                tanggal_tambahan: draft.tanggal_tambahan || [],
                            }
                        }
                    }));
                    return;
                }

                if (form === 'sub_kegiatan') {
                    const serverKegiatan = this.resultContext('kegiatan');
                    window.dispatchEvent(new CustomEvent('open-smart-modal', {
                        detail: {
                            modalId: 'modal-sub-kegiatan',
                            mode: 'create',
                            data: {
                                id_kegiatan: this.context.id_kegiatan || serverKegiatan?.id_kegiatan || '',
                                nama_rk_kegiatan: this.context.nama_rk_kegiatan || serverKegiatan?.nama_rk_kegiatan || '',
                                ...draft,
                            }
                        }
                    }));
                    return;
                }

                if (form === 'kegiatan') {
                    delete draft.rk_jpt_label;
                    delete draft.iki_jpt_label;

                    window.dispatchEvent(new CustomEvent('open-smart-modal', {
                        detail: {
                            modalId: 'modal-kegiatan',
                            mode: 'create',
                            data: draft
                        }
                    }));
                }
            },

            resultContext(key) {
                return this.context?.server_context?.[key] || null;
            },
        };
    }
</script>
