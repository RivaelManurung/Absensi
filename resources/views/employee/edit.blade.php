@extends('layouts.app')

@section('title', 'Edit Employee')
@section('page_title', 'Edit Employee')
@section('page_description', 'Update employee profile details and status.')

@section('content')
    <form method="POST" action="{{ route('employee.update', $employee->id) }}" class="space-y-4 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="space-y-1">
            <x-select label="User" name="user_id" placeholder="Select user">
                @foreach ($users as $user)
                    <x-select.option :label="$user->name" :value="$user->id" :selected="old('user_id', $employee->user_id) === $user->id" />
                @endforeach
            </x-select>
            @error('user_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-form.input label="Employee Code" name="employee_code" :value="old('employee_code', $employee->employee_code)" required />

        <div class="space-y-1">
            <x-select label="Department" name="department_id" placeholder="Select department">
                @foreach ($departments as $department)
                    <x-select.option :label="$department->name" :value="$department->id" :selected="old('department_id', $employee->department_id) === $department->id" />
                @endforeach
            </x-select>
            @error('department_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1">
            <x-select label="Position" name="position_id" placeholder="Select position">
                @foreach ($positions as $position)
                    <x-select.option :label="$position->name" :value="$position->id" :selected="old('position_id', $employee->position_id) === $position->id" />
                @endforeach
            </x-select>
            @error('position_id')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-form.input label="Phone" name="phone" :value="old('phone', $employee->phone)" />
        <x-form.input label="Hire Date" name="hire_date" type="date" :value="old('hire_date', $employee->hire_date)" />
        <x-form.input label="Status" name="status" :value="old('status', $employee->status)" required />

        <div class="pt-2 flex gap-2">
            <x-button type="submit" primary label="Update Employee" />
            <x-button flat href="{{ route('employee.index') }}" label="Cancel" />
        </div>
    </form>
@endsection
