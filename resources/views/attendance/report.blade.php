@extends('layouts.app')

@section('title', 'Attendance Report')
@section('page_title', 'Attendance Report')
@section('page_description', 'Summary analytics for attendance performance.')

@section('content')
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Present Rate</p>
            <p class="text-2xl font-bold mt-1">{{ $presentRate ?? 0 }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Late Rate</p>
            <p class="text-2xl font-bold mt-1">{{ $lateRate ?? 0 }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Absence</p>
            <p class="text-2xl font-bold mt-1">{{ $absenceRate ?? 0 }}%</p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Overtime</p>
            <p class="text-2xl font-bold mt-1">{{ $overtimeCount ?? 0 }}</p>
        </div>
    </div>

    <x-table.table
        :headers="['Department', 'Present', 'Late', 'Absent']"
        :rows="$rows ?? []"
    />
@endsection
