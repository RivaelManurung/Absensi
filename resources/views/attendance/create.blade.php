@extends('layouts.app')

@section('title', 'Create Attendance')
@section('page_title', 'Create Attendance')
@section('page_description', 'Record employee check-in and check-out information.')

@section('content')
    <form method="POST" action="{{ route('attendance.store') }}" class="space-y-4 max-w-2xl">
        @csrf

        <div class="space-y-1">
            <x-select label="Employee" name="employee_id" placeholder="Select employee">
                @foreach ($employees as $employee)
                    <x-select.option :label="$employee->employee_code" :value="$employee->id" />
                @endforeach
            </x-select>
            @error('employee_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-form.input label="Attendance Date" name="attendance_date" type="date" :value="old('attendance_date')" required />
        <x-form.input label="Check In Time" name="check_in_time" type="datetime-local" :value="old('check_in_time')" />
        <x-form.input label="Check Out Time" name="check_out_time" type="datetime-local" :value="old('check_out_time')" />
        <x-form.input label="Status" name="status" placeholder="present / late / absent" :value="old('status')" required />

        <div class="pt-2">
            <x-button type="submit" primary label="Save Attendance" />
        </div>
    </form>
@endsection
