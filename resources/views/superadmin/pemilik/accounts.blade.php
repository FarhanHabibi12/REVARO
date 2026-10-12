@extends('layouts.superadmin')

@section('title', 'Manajemen Akun Admin — REVARO')
@section('page-title', 'Manajemen Akun Admin')
@section('page-subtitle', 'Buat, aktifkan, atau nonaktifkan akun internal (Verifikator, Resolusi, Monitoring)')

@section('content')
<div class="space-y-6">
    <!-- Create Account Form -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4">Tambah Akun Admin Baru</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap Admin</label>
                <input type="text" id="newAdminNama" class="input-field" placeholder="cth. Farhan Habibi">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Akun Google / Email</label>
                <input type="email" id="newAdminEmail" class="input-field" placeholder="admin@gmail.com">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Role / Jabatan</label>
                <select id="newAdminRole" class="input-field">
                    <option value="">— Pilih Role —</option>
                    <option value="admin-verifikator">Admin Verifikator</option>
                    <option value="admin-resolusi">Admin Resolusi</option>
                </select>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kata Sandi Awal</label>
                    <span class="text-[9px] text-orange-400 font-bold px-1.5 py-0.5 rounded bg-orange-500/10 border border-orange-500/20">Auto-Generate</span>
                </div>
                <div class="relative flex items-center">
                    <input type="text" id="newAdminPassword" class="input-field font-mono text-orange-300 font-semibold pr-24 tracking-wider" placeholder="Password acak...">
                    <div class="absolute right-1 flex items-center gap-0.5">
                        <button type="button" onclick="generateNewPassword()" title="Acak Ulang Password" class="p-1 rounded text-slate-400 hover:text-white hover:bg-slate-700/60 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                        <button type="button" onclick="togglePasswordVisibility('newAdminPassword', 'pwdEyeIcon')" title="Lihat / Sembunyikan" class="p-1 rounded text-slate-400 hover:text-white hover:bg-slate-700/60 transition">
                            <span id="pwdEyeIcon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </span>
                        </button>
                        <button type="button" onclick="copyPasswordFromInput()" title="Salin Password" class="p-1 rounded text-slate-400 hover:text-orange-400 hover:bg-slate-700/60 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Password otomatis digenerate dan dapat dilihat di atas. Admin baru wajib mengatur username &amp; kata sandi baru saat login pertama.</span>
            </p>
            <button id="btnSubmitCreateAdmin" onclick="createAdmin()" class="btn-primary shrink-0">+ Buat Akun Admin</button>
        </div>
    </div>

    <!-- Account List -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-extrabold text-white">Daftar Akun Admin Internal</h3>
            <input type="text" id="searchAdmin" class="input-field" style="width:220px;" placeholder="Cari nama atau email..." oninput="filterTable()">
        </div>
        <div class="overflow-x-auto">
            <table class="data-table" id="adminTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card p-6">
        <h3 class="text-sm font-extrabold text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-brandOrange" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Audit Log Aktivitas Admin</span>
        </h3>
        <div id="auditLogList" class="space-y-2">
            <p class="text-slate-500 text-sm text-center py-4">Memuat log...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allAdmins = [];

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/'/g, '&#39;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function generateRandomPassword(length = 10) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$';
    let result = '';
    const array = new Uint32Array(length);
    window.crypto.getRandomValues(array);
    for (let i = 0; i < length; i++) {
        result += chars[array[i] % chars.length];
    }
    return result;
}

function generateNewPassword() {
    const pwd = generateRandomPassword(10);
    const input = document.getElementById('newAdminPassword');
    if (input) {
        input.value = pwd;
    }
}

function copyPasswordFromInput() {
    const input = document.getElementById('newAdminPassword');
    if (!input || !input.value) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            background: '#1E293B',
            color: '#E2E8F0'
        });
        toast.fire({ icon: 'success', title: 'Password awal berhasil disalin!' });
    }).catch(() => {
        // Fallback jika clipboard permissions terbatas
        input.select();
        document.execCommand('copy');
        alert('Password berhasil disalin!');
    });
}

