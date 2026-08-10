@extends('layouts.admin')

@section('title', 'Antrian Pending — Verifikator')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-black text-white">⏳ 1. Antrian Verifikasi Pending (Murni Baru)</h2>
            <p class="text-xs text-slate-400">Daftar pendaftar Teknisi dan Toko baru yang belum pernah diverifikasi.</p>
        </div>
        <button onclick="loadPendingData()" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-2 px-4 rounded-xl transition shadow">
            🔄 Refresh Antrian
        </button>
    </div>

    <!-- Teknisi Table -->
    <div class="bg-slate-900/40 rounded-2xl p-6 border border-slate-800 space-y-4">
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
            <span>🔧</span> Antrian Calon Teknisi
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="pb-3">Nama</th>
                        <th class="pb-3">Kategori</th>
                        <th class="pb-3">HP</th>
                        <th class="pb-3">Lokasi</th>
                        <th class="pb-3">Terdaftar</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="techTableBody" class="text-xs text-slate-300 divide-y divide-slate-800/50">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat antrian teknisi...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Toko Table -->
    <div class="bg-slate-900/40 rounded-2xl p-6 border border-slate-800 space-y-4">
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
            <span>🏪</span> Antrian Calon Toko Sparepart
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="pb-3">Nama Toko</th>
                        <th class="pb-3">Izin Usaha</th>
                        <th class="pb-3">Telepon</th>
                        <th class="pb-3">Alamat</th>
                        <th class="pb-3">Terdaftar</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="shopTableBody" class="text-xs text-slate-300 divide-y divide-slate-800/50">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat antrian toko...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail & Keputusan -->
<div id="decisionModal" style="display:none;" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-8 shadow-2xl space-y-6">
        <div class="flex justify-between items-center pb-4 border-b border-slate-800">
            <h3 class="text-base font-black text-white flex items-center gap-2">
                <span>📋</span> Detail Dokumen & Keputusan Verifikasi
            </h3>
            <button onclick="closeDecisionModal()" class="text-slate-400 hover:text-white font-bold text-lg">&times;</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left: Document Preview -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-brandOrange uppercase tracking-wider">Dokumen yang Diunggah</h4>
                
                <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-4">
                    <!-- Dynamic details will be loaded here -->
                    <div id="documentDetails" class="space-y-3 text-xs text-slate-300">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>

            <!-- Right: Decision Form -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-brandOrange uppercase tracking-wider">Formulir Keputusan</h4>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tindakan / Keputusan</label>
                        <select id="decisionStatus" class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition">
                            <option value="">— Pilih Keputusan —</option>
                            <option value="APPROVED">Setujui (Approve)</option>
                            <option value="NEEDS_REVISION">Minta Revisi (Needs Revision)</option>
                            <option value="REJECTED">Tolak Permanen (Reject)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Catatan Verifikasi (Wajib)</label>
                        <textarea id="decisionCatatan" rows="4" class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 outline-none text-white focus:border-brandOrange transition" placeholder="Tuliskan catatan alasan persetujuan, revisi dokumen, atau penolakan..."></textarea>
                    </div>

                    <button onclick="submitDecision()" class="w-full bg-brandOrange hover:bg-orange-600 text-white font-bold text-xs py-3.5 rounded-xl transition shadow">
                        Simpan & Terapkan Keputusan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentTargetType = '';
let currentTargetId = null;

