@extends('layouts.superadmin')

@section('title', 'Kebijakan Komisi — REVARO')
@section('page-title', 'Data Master — Kebijakan Komisi')
@section('page-subtitle', 'Atur persentase bagi hasil jasa servis dan fee sparepart yang berlaku di seluruh platform')

@section('content')
<div class="space-y-6">
    <!-- Active Policy Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="card p-6 relative overflow-hidden">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Komisi Jasa Servis (Aktif)</p>
            <h3 class="text-4xl font-black text-brandOrange mt-2" id="komisiBadge">—%</h3>
            <p class="text-xs text-slate-500 mt-1">Potongan dari biaya jasa teknisi setiap transaksi selesai</p>
        </div>
        <div class="card p-6 relative overflow-hidden">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fee Sparepart (Aktif)</p>
            <h3 class="text-4xl font-black text-blue-400 mt-2" id="feeBadge">—%</h3>
            <p class="text-xs text-slate-500 mt-1">Potongan dari nilai transaksi sparepart toko mitra</p>
        </div>
    </div>

    <!-- Update Policy Form -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-1">Update Kebijakan Komisi</h3>
        <p class="text-xs text-slate-500 mb-5">Perubahan ini langsung berlaku untuk semua transaksi baru setelah disimpan.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Persentase Komisi Jasa (%)</label>
                <input type="number" id="komisiPct" class="input-field" placeholder="cth. 10" min="0" max="100" step="0.5">
                <p class="text-[10px] text-slate-500 mt-1">Contoh: isi 10 = 10% dari biaya jasa teknisi</p>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Persentase Fee Sparepart (%)</label>
                <input type="number" id="feePct" class="input-field" placeholder="cth. 5" min="0" max="100" step="0.5">
                <p class="text-[10px] text-slate-500 mt-1">Contoh: isi 5 = 5% dari nilai total sparepart</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button onclick="loadPolicy()" class="btn-outline">Reset ke Nilai Saat Ini</button>
            <button onclick="savePolicy()" class="btn-primary">Simpan Kebijakan</button>
        </div>
    </div>

    <!-- History Note -->
    <div class="card p-5" style="border-color:rgba(234,179,8,0.2);background:rgba(234,179,8,0.04);">
        <div class="flex gap-3 items-start">
            <svg class="w-5 h-5 text-yellow-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-yellow-400 text-sm font-bold">Catatan Penting</p>
                <p class="text-slate-400 text-xs mt-1">Perubahan kebijakan komisi hanya berlaku untuk transaksi <strong class="text-white">baru</strong> setelah disimpan. Transaksi yang sudah berjalan atau selesai <strong class="text-white">tidak terpengaruh</strong>. Pastikan perubahan sudah dikomunikasikan kepada tim operasional sebelum diaplikasikan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function loadPolicy() {
    const token = localStorage.getItem('admin_token');
    try {
        const r = await fetch('/api/admin/commission-policy', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
        const d = await r.json();
        document.getElementById('komisiBadge').textContent = (d.persen_komisi_jasa ?? '—') + '%';
        document.getElementById('feeBadge').textContent = (d.persen_fee_sparepart ?? '—') + '%';
        document.getElementById('komisiPct').value = d.persen_komisi_jasa ?? '';
        document.getElementById('feePct').value = d.persen_fee_sparepart ?? '';
    } catch(e) {}
}

async function savePolicy() {
    const token = localStorage.getItem('admin_token');
    const pct = parseFloat(document.getElementById('komisiPct').value);
    const fee = parseFloat(document.getElementById('feePct').value);
    if (isNaN(pct) || isNaN(fee) || pct < 0 || fee < 0 || pct > 100 || fee > 100) {
        Swal.fire({ title: 'Input Tidak Valid', text: 'Nilai harus antara 0–100.', icon: 'warning', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        return;
    }
    const confirm = await Swal.fire({ title: 'Konfirmasi Perubahan?', html: `Komisi jasa: <b>${pct}%</b><br>Fee sparepart: <b>${fee}%</b><br><br>Ini akan berlaku untuk semua transaksi baru.`, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Simpan', cancelButtonText: 'Batal', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316', cancelButtonColor: '#475569' });
    if (!confirm.isConfirmed) return;
    try {
        const r = await fetch('/api/admin/commission-policy', { method: 'PUT', headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ persen_komisi_jasa: pct, persen_fee_sparepart: fee }) });
        const d = await r.json();
        if (r.ok) {
            Swal.fire({ title: 'Disimpan!', text: 'Kebijakan komisi berhasil diperbarui.', icon: 'success', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
            loadPolicy();
        } else {
            Swal.fire({ title: 'Gagal', text: d.message || 'Terjadi kesalahan.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', loadPolicy);
</script>
@endsection
