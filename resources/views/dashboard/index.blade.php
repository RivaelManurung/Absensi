@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_description', 'Overview employee attendance, leave, and shift activity.')

@section('content')
    <div class="grid gap-4 md:grid-cols-3 mb-6">
        <div class="rounded-2xl bg-sky-600 text-white p-5">
            <p class="text-sm opacity-80">Attendance Today</p>
            <p class="text-3xl font-bold mt-2">{{ $attendanceToday ?? 0 }}</p>
        </div>

        <div class="rounded-2xl bg-emerald-600 text-white p-5">
            <p class="text-sm opacity-80">On Leave</p>
            <p class="text-3xl font-bold mt-2">{{ $onLeave ?? 0 }}</p>
        </div>

        <div class="rounded-2xl bg-amber-500 text-white p-5">
            <p class="text-sm opacity-80">Late Check-In</p>
            <p class="text-3xl font-bold mt-2">{{ $lateCount ?? 0 }}</p>
        </div>
    </div>

    <x-table.table
        :headers="['Employee Code', 'Name', 'Check In', 'Status']"
        :rows="$rows ?? []"
    />
@endsection
