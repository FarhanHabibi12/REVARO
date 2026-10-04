@extends('layouts.admin')

@section('title', 'Evaluasi Rating & Disiplin Mitra — Resolusi')

@section('styles')
<style>
    .eval-card {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.25rem;
    }
    .eval-card-active {
        border-color: #FF7F00;
        box-shadow: 0 0 0 1px #FF7F00, 0 10px 25px -5px rgba(255, 127, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Top Summary Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 p-6 md:p-8 rounded-3xl border border-slate-800">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 font-extrabold text-[10px] uppercase tracking-wider flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span>Disiplin &amp; Mutu Layanan</span>
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 font-bold text-[10px] uppercase">
                    Admin Resolusi
                </span>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">Evaluasi Ulasan &amp; Kinerja Teknisi</h2>
            <p class="text-xs text-slate-400 max-w-2xl leading-relaxed">
                Tinjau kasus keluhan mutu akibat ulasan bintang rendah (1–2 bintang). Admin Resolusi berhak memberikan SP, penurunan tier, atau <strong>BANNED / Pembekuan Akun Permanen</strong> agar teknisi pelanggar tidak dapat login ke sistem.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <button onclick="loadRatingCases()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs py-2.5 px-4 rounded-xl transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Muat Ulang</span>
            </button>
        </div>
    </div>

    <!-- Main Content Layout (Generous Spacing) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Daftar Kasus Rating Buruk (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Antrean Evaluasi Rating Buruk</span>
                </h3>
                <span id="caseCountBadge" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">0 Kasus</span>
            </div>

            <div id="ratingCasesListContainer" class="space-y-4 overflow-y-auto max-h-[75vh] pr-1.5 scrollbar-thin scrollbar-thumb-slate-800">
                <div class="eval-card p-8 text-center space-y-2">
                    <p class="text-xs text-slate-400">Memuat daftar kasus evaluasi rating...</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Panel Investigasi & Eksekusi Sanksi BANNED (7 Cols) -->
        <div class="lg:col-span-7">
            
            <!-- Empty state when no case selected -->
            <div id="noCaseSelectedPanel" class="eval-card p-12 text-center flex flex-col items-center justify-center min-h-[520px] space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center shadow-inner">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-200">Pilih Kasus Evaluasi Teknisi</h3>
                <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                    Klik salah satu laporan ulasan buruk di sebelah kiri untuk meninjau riwayat kasus, chat order, dan mengeksekusi sanksi <strong>BANNED</strong> atau <strong>Surat Peringatan</strong>.
                </p>
            </div>

            <!-- Active Case Details & Action Panel -->
            <div id="activeCasePanel" style="display: none;" class="space-y-6">
                
                <!-- Technician Card with Case Counter -->
                <div class="eval-card p-6 md:p-7 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-brandOrange to-amber-500 text-white font-black text-xl flex items-center justify-center shadow-lg border border-white/20 shrink-0" id="techAvatarInitial">
                                T
                            </div>
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-white" id="techNama">—</h3>
                                    <span id="techStatusBadge" class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        AKTIF
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400" id="techCategory">—</p>
                                <p class="text-[11px] text-slate-500 font-mono" id="techPhone">—</p>
                            </div>
                        </div>

                        <!-- Accumulation badge & Direct Ban -->
                        <div class="text-left sm:text-right space-y-1.5">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 font-black text-xs" id="techAccumulationBadge">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span id="techCaseCountLabel">0x Kasus Rating Buruk</span>
                            </div>
                            <p class="text-[10px] text-slate-400">Rerata Rating: <strong class="text-amber-400 font-bold" id="techAvgRating">0.0</strong></p>
                        </div>
                    </div>

                    <!-- Direct Quick Ban Warning Bar -->
                    <div id="bannedNoticeBar" style="display: none;" class="p-3.5 bg-red-950/40 border border-red-800 rounded-xl flex items-center justify-between gap-3 text-xs text-red-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span><strong>AKUN TEKNISI INI TELAH DI-BANNED / FROZEN</strong>. Teknisi tidak dapat login ke aplikasi.</span>
                        </div>
                    </div>
                </div>

                <!-- Complaint Details & Review Card -->
                <div class="eval-card p-6 md:p-7 space-y-5">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <span>Rincian Ulasan &amp; Keluhan Pelanggan</span>
                        </h4>
                        <span class="font-mono text-xs text-brandOrange font-bold" id="evalOrderCode">#—</span>
                    </div>

                    <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-400 font-medium">Pelapor:</span>
                                <strong class="text-xs text-white" id="evalPelangganName">—</strong>
                            </div>
                            <div class="flex items-center gap-1 text-amber-400 text-sm font-black" id="evalStars">
                                ★☆☆☆☆
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-900">
                            <p class="text-xs text-slate-300 italic leading-relaxed" id="evalCustomerComment">
                                "—"
                            </p>
                        </div>
                    </div>

                    <!-- Order Details Summary -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-slate-400">
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-850">
                            <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5">Biaya Jasa</span>
                            <span class="text-white font-bold" id="evalNotaJasa">Rp —</span>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-850">
                            <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5">Biaya Sparepart</span>
                            <span class="text-white font-bold" id="evalNotaParts">Rp —</span>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-850">
                            <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5">Total Order</span>
                            <span class="text-brandOrange font-black" id="evalNotaTotal">Rp —</span>
                        </div>
                    </div>

                    <!-- Chat Logs Accordion -->
                    <div class="pt-2">
                        <button type="button" onclick="toggleChatLogs()" class="w-full flex items-center justify-between p-3 rounded-xl bg-slate-950/80 border border-slate-800 text-xs font-bold text-slate-300 hover:text-white transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Log Percakapan Chat Pelanggan - Teknisi
                            </span>
                            <span id="chatToggleIcon">▼</span>
                        </button>
                        <div id="evalChatLogsContainer" class="hidden mt-2 p-4 bg-slate-950 rounded-xl border border-slate-800 max-h-52 overflow-y-auto space-y-2 text-xs">
                            <p class="text-slate-500 text-center py-4">Memuat pesan chat...</p>
                        </div>
                    </div>
                </div>

                <!-- Decision & Sanction Form Panel -->
                <div class="eval-card p-6 md:p-8 space-y-5 border-2 border-slate-800">
                    <div class="border-b border-slate-800 pb-3">
                        <h4 class="text-sm font-black text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            <span>Eksekusi Putusan Disiplin Staf Resolusi</span>
                        </h4>
                        <p class="text-xs text-slate-400 mt-1">
                            Pilih sanksi kedisiplinan yang tepat. Jika rating buruk berulang atau terjadi fraud/kelalaian parah, pilih <strong>BANNED</strong> untuk memblokir login teknisi secara instan.
                        </p>
                    </div>

                    <form onsubmit="event.preventDefault(); submitRatingEvaluation();" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Pilih Tindakan / Sanksi *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-red-900/60 bg-red-950/20 hover:bg-red-950/40 cursor-pointer transition">
                                    <input type="radio" name="evalDecision" value="BAN_TEKNISI" class="mt-1 accent-red-500">
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-extrabold text-red-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            BANNED / Bekukan Akun
                                        </span>
                                        <span class="text-[10px] text-slate-400 leading-tight block">Blokir login permanen, bekukan status FROZEN, nonaktifkan akun.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-amber-900/60 bg-amber-950/20 hover:bg-amber-950/40 cursor-pointer transition">
                                    <input type="radio" name="evalDecision" value="SURAT_PERINGATAN" class="mt-1 accent-amber-500">
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-extrabold text-amber-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Terbitkan Surat Peringatan (SP)
                                        </span>
                                        <span class="text-[10px] text-slate-400 leading-tight block">Kirim SP resmi ke teknisi. Akumulasi 3x SP otomatis di-BANNED sistem.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-blue-900/60 bg-blue-950/20 hover:bg-blue-950/40 cursor-pointer transition">
                                    <input type="radio" name="evalDecision" value="TURUNKAN_TIER" class="mt-1 accent-blue-500">
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-extrabold text-blue-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                            Turunkan Tier Kemitraan
                                        </span>
                                        <span class="text-[10px] text-slate-400 leading-tight block">Turunkan tier (Gold &rarr; Silver &rarr; Bronze) akibat penurunan mutu.</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3.5 rounded-xl border border-emerald-900/60 bg-emerald-950/20 hover:bg-emerald-950/40 cursor-pointer transition">
                                    <input type="radio" name="evalDecision" value="SELESAIKAN_EVALUASI" class="mt-1 accent-emerald-500">
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-extrabold text-emerald-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Selesaikan &amp; Arsipkan
                                        </span>
                                        <span class="text-[10px] text-slate-400 leading-tight block">Klarifikasi selesai tanpa penalti lanjutan, arsipkan evaluasi.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Catatan Evaluasi / Bukti Pelanggaran (Wajib) *</label>
                            <textarea id="evalNotesInput" required rows="3" class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3.5 text-white outline-none focus:border-brandOrange transition font-medium" placeholder="Tuliskan catatan evaluasi pertimbangan staf, rincian keluhan pelanggan, atau dasar hukum penjatuhan sanksi..."></textarea>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <button type="button" onclick="directBanPrompt()" class="w-full sm:w-auto px-4 py-3 rounded-xl bg-red-950 hover:bg-red-900 text-red-400 hover:text-white border border-red-800 text-xs font-bold transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                <span>Ban Teknisi Langsung</span>
                            </button>
                            
                            <button type="submit" id="btnSubmitEval" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-brandOrange hover:bg-orange-600 text-white font-extrabold text-xs transition shadow flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Terapkan Keputusan Evaluasi</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentCases = [];
let activeCase = null;

async function loadRatingCases() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const container = document.getElementById('ratingCasesListContainer');
    const badge = document.getElementById('caseCountBadge');

    try {
        container.innerHTML = '<div class="eval-card p-8 text-center text-xs text-slate-400">Memuat data kasus evaluasi rating...</div>';
        const res = await fetch('/api/admin/resolusi/rating-evaluations', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        
        if (!res.ok) {
            container.innerHTML = '<div class="eval-card p-8 text-center text-xs text-red-400">Gagal memuat data evaluasi rating.</div>';
            return;
        }

        const cases = await res.json();
        currentCases = cases;
        badge.textContent = `${cases.length} Kasus`;

        if (cases.length === 0) {
            container.innerHTML = `
                <div class="eval-card p-10 text-center space-y-2">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-slate-800/80 border border-slate-700 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-300">Tidak Ada Kasus Rating Buruk</h4>
                    <p class="text-[10px] text-slate-500">Semua order diselesaikan dengan mutu pelayanan baik.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = cases.map((c, idx) => {
            const order = c.order || {};
            const tech = order.teknisi || {};
            const cust = order.pelanggan || {};
            const isResolved = c.status === 'RESOLVED';
            const isBanned = c.is_banned;
            const badCases = c.total_bad_rating_cases || 1;

            return `
                <div onclick="selectCase(${idx})" class="eval-card p-4 transition cursor-pointer hover:border-brandOrange space-y-3 ${activeCase && activeCase.id === c.id ? 'eval-card-active' : ''}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-mono font-bold text-brandOrange">#${order.kode_order || c.order_id}</span>
                            <h4 class="text-xs font-black text-white">${tech.nama || 'Teknisi'}</h4>
                            <p class="text-[10px] text-slate-400">Pelapor: ${cust.nama || 'Pelanggan'}</p>
                        </div>
                        <div class="text-right shrink-0 space-y-1">
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase ${isBanned ? 'bg-red-500/20 text-red-400 border border-red-500/30' : (isResolved ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30')}">
                                ${isBanned ? 'BANNED' : (isResolved ? 'SELESAI' : 'PERLU TINDAKAN')}
                            </span>
                            <p class="text-[9px] text-red-400 font-bold flex items-center justify-end gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                ${badCases}x Kasus
                            </p>
                        </div>
                    </div>
                    <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-850 text-[11px] text-slate-300 line-clamp-2 italic">
                        "${c.alasan || 'Rating rendah diterima.'}"
                    </div>
                </div>
            `;
        }).join('');

    } catch(e) {
        console.error(e);
        container.innerHTML = '<div class="eval-card p-8 text-center text-xs text-red-400">Error memuat antrian evaluasi.</div>';
    }
}

function selectCase(idx) {
    const c = currentCases[idx];
    if (!c) return;
    activeCase = c;

    // Highlight card
    loadRatingCases(); // Re-render to update active styling

    document.getElementById('noCaseSelectedPanel').style.display = 'none';
    document.getElementById('activeCasePanel').style.display = 'block';

    const order = c.order || {};
    const tech = order.teknisi || {};
    const cust = order.pelanggan || {};
    const transaksi = order.transaksi || {};

    // Fill tech info
    document.getElementById('techNama').textContent = tech.nama || 'Teknisi';
    document.getElementById('techAvatarInitial').textContent = (tech.nama || 'T').charAt(0).toUpperCase();
    document.getElementById('techCategory').textContent = `Kategori: ${tech.kategori || 'Elektronik'} • Tier ${tech.tier || 'Bronze'}`;
    document.getElementById('techPhone').textContent = `Kontak: ${tech.hp || '-'}`;
    document.getElementById('techAvgRating').textContent = `★ ${parseFloat(tech.rating || 0).toFixed(1)} / 5.0`;
    document.getElementById('techCaseCountLabel').textContent = `${c.total_bad_rating_cases || 1}x Kasus Rating Buruk Diterima`;

    const statusBadge = document.getElementById('techStatusBadge');
    if (c.is_banned || tech.status === 'FROZEN') {
        statusBadge.textContent = 'BANNED / FROZEN';
        statusBadge.className = 'px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30';
        document.getElementById('bannedNoticeBar').style.display = 'flex';
    } else {
        statusBadge.textContent = tech.status || 'VERIFIED';
        statusBadge.className = 'px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
        document.getElementById('bannedNoticeBar').style.display = 'none';
    }

    // Fill order & complaint
    document.getElementById('evalOrderCode').textContent = `#${order.kode_order || c.order_id}`;
    document.getElementById('evalPelangganName').textContent = cust.nama || 'Pelanggan';
    document.getElementById('evalCustomerComment').textContent = `"${c.alasan || 'Keluhan mutu servis.'}"`;

    // Calculate stars from reason text if available
    let stars = '★☆☆☆☆';
    if (c.alasan && c.alasan.includes('2 Bintang')) stars = '★★☆☆☆';
    else if (c.alasan && c.alasan.includes('3 Bintang')) stars = '★★★☆☆';
    document.getElementById('evalStars').textContent = stars;

    // Fill nota
    const jasa = parseFloat(transaksi.biaya_jasa || 0);
    const parts = parseFloat(transaksi.biaya_part || 0);
    const total = parseFloat(transaksi.total || jasa + parts);
    document.getElementById('evalNotaJasa').textContent = `Rp ${jasa.toLocaleString('id-ID')}`;
    document.getElementById('evalNotaParts').textContent = `Rp ${parts.toLocaleString('id-ID')}`;
    document.getElementById('evalNotaTotal').textContent = `Rp ${total.toLocaleString('id-ID')}`;

    // Load chats
    loadOrderChats(order.id);
}

async function loadOrderChats(orderId) {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const container = document.getElementById('evalChatLogsContainer');
    if (!orderId) {
        container.innerHTML = '<p class="text-slate-500 text-center py-4">Data order tidak ditemukan.</p>';
        return;
    }
    try {
        const res = await fetch(`/api/orders/${orderId}/chats`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        if (res.ok) {
            const chats = await res.json();
            if (chats.length === 0) {
                container.innerHTML = '<p class="text-slate-500 text-center py-4">Belum ada riwayat pesan chat pada order ini.</p>';
                return;
            }
            container.innerHTML = chats.map(ch => {
                const time = new Date(ch.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                return `
                    <div class="p-2.5 bg-slate-900/80 rounded-lg space-y-1">
                        <div class="flex justify-between text-[10px] text-slate-400">
                            <span class="font-bold text-brandOrange">${ch.sender ? ch.sender.nama : 'Pengirim'}</span>
                            <span>${time}</span>
                        </div>
                        <p class="text-xs text-slate-200 leading-snug">${ch.pesan}</p>
                    </div>
                `;
            }).join('');
        }
    } catch(e) {
        container.innerHTML = '<p class="text-slate-500 text-center py-4">Tidak dapat memuat log chat.</p>';
    }
}

function toggleChatLogs() {
    const c = document.getElementById('evalChatLogsContainer');
    const icon = document.getElementById('chatToggleIcon');
    if (c.classList.contains('hidden')) {
        c.classList.remove('hidden');
        icon.textContent = '▲';
    } else {
        c.classList.add('hidden');
        icon.textContent = '▼';
    }
}

async function submitRatingEvaluation() {
    if (!activeCase) return;

    const selectedRadio = document.querySelector('input[name="evalDecision"]:checked');
    if (!selectedRadio) {
        Swal.fire({
            title: 'Pilih Tindakan Sanksi',
            text: 'Silakan pilih salah satu opsi keputusan/sanksi kedisiplinan.',
            icon: 'warning',
            background: '#0F172A',
            color: '#E2E8F0',
            confirmButtonColor: '#FF6A00'
        });
        return;
    }

    const decision = selectedRadio.value;
    const notes = document.getElementById('evalNotesInput').value.trim();

    if (!notes || notes.length < 5) {
        Swal.fire({
            title: 'Catatan Evaluasi Wajib',
            text: 'Tuliskan catatan evaluasi minimal 5 karakter sebagai bukti arsip keputusan staf.',
            icon: 'warning',
            background: '#0F172A',
            color: '#E2E8F0',
            confirmButtonColor: '#FF6A00'
        });
        return;
    }

    const decisionLabels = {
        'BAN_TEKNISI': 'BANNED / Pembekuan Akun Permanen (Teknisi tidak bisa login)',
        'SURAT_PERINGATAN': 'Terbitkan Surat Peringatan (SP)',
        'TURUNKAN_TIER': 'Turunkan Tier Kemitraan',
        'SELESAIKAN_EVALUASI': 'Selesaikan & Arsipkan Kasus'
    };

    const confirmRes = await Swal.fire({
        title: 'Konfirmasi Putusan Resolusi',
        html: `
            <div class="text-left space-y-2 text-xs text-slate-300">
                <p>Keputusan yang dipilih:</p>
                <div class="p-3 bg-slate-900 border border-slate-700 rounded-xl font-bold text-brandOrange">
                    ${decisionLabels[decision]}
                </div>
                <p class="text-slate-400">Tindakan ini akan dicatat dalam rekam jejak resmi sistem.</p>
            </div>
        `,
        icon: 'question',
        background: '#0F172A',
        color: '#E2E8F0',
        showCancelButton: true,
        confirmButtonColor: decision === 'BAN_TEKNISI' ? '#DC2626' : '#FF6A00',
        confirmButtonText: 'Ya, Terapkan Sekarang',
        cancelButtonText: 'Batal'
    });

    if (!confirmRes.isConfirmed) return;

    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const submitBtn = document.getElementById('btnSubmitEval');
    submitBtn.disabled = true;
    submitBtn.innerText = 'Memproses...';

    try {
        const res = await fetch(`/api/admin/resolusi/evaluate-rating/${activeCase.id}`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                keputusan: decision,
                catatan: notes
            })
        });

        const data = await res.json();
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Terapkan Keputusan Evaluasi</span>
        `;

        if (res.ok) {
            Swal.fire({
                title: 'Putusan Berhasil Diterapkan!',
                text: data.message,
                icon: 'success',
                background: '#0F172A',
                color: '#E2E8F0',
                confirmButtonColor: '#FF6A00'
            });
            document.getElementById('evalNotesInput').value = '';
            loadRatingCases();
        } else {
            Swal.fire({
                title: 'Gagal',
                text: data.message || 'Terjadi kesalahan saat memproses keputusan.',
                icon: 'error',
                background: '#0F172A',
                color: '#E2E8F0',
                confirmButtonColor: '#FF6A00'
            });
        }
    } catch(e) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Terapkan Keputusan Evaluasi</span>
        `;
        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
    }
}

async function directBanPrompt() {
    if (!activeCase || !activeCase.order || !activeCase.order.teknisi) return;
    const tech = activeCase.order.teknisi;

    const { value: reason } = await Swal.fire({
        title: `Ban Akun Teknisi: ${tech.nama}`,
        text: 'Masukkan alasan pembekuan akun. Teknisi akan langsung dibekukan dan tidak dapat login ke aplikasi REVARO.',
        input: 'textarea',
        inputPlaceholder: 'Tuliskan alasan pembekuan akun...',
        inputValidator: (v) => (!v || v.trim().length < 5) && 'Alasan pembekuan wajib diisi minimal 5 karakter!',
        background: '#0F172A',
        color: '#E2E8F0',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        confirmButtonText: 'Ya, Bekukan & Ban Akun',
        cancelButtonText: 'Batal'
    });

    if (!reason) return;

    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    try {
        const res = await fetch(`/api/admin/teknisi/${tech.id}/ban`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ alasan: reason })
        });
        const data = await res.json();
        if (res.ok) {
            Swal.fire({
                title: 'Akun Teknisi Berhasil Di-Ban!',
                text: data.message,
                icon: 'success',
                background: '#0F172A',
                color: '#E2E8F0',
                confirmButtonColor: '#FF6A00'
            });
            loadRatingCases();
        } else {
            Swal.fire('Gagal', data.message || 'Gagal memproses pembekuan.', 'error');
        }
    } catch(e) {
        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
    }
}

function initPage() {
    loadRatingCases();
}

window.addEventListener('load', () => {
    initPage();
});
</script>
@endsection
