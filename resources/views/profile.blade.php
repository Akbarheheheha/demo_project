@extends('layouts.app')

@section('title', 'Profil Saya')
@section('active_page', 'profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Profile Header / Banner Card -->
    <div class="relative bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-950 text-white rounded-3xl border border-slate-800 shadow-xl overflow-hidden p-6 md:p-8">
        <!-- Background glows -->
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-indigo-500/20 filter blur-[80px] pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-violet-500/20 filter blur-[80px] pointer-events-none"></div>
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <!-- Profile Avatar Frame -->
            <div class="relative">
                <img class="h-28 w-28 rounded-2xl object-cover border-2 border-indigo-400/40 shadow-xl" 
                     src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150&h=150" 
                     alt="Profile Picture">
                <span class="absolute bottom-2 right-2 flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-slate-950"></span>
                </span>
            </div>

            <!-- Profile Info -->
            <div class="text-center md:text-left space-y-1.5 flex-1">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <h2 class="text-2xl font-black tracking-wide">{{ auth()->user()->name }}</h2>
                    <div class="flex justify-center">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/25 border border-indigo-400/40 text-indigo-200">
                            {{ auth()->user()->roles->pluck('name')->implode(', ') }}
                        </span> 
                    </div>
                </div>
                <p class=" text-xs flex items-start justify-start md:justify-start gap-1.5">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                    {{ auth()->user()->email }}
                </p>
                <p class=" text-[10px] flex items-start justify-start md:justify-start gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 "></i>
                    Anggota Sejak: {{ auth()->user()->created_at ? auth()->user()->created_at->format('d F Y') : 'Juni 2026' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Info Detail Card (Takes 2 Columns) -->
        <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i data-lucide="user-cog" class="w-5 h-5 text-indigo-600"></i>
                    Detail Informasi Akun
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Informasi pribadi dan setelan keamanan akun Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">Nama Lengkap</span>
                    <p class="font-bold text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ auth()->user()->name }}</p>
                </div>
                
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">Alamat Email</span>
                    <p class="font-bold text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ auth()->user()->email }}</p>
                </div>
                
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">Peran (Role)</span>
                    <p class="font-bold text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 capitalize">
                        {{ auth()->user()->roles->pluck('name')->implode(', ') }}
                    </p>
                </div>
                
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">Status Akun</span>
                    <div class="flex items-center gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-bold text-emerald-700">Aktif & Sinkron</span>
                    </div>
                </div>
            </div>

            <!-- Toast simulation info alert -->
            <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl flex items-start gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl mt-0.5">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-indigo-850">Keamanan Tingkat Tinggi</h4>
                    <p class="text-[10px] text-indigo-600 leading-relaxed mt-0.5">Akun Anda terhubung dengan enkripsi SSL 256-bit dan autentikasi berbasis RBAC (Role-Based Access Control) yang aman.</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Statistics Card (Takes 1 Column) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
            <div>
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-indigo-600"></i>
                        Statistik Cepat
                    </h3>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-medium">Device Aktif</span>
                        <span class="font-bold text-slate-800">1 Perangkat</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-medium">Sesi Login</span>
                        <span class="font-bold text-indigo-600">Aktif (2 Jam)</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2 pt-4 border-t border-slate-100">
                <button @click="$dispatch('show-toast', { message: 'Ubah password akan hadir pada rilis berikutnya!', type: 'info' })"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200/70 text-slate-700 text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="key" class="w-4 h-4"></i>
                    <span>Ubah Password</span>
                </button>
                <form id="profile-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <button @click.prevent="document.getElementById('profile-logout-form').submit()"
                        class="w-full py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Log Out</span>
                </button>
            </div>
        </div>

    </div>

</div>
@endsection
