@extends('layouts.app')

@section('title', 'Attendance')
@section('page_title', 'Attendance List')
@section('page_description', 'Monitor all attendance records and statuses.')

@section('content')
    <div class="flex flex-wrap gap-3 mb-5">
        <x-button href="{{ route('attendance.create') }}" primary label="Create Attendance" />
        <x-button href="{{ route('attendance.report') }}" flat label="View Report" />
    </div>

    <x-table.table
        :headers="['Date', 'Employee', 'Check In', 'Check Out', 'Status']"
        :rows="$rows ?? []"
    />
@endsection