function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const iconSpan = document.getElementById(iconId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        if (iconSpan) {
            iconSpan.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>`;
        }
    } else {
        input.type = 'password';
        if (iconSpan) {
            iconSpan.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
        }
    }
}

function copyCreds(email, pwd) {
    const text = `Kredensial Admin REVARO:\nEmail: ${email}\nPassword: ${pwd}`;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('btnCopyCredsModal');
        if (btn) btn.innerHTML = `<span>✓ Kredensial Berhasil Disalin!</span>`;
    }).catch(() => {
        prompt('Salin kredensial berikut:', text);
    });
}

function copyResetCreds(email, pwd) {
    const text = `Kredensial Baru Admin REVARO:\nEmail: ${email}\nPassword Baru: ${pwd}`;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('btnCopyResetCreds');
        if (btn) btn.innerHTML = `<span>✓ Kredensial Baru Berhasil Disalin!</span>`;
    }).catch(() => {
        prompt('Salin kredensial berikut:', text);
    });
}

async function loadAdmins() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    try {
        const r = await fetch('/api/admin/accounts', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        if (!r.ok) throw new Error('Status: ' + r.status);
        allAdmins = await r.json();
        renderTable(allAdmins);
    } catch(e) {
        document.getElementById('adminTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-red-400 py-8">Gagal memuat data akun admin.</td></tr>';
    }
}

function roleLabel(role) {
    const map = {
        'admin-verifikator': 'Verifikator',
        'admin-resolusi': 'Resolusi',
        'super-admin-monitoring': 'Monitoring',
        'super-admin-pemilik': 'Pemilik'
    };
    return map[role] || role;
}

function renderTable(data) {
    const tbody = document.getElementById('adminTableBody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-slate-500 py-8">Tidak ada data akun.</td></tr>';
        return;
    }
    const loggedInUser = JSON.parse(localStorage.getItem('admin_user') || '{}');
    tbody.innerHTML = data.map(a => `
        <tr>
            <td class="font-semibold text-white">${escapeHtml(a.nama)}</td>
            <td class="text-slate-400">${escapeHtml(a.email)}</td>
            <td><span class="badge badge-blue">${roleLabel(a.role)}</span></td>
            <td><span class="badge ${a.is_active ? 'badge-green' : 'badge-red'}">${a.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
            <td class="text-slate-500 text-xs">${new Date(a.created_at).toLocaleDateString('id-ID')}</td>
            <td>
                <div class="flex items-center gap-1.5 flex-wrap">
                    ${a.role === 'super-admin-pemilik' || a.id === loggedInUser.id ? `
                        <span class="text-slate-500 text-xs font-bold">Utama (Permanen)</span>
                    ` : `
                        <button onclick="toggleAdmin(${a.id}, ${a.is_active})" class="btn-outline" style="font-size:0.7rem;padding:5px 10px;">${a.is_active ? 'Nonaktifkan' : 'Aktifkan'}</button>
                        <button onclick="resetPasswordModal(${a.id}, '${escapeHtml(a.nama)}', '${escapeHtml(a.email)}')" class="btn-outline text-amber-400 hover:text-amber-300 border-amber-500/40 hover:border-amber-500" style="font-size:0.7rem;padding:5px 10px;" title="Reset Password Awal">Reset Password</button>
                    `}
                </div>
            </td>
        </tr>
    `).join('');
}

function filterTable() {
    const q = document.getElementById('searchAdmin').value.toLowerCase();
    renderTable(allAdmins.filter(a => (a.nama || '').toLowerCase().includes(q) || (a.email || '').toLowerCase().includes(q)));
}

async function createAdmin() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const nama = document.getElementById('newAdminNama').value.trim();
    const email = document.getElementById('newAdminEmail').value.trim();
    const role = document.getElementById('newAdminRole').value;
    const password = document.getElementById('newAdminPassword').value.trim();

    if (!nama || !email || !role) {
        Swal.fire({
            title: 'Lengkapi Data',
            text: 'Nama lengkap, akun email Google, dan role wajib diisi.',
            icon: 'warning',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#F97316'
        });
        return;
    }

    const payload = { nama, email, role };
    if (password) {
        payload.password = password;
    }

    const btnSubmit = document.getElementById('btnSubmitCreateAdmin');
    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerText = 'Membuat Akun...';
    }

    try {
        const r = await fetch('/api/admin/accounts', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const d = await r.json();

        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerText = '+ Buat Akun Admin';
        }

        if (r.ok) {
            const tempPwd = d.temporary_password || password;
            await Swal.fire({
                title: 'Akun Admin Berhasil Dibuat!',
                html: `
                    <div style="text-align: left; font-size: 0.85rem; line-height: 1.6; color: #CBD5E1;">
                        <p class="mb-3">Akun internal staf berhasil didaftarkan. Harap salin dan berikan kredensial awal berikut kepada Admin terkait:</p>
                        <div style="background: #0F172A; padding: 14px; border-radius: 10px; border: 1px solid #334155; font-family: monospace;">
                            <div class="flex justify-between items-center py-1 border-b border-slate-800">
                                <div><b class="text-slate-400">Nama:</b> <span class="text-white font-sans">${escapeHtml(d.user?.nama || nama)}</span></div>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-slate-800 mt-1">
                                <div><b class="text-slate-400">Role:</b> <span class="text-blue-400 font-sans font-bold">${roleLabel(d.user?.role || role)}</span></div>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-slate-800 mt-1">
                                <div><b class="text-slate-400">Email:</b> <span class="text-orange-400 font-bold">${escapeHtml(email)}</span></div>
                            </div>
                            <div class="flex justify-between items-center py-1 mt-1">
                                <div><b class="text-slate-400">Password Awal:</b> <span style="background: #EA580C; color: white; padding: 3px 8px; border-radius: 4px; font-weight: bold; letter-spacing: 0.05em;">${escapeHtml(tempPwd)}</span></div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" id="btnCopyCredsModal" onclick="copyCreds('${escapeHtml(email)}', '${escapeHtml(tempPwd)}')" style="width: 100%; background: #334155; color: #F8FAFC; border: 1px solid #475569; padding: 9px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Salin Kredensial (Email & Password)</span>
                            </button>
                        </div>
                        <p class="mt-3 text-xs text-amber-400 bg-amber-500/10 p-2.5 rounded border border-amber-500/20">
                            <b>Penting:</b> Pada saat pertama kali login di <code>/login-admin</code>, admin wajib mengisi kode OTP, menentukan <b>Username</b> resmi, serta memperbarui <b>Password</b> awal mereka.
                        </p>
                    </div>
                `,
                icon: 'success',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316',
                confirmButtonText: 'Selesai & Tutup',
                allowOutsideClick: false,
                allowEscapeKey: false
            });

            document.getElementById('newAdminNama').value = '';
            document.getElementById('newAdminEmail').value = '';
            document.getElementById('newAdminRole').value = '';
            generateNewPassword();
            loadAdmins();
            loadAuditLogs();
        } else {
            let errMsg = d.message || 'Terjadi kesalahan saat membuat akun.';
            if (d.errors) {
                errMsg = Object.values(d.errors).flat().join('<br>');
            }
            Swal.fire({
                title: 'Gagal Membuat Akun',
                html: errMsg,
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    } catch(e) {
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerText = '+ Buat Akun Admin';
        }
        Swal.fire({
            title: 'Error Jaringan',
            text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
            icon: 'error',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#F97316'
        });
    }
}

async function resetPasswordModal(id, nama, email) {
    const generatedPwd = generateRandomPassword(10);
    const { value: formValues } = await Swal.fire({
        title: 'Reset Password Admin',
        html: `
            <div style="text-align: left; font-size: 0.85rem; color: #CBD5E1;">
                <p class="mb-3">Reset kata sandi untuk akun <b>${escapeHtml(nama)}</b> (<span class="text-orange-400">${escapeHtml(email)}</span>).</p>
                <label class="text-[11px] font-bold text-slate-400 block mb-1">Kata Sandi Baru:</label>
                <div style="position: relative;">
                    <input id="swalResetPasswordInput" class="swal2-input font-mono text-orange-300 font-bold" style="width: 100%; margin: 0; background: #0F172A; border: 1px solid #334155; color: #FDBA74; font-size: 0.9rem;" value="${generatedPwd}">
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Anda dapat menggunakan password acak di atas atau mengetikkan password baru (min. 6 karakter). Staf wajib mengganti password saat login berikutnya.</p>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Reset Password Sekarang',
        cancelButtonText: 'Batal',
        background: '#1E293B',
        color: '#E2E8F0',
        confirmButtonColor: '#F97316',
        cancelButtonColor: '#475569',
        preConfirm: () => {
            const val = document.getElementById('swalResetPasswordInput').value.trim();
            if (!val || val.length < 6) {
                Swal.showValidationMessage('Password minimal 6 karakter.');
                return false;
            }
            return val;
        }
    });

    if (!formValues) return;

    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    try {
        const r = await fetch(`/api/admin/accounts/${id}/reset-password`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ password: formValues })
        });
        const d = await r.json();
        if (r.ok) {
            const newPwd = d.temporary_password || formValues;
            await Swal.fire({
                title: 'Password Berhasil Direset!',
                html: `
                    <div style="text-align: left; font-size: 0.85rem; color: #CBD5E1;">
                        <p class="mb-3">Kata sandi akun <b>${escapeHtml(nama)}</b> berhasil diperbarui. Berikan kredensial berikut kepada admin terkait:</p>
                        <div style="background: #0F172A; padding: 12px; border-radius: 8px; border: 1px solid #334155; font-family: monospace;">
                            <div><b>Email:</b> <span class="text-orange-400 font-bold">${escapeHtml(email)}</span></div>
                            <div class="mt-1"><b>Password Baru:</b> <span style="background: #EA580C; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold;">${escapeHtml(newPwd)}</span></div>
                        </div>
                        <div class="mt-3">
                            <button type="button" id="btnCopyResetCreds" onclick="copyResetCreds('${escapeHtml(email)}', '${escapeHtml(newPwd)}')" style="width: 100%; background: #334155; color: #F8FAFC; border: 1px solid #475569; padding: 8px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Salin Kredensial Baru</span>
                            </button>
                        </div>
                    </div>
                `,
                icon: 'success',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316',
                confirmButtonText: 'Selesai',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            loadAuditLogs();
        } else {
            Swal.fire({
                title: 'Gagal Reset Password',
                text: d.message || 'Terjadi kesalahan saat mereset password.',
                icon: 'error',
                background: '#1E293B',
                color: '#E2E8F0',
                confirmButtonColor: '#F97316'
            });
        }
    } catch(e) {
        Swal.fire({
            title: 'Error Jaringan',
            text: 'Tidak dapat terhubung ke server.',
            icon: 'error',
            background: '#1E293B',
            color: '#E2E8F0',
            confirmButtonColor: '#F97316'
        });
    }
}

