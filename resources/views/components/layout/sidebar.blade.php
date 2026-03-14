<aside class="w-full lg:w-72 bg-white/80 backdrop-blur border-r border-slate-200 lg:min-h-screen">
    @php
        $user = auth()->user();

        $roleNames = $user
            ? \Illuminate\Support\Facades\DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $user->id)
                ->pluck('roles.name')
                ->all()
            : [];

        $canManageMaster = collect($roleNames)->intersect(['Admin', 'HR', 'Manager'])->isNotEmpty();
    @endphp

    <div class="px-6 py-5 border-b border-slate-200">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Absensi</p>
        <h1 class="text-xl font-bold text-slate-800">Workforce Hub</h1>
        @if ($user)
            <p class="text-xs text-slate-500 mt-2">{{ $user->name }} • {{ implode(', ', $roleNames) ?: 'No Role' }}</p>
        @endif
    </div>

    <nav class="p-4 space-y-1">
        <a href="{{ route('dashboard.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('dashboard.*') ? 'bg-sky-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
            Dashboard
        </a>

        <a href="{{ route('attendance.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('attendance.*') ? 'bg-sky-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
            Attendance
        </a>

        @if ($canManageMaster)
            <a href="{{ route('employee.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('employee.*') ? 'bg-sky-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                Employee
            </a>

            <a href="{{ route('shift.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('shift.*') ? 'bg-sky-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                Shift
            </a>
        @endif
    </nav>

    @if ($user)
        <div class="p-4 border-t border-slate-200 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl px-4 py-3 text-sm font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    @endif
</aside>