async function loadPendingData() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/verifications/pending', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const d = await r.json();

        // Populate Technicians
        const techs = d.new_registrations.technicians || [];
        const techTbody = document.getElementById('techTableBody');
        if (!techs.length) {
            techTbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Tidak ada antrian pending baru</td></tr>';
        } else {
            techTbody.innerHTML = techs.map(t => `
                <tr class="hover:bg-slate-800/30">
                    <td class="py-3 font-semibold text-white">${t.nama}</td>
                    <td class="py-3">${t.kategori}</td>
                    <td class="py-3">${t.hp}</td>
                    <td class="py-3 text-slate-400">${t.latitude ? t.latitude + ', ' + t.longitude : 'Belum diset'}</td>
                    <td class="py-3 text-slate-500">${new Date(t.created_at).toLocaleDateString('id-ID')}</td>
                    <td class="py-3 text-right">
                        <button onclick="openVerifyModal('teknisi', ${JSON.stringify(t).replace(/"/g, '&quot;')})" class="bg-brandOrange hover:bg-orange-600 text-white font-bold px-3 py-1.5 rounded-lg transition text-[10px]">
                            Tinjau
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Populate Shops
        const shops = d.new_registrations.shops || [];
        const shopTbody = document.getElementById('shopTableBody');
        if (!shops.length) {
            shopTbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Tidak ada antrian pending baru</td></tr>';
        } else {
            shopTbody.innerHTML = shops.map(s => `
                <tr class="hover:bg-slate-800/30">
                    <td class="py-3 font-semibold text-white">${s.nama}</td>
                    <td class="py-3 font-mono">${s.izin_usaha}</td>
                    <td class="py-3">${s.telepon}</td>
                    <td class="py-3 text-slate-400 max-w-xs truncate">${s.alamat}</td>
                    <td class="py-3 text-slate-500">${new Date(s.created_at).toLocaleDateString('id-ID')}</td>
                    <td class="py-3 text-right">
                        <button onclick="openVerifyModal('toko', ${JSON.stringify(s).replace(/"/g, '&quot;')})" class="bg-brandOrange hover:bg-orange-600 text-white font-bold px-3 py-1.5 rounded-lg transition text-[10px]">
                            Tinjau
                        </button>
                    </td>
                </tr>
            `).join('');
        }

    } catch (e) {
        console.error(e);
    }
}

function openVerifyModal(type, data) {
    currentTargetType = type;
    currentTargetId = data.id;

    document.getElementById('decisionStatus').value = '';
    document.getElementById('decisionCatatan').value = '';

    const token = localStorage.getItem('admin_token');
    const docContainer = document.getElementById('documentDetails');
    
    if (type === 'teknisi') {
        const ktpLink = data.ktp 
            ? `<a href="/api/mitra/documents/ktp/${data.id}?token=${token}" target="_blank" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg mt-2 inline-block">📄 Buka Foto KTP</a>`
            : `<span class="text-[10px] text-slate-500 font-semibold block mt-2">KTP Belum Diunggah</span>`;

        const certLink = data.sertifikat 
            ? `<a href="/api/mitra/documents/sertifikat/${data.id}?token=${token}" target="_blank" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg mt-2 inline-block">📜 Buka Sertifikat</a>`
            : `<span class="text-[10px] text-slate-500 font-semibold block mt-2">Sertifikat Belum Diunggah</span>`;

        docContainer.innerHTML = `
            <div class="space-y-2">
                <p><strong>Nama Teknisi:</strong> ${data.nama}</p>
                <p><strong>Nomor HP:</strong> ${data.hp}</p>
                <p><strong>Spesialisasi Kategori:</strong> ${data.kategori}</p>
                <p><strong>Tier Kinerja:</strong> ${data.tier}</p>
                <hr class="border-slate-800" />
                <p class="font-bold text-slate-400">Pratinjau Dokumen Teknis:</p>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-3 text-center">
                        <p class="text-[9px] text-slate-500 uppercase font-black">Foto KTP</p>
                        <div class="text-3xl py-2">🪪</div>
                        ${ktpLink}
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-3 text-center">
                        <p class="text-[9px] text-slate-500 uppercase font-black">Sertifikat Kompetensi</p>
                        <div class="text-3xl py-2">👤</div>
                        ${certLink}
                    </div>
                </div>
            </div>
        `;
    } else {
        const docIzinLink = data.dokumen_izin 
            ? `<a href="/api/mitra/documents/dokumen_izin/${data.id}?token=${token}" target="_blank" class="bg-brandOrange hover:bg-orange-600 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg mt-2 inline-block">📄 Buka Izin Usaha</a>`
            : `<span class="text-[10px] text-slate-500 font-semibold block mt-2">SIUP/NIB Belum Diunggah</span>`;

        docContainer.innerHTML = `
            <div class="space-y-2">
                <p><strong>Nama Toko Mitra:</strong> ${data.nama}</p>
                <p><strong>Telepon Toko:</strong> ${data.telepon}</p>
                <p><strong>Nomor Izin Usaha (NIB):</strong> <span class="font-mono text-brandOrange">${data.izin_usaha}</span></p>
                <p><strong>Alamat Toko:</strong> ${data.alamat}</p>
                <hr class="border-slate-800" />
                <p class="font-bold text-slate-400">Dokumen Legalitas:</p>
                <div class="grid grid-cols-1 gap-3 mt-2">
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-3 text-center">
                        <p class="text-[9px] text-slate-500 uppercase font-black">Izin Usaha / NIB</p>
                        <div class="text-3xl py-2">📄</div>
                        ${docIzinLink}
                    </div>
                </div>
            </div>
        `;
    }

    document.getElementById('decisionModal').style.display = 'flex';
}

function closeDecisionModal() {
    document.getElementById('decisionModal').style.display = 'none';
}

async function submitDecision() {
    const token = localStorage.getItem('admin_token');
    const status = document.getElementById('decisionStatus').value;
    const catatan = document.getElementById('decisionCatatan').value.trim();

    if (!status || !catatan) {
        Swal.fire({
            title: 'Lengkapi Formulir',
            text: 'Tindakan keputusan dan catatan wajib diisi.',
            icon: 'warning',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#F97316'
        });
        return;
    }

    try {
        const r = await fetch('/api/verifications/decision', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                target_type: currentTargetType,
                target_id: currentTargetId,
                status: status,
                catatan: catatan
            })
        });

        const d = await r.json();
        if (r.ok) {
            Swal.fire({
                title: 'Keputusan Disimpan!',
                text: d.message,
                icon: 'success',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
            closeDecisionModal();
            loadPendingData();
        } else {
            Swal.fire({
                title: 'Gagal',
                text: d.message || 'Terjadi kesalahan.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    } catch(e) {
        console.error(e);
    }
}

// Auto init page
function initPage() {
    loadPendingData();
}
</script>
@endsection
