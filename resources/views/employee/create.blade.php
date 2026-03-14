@extends('layouts.app')

@section('title', 'Create Employee')
@section('page_title', 'Create Employee')
@section('page_description', 'Add a new employee profile and organizational assignment.')

@section('content')
    <form method="POST" action="{{ route('employee.store') }}" class="space-y-4 max-w-2xl">
        @csrf

        <div class="space-y-1">
            <x-select label="User" name="user_id" placeholder="Select user">
                @foreach ($users as $user)
                    <x-select.option :label="$user->name" :value="$user->id" />
                @endforeach
            </x-select>
            @error('user_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-form.input label="Employee Code" name="employee_code" placeholder="EMP-0001" :value="old('employee_code')" required />

        <div class="space-y-1">
            <x-select label="Department" name="department_id" placeholder="Select department">
                @foreach ($departments as $department)
                    <x-select.option :label="$department->name" :value="$department->id" />
                @endforeach
            </x-select>
            @error('department_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <x-select label="Position" name="position_id" placeholder="Select position">
                @foreach ($positions as $position)
                    <x-select.option :label="$position->name" :value="$position->id" />
                @endforeach
            </x-select>
            @error('position_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-form.input label="Phone" name="phone" placeholder="0812xxxx" :value="old('phone')" />
        <x-form.input label="Hire Date" name="hire_date" type="date" :value="old('hire_date')" />
        <x-form.input label="Status" name="status" placeholder="active / inactive" :value="old('status')" required />

        <div class="pt-2">
            <x-button type="submit" primary label="Save Employee" />
        </div>
    </form>
@endsection
