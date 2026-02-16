@extends('app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'sans-serif'],
                },
                colors: {
                    brand: '#2563eb',
                    bgMain: '#f8fafc',
                }
            }
        }
    }
</script>

<div class="p-6 lg:p-10 bg-bgMain min-h-screen font-sans text-slate-900">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Hi, Rian</h1>
            <p class="text-slate-400 text-sm font-medium mt-1">Welcome back to Mendunia Monitoring!</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4 text-slate-400"></i> Download
            </button>
            <button class="flex items-center gap-2 px-4 py-2 bg-brand text-white rounded-lg text-sm font-semibold shadow-md shadow-blue-200 hover:bg-blue-700 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Task
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class=" p-5 rounded-2xl shadow-lg shadow-blue-100 flex flex-col justify-between"   style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-bottom: 3px solid rgba(255,255,255,0.1);">
            <div class="flex justify-between items-start">
                <h3 class="text-blue-100 text-xs font-semibold uppercase tracking-wider">Progress Proyek</h3>
                <div class="bg-blue-700/30 p-1.5 rounded-lg">
                    <i data-lucide="trello" class="text-white w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-white tracking-tight">78.4%</p>
                <div class="w-full bg-white/20 h-1 rounded-full mt-3 overflow-hidden">
                    <div class="bg-white h-full w-[78%]"></div>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <h3 class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Task Berjalan</h3>
                <div class="bg-orange-50 p-1.5 rounded-lg border border-orange-100">
                    <i data-lucide="clock" class="text-orange-500 w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-slate-900 tracking-tight">24.00</p>
                <p class="text-[11px] text-orange-500 font-bold mt-2">8 Task Deadline</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <h3 class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Lead Conversation</h3>
                <div class="bg-blue-50 p-1.5 rounded-lg border border-blue-100">
                    <i data-lucide="users" class="text-brand w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-slate-900 tracking-tight">78.93%</p>
                <div class="flex -space-x-1 mt-2">
                    <div class="w-5 h-5 rounded-full bg-slate-200 border border-white"></div>
                    <div class="w-5 h-5 rounded-full bg-slate-300 border border-white"></div>
                    <div class="w-5 h-5 rounded-full bg-brand border border-white flex items-center justify-center text-[8px] text-white">+5</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <h3 class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Goal this Month</h3>
                <div class="bg-emerald-50 p-1.5 rounded-lg border border-emerald-100">
                    <i data-lucide="calendar" class="text-emerald-500 w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-slate-900 tracking-tight">$32.200</p>
                <p class="text-[10px] text-emerald-600 font-bold mt-2 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> 88% more earnings
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-4">
                <div class="flex items-center gap-2 px-2">
                    <h4 class="font-bold text-slate-800 text-sm">To Do</h4>
                    <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-md">5</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:border-brand/40 transition-all cursor-pointer group">
                    <div class="flex justify-between mb-2">
                        <span class="text-[9px] font-bold px-2 py-0.5 bg-blue-50 text-brand rounded uppercase border border-blue-100">UI Design</span>
                        <i data-lucide="more-horizontal" class="w-4 h-4 text-slate-300"></i>
                    </div>
                    <h5 class="text-xs font-bold text-slate-800 leading-snug">Re-design Dashboard Mendunia</h5>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                        <span class="text-[10px] text-slate-400 font-medium">Sept 20, 2026</span>
                        <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-2 px-2">
                    <h4 class="font-bold text-slate-800 text-sm">In Progress</h4>
                    <span class="bg-blue-100 text-brand text-[10px] font-bold px-2 py-0.5 rounded-md">2</span>
                </div>
                <div class="bg-white p-4 rounded-xl border-l-4 border-l-brand border-y border-r border-slate-100 shadow-sm">
                    <span class="text-[9px] font-bold px-2 py-0.5 bg-orange-50 text-orange-600 rounded uppercase border border-orange-100 mb-2 inline-block">System</span>
                    <h5 class="text-xs font-bold text-slate-800 leading-snug">Geofencing Cabang Cianjur</h5>
                    <div class="w-full bg-slate-100 h-1 rounded-full mt-3">
                        <div class="bg-brand h-full w-[45%]"></div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-2 px-2">
                    <h4 class="font-bold text-slate-800 text-sm">Completed</h4>
                    <span class="bg-emerald-100 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-md">18</span>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm opacity-80">
                    <div class="flex items-center gap-2 text-emerald-500 mb-2">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span class="text-[9px] font-bold uppercase">Verified</span>
                    </div>
                    <h5 class="text-xs font-bold text-slate-400 line-through">Integrasi Captcha Login</h5>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm h-fit">
            <div class="flex items-center justify-between mb-6">
                <h4 class="font-bold text-slate-800 text-sm">Recent Activity</h4>
                <i data-lucide="more-vertical" class="w-4 h-4 text-slate-300"></i>
            </div>
            <div class="space-y-5">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex-shrink-0 border border-slate-200"></div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Uchiha Itachi</p>
                        <p class="text-[10px] text-slate-400 mt-0.5 italic">Update status to Done</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex-shrink-0 border border-slate-200"></div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Haruno Sakura</p>
                        <p class="text-[10px] text-slate-400 mt-0.5 italic">Added new task</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endsection