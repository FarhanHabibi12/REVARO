@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
<h1 class="text-lg font-semibold text-slate-900 mb-1">Buat akun</h1>
<p class="text-sm text-slate-500 mb-6">Untuk staf internal REVARO saja.</p>

@if ($errors->any())
    <div class="bg-red-50 border border-red-100 text-red-700 text-xs rounded-lg p-3 mb-4">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register.attempt') }}" class="flex flex-col gap-4">
    @csrf

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Nama lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
               placeholder="Nama kamu">
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
               placeholder="nama@revaro.id">
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Peran</label>
        <select name="role" required
                class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            <option value="admin_verifikator">Admin verifikator</option>
            <option value="admin_resolusi">Admin resolusi</option>
            <option value="super_admin">Super admin</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Kata sandi</label>
        <input type="password" name="password" required
               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
               placeholder="Minimal 8 karakter">
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Konfirmasi kata sandi</label>
        <input type="password" name="password_confirmation" required
               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
               placeholder="Ulangi kata sandi">
    </div>

    <button type="submit"
            class="w-full bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg py-2.5 transition">
        Daftar
    </button>
</form>

<p class="text-center text-xs text-slate-500 mt-5">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="text-teal-600 font-medium hover:underline">Masuk</a>
</p>
@endsection
