@extends('layouts.admin')

@section('title', 'Arahan & Peringatan Pemilik — Admin Verifikator')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h2 class="text-xl font-black text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                <span>Instruksi &amp; Peringatan dari Pemilik Sistem</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Daftar arahan khusus, peringatan operasional, dan direktif pengawasan dari Super Admin Pemilik
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="fetchInstructions()" class="btn-outline text-xs py-2 px-3 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Segarkan</span>
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 border-l-4 border-l-orange-500 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Instruksi Diterima</p>
                <p class="text-2xl font-black text-white mt-1" id="statTotal">0</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
        </div>

        <div class="card p-5 border-l-4 border-l-red-500 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Belum Dikonfirmasi</p>
                <p class="text-2xl font-black text-red-400 mt-1" id="statUnread">0</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <div class="card p-5 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sudah Ditindaklanjuti</p>
                <p class="text-2xl font-black text-emerald-400 mt-1" id="statRead">0</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & List Container -->
    <div class="card p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <button onclick="setFilter('ALL')" id="tabAll" class="filter-tab active-tab text-xs px-3 py-1.5 rounded-lg font-bold">
                    Semua Arahan
                </button>
                <button onclick="setFilter('UNREAD')" id="tabUnread" class="filter-tab text-xs px-3 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white">
                    Perlu Tindakan
                </button>
                <button onclick="setFilter('READ')" id="tabRead" class="filter-tab text-xs px-3 py-1.5 rounded-lg font-bold text-slate-400 hover:text-white">
                    Selesai
                </button>
            </div>
            <span class="text-xs text-slate-500" id="filterCountInfo">Menampilkan data...</span>
        </div>

        <!-- Instructions List -->
        <div id="instructionsList" class="space-y-3">
            <div class="text-center py-12 text-slate-500 text-sm">
                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data instruksi dari database...
            </div>
        </div>
    </div>
</div>

<style>
.active-tab {
    background-color: rgba(255, 106, 0, 0.15);
    color: #FF6A00 !important;
    border: 1px solid rgba(255, 106, 0, 0.3);
}
</style>
@endsection

@section('scripts')
<script>
let allNotifs = [];
let currentFilter = 'ALL';

async function fetchInstructions() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const container = document.getElementById('instructionsList');
    
    try {
        const res = await fetch('/api/notifications', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) throw new Error('Gagal mengambil data instruksi');
        
        const data = await res.json();
        allNotifs = data.notifications || [];
        
        // Update stats
        const total = allNotifs.length;
        const unread = allNotifs.filter(n => !n.dibaca).length;
        const read = total - unread;
        
        document.getElementById('statTotal').innerText = total;
        document.getElementById('statUnread').innerText = unread;
        document.getElementById('statRead').innerText = read;
        
        renderInstructions();
    } catch(err) {
        console.error(err);
        container.innerHTML = `
            <div class="text-center py-8 text-red-400 text-xs">
                Gagal terhubung ke database notifikasi: ${err.message}
            </div>
        `;
    }
}

function setFilter(filter) {
    currentFilter = filter;
    
    ['tabAll', 'tabUnread', 'tabRead'].forEach(id => {
        document.getElementById(id).classList.remove('active-tab');
        document.getElementById(id).classList.add('text-slate-400');
    });
    
    if (filter === 'ALL') {
        document.getElementById('tabAll').classList.add('active-tab');
        document.getElementById('tabAll').classList.remove('text-slate-400');
    } else if (filter === 'UNREAD') {
        document.getElementById('tabUnread').classList.add('active-tab');
        document.getElementById('tabUnread').classList.remove('text-slate-400');
    } else if (filter === 'READ') {
        document.getElementById('tabRead').classList.add('active-tab');
        document.getElementById('tabRead').classList.remove('text-slate-400');
    }
    
    renderInstructions();
}

function renderInstructions() {
    const container = document.getElementById('instructionsList');
    let filtered = allNotifs;
    
    if (currentFilter === 'UNREAD') {
        filtered = allNotifs.filter(n => !n.dibaca);
    } else if (currentFilter === 'READ') {
        filtered = allNotifs.filter(n => n.dibaca);
    }
    
    document.getElementById('filterCountInfo').innerText = `${filtered.length} Instruksi ditemukan`;
    
    if (!filtered.length) {
        container.innerHTML = `
            <div class="text-center py-12 rounded-2xl bg-slate-900/30 border border-slate-800/80 space-y-2">
                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-800/80 border border-slate-700 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-white">Tidak ada instruksi ${currentFilter === 'UNREAD' ? 'yang perlu tindakan' : ''}</p>
                <p class="text-xs text-slate-500 mt-1">Semua arahan dan evaluasi pengawasan dari Pemilik Sistem telah ditindaklanjuti.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = filtered.map(item => {
        const isUnread = !item.dibaca;
        const dateStr = new Date(item.created_at).toLocaleString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });
        
        return `
            <div class="p-5 rounded-2xl border transition-all ${isUnread ? 'bg-slate-900/90 border-orange-500/40 shadow-lg shadow-orange-500/5' : 'bg-slate-950/50 border-slate-800/70'}">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${isUnread ? 'bg-orange-500/15 text-orange-400 border border-orange-500/30' : 'bg-slate-800 text-slate-400'}">
                            ${isUnread 
                                ? '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>' 
                                : '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'
                            }
                        </div>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="text-sm font-black ${isUnread ? 'text-white' : 'text-slate-300'}">
                                    ${escapeHtml(item.judul)}
                                </h4>
                                ${isUnread 
                                    ? '<span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">Wajib Ditindaklanjuti</span>'
                                    : '<span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">Dikonfirmasi Selesai</span>'
                                }
                            </div>
                            <p class="text-xs ${isUnread ? 'text-slate-300 font-medium' : 'text-slate-400'} leading-relaxed mt-1">
                                ${escapeHtml(item.pesan)}
                            </p>
                            <p class="text-[10px] text-slate-500 font-mono mt-2">
                                Diterbitkan: ${dateStr} WIB
                            </p>
                        </div>
                    </div>
                    
                    <div class="shrink-0 pt-2 sm:pt-0 self-end sm:self-start">
                        ${isUnread ? `
                            <button onclick="markAsAcknowledged(${item.id})" class="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-md shadow-orange-500/20">
                                <span>✓</span> Konfirmasi &amp; Tandai Selesai
                            </button>
                        ` : `
                            <span class="text-xs text-slate-500 font-semibold flex items-center gap-1">
                                <span>✓</span> Sudah Ditindaklanjuti
                            </span>
                        `}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

async function markAsAcknowledged(id) {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    
    try {
        const res = await fetch(`/api/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (res.ok) {
            Swal.fire({
                title: 'Arahan Dikonfirmasi',
                text: 'Instruksi telah ditandai selesai dan status diperbarui di database.',
                icon: 'success',
                background: '#0F172A',
                color: '#F8FAFC',
                confirmButtonColor: '#FF6A00',
                timer: 1500,
                showConfirmButton: false
            });
            await fetchInstructions();
        }
    } catch(err) {
        console.error(err);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function initPage() {
    fetchInstructions();
}

document.addEventListener('DOMContentLoaded', fetchInstructions);
</script>
@endsection