async function toggleAdmin(id, currentState) {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    const action = currentState ? 'nonaktifkan' : 'aktifkan';
    const confirm = await Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} akun ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        background: '#1E293B',
        color: '#E2E8F0',
        confirmButtonColor: '#F97316',
        cancelButtonColor: '#475569'
    });
    if (!confirm.isConfirmed) return;
    try {
        const r = await fetch(`/api/admin/accounts/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        if (r.ok) {
            loadAdmins();
            loadAuditLogs();
        } else {
            const d = await r.json();
            Swal.fire({ title: 'Gagal', text: d.message || 'Terjadi kesalahan.', icon: 'error', background: '#1E293B', color: '#E2E8F0', confirmButtonColor: '#F97316' });
        }
    } catch(e) {}
}

async function loadAuditLogs() {
    const token = localStorage.getItem('admin_token') || localStorage.getItem('token');
    try {
        const r = await fetch('/api/admin/audit-logs', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        const logs = await r.json();
        const container = document.getElementById('auditLogList');
        if (!Array.isArray(logs) || !logs.length) {
            container.innerHTML = '<p class="text-slate-500 text-sm text-center py-4">Belum ada log.</p>';
            return;
        }
        container.innerHTML = logs.slice(0, 20).map(l => `
            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-900/40 border border-slate-800/50">
                <span class="text-slate-500 text-xs mt-0.5 whitespace-nowrap">${new Date(l.created_at).toLocaleString('id-ID')}</span>
                <span class="text-slate-300 text-xs">${escapeHtml(l.aktivitas)}</span>
                ${l.ip_address ? `<span class="ml-auto text-[10px] text-slate-600">${escapeHtml(l.ip_address)}</span>` : ''}
            </div>
        `).join('');
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    generateNewPassword();
    loadAdmins();
    loadAuditLogs();
});
</script>
@endsection
