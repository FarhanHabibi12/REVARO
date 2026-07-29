@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<h1 class="text-lg font-semibold text-slate-900 mb-1">Masuk ke panel</h1>
<p class="text-sm text-slate-500 mb-6">Khusus admin verifikator, admin resolusi, dan super admin.</p>

@if ($errors->any())
    <div class="bg-red-50 border border-red-100 text-red-700 text-xs rounded-lg p-3 mb-4">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (session('status'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs rounded-lg p-3 mb-4">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login.attempt') }}" class="flex flex-col gap-4">
    @csrf

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
               placeholder="nama@revaro.id">
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Kata sandi</label>
        <input type="password" name="password" required
               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
               placeholder="••••••••">
    </div>

    <label class="flex items-center gap-2 text-xs text-slate-600">
        <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
        Ingat saya
    </label>

    <button type="submit"
            class="w-full bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg py-2.5 transition">
        Masuk
    </button>
</form>

<p class="text-center text-xs text-slate-500 mt-5">
    Belum punya akun?
    <a href="{{ route('register') }}" class="text-teal-600 font-medium hover:underline">Daftar</a>
</p>
@endsection
