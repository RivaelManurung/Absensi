@extends('layouts.app')

@section('title', 'Create Shift')
@section('page_title', 'Create Shift')
@section('page_description', 'Add new shift schedule settings for employees.')

@section('content')
    <form method="POST" action="{{ route('shift.store') }}" class="space-y-4 max-w-2xl">
        @csrf

        <x-form.input label="Shift Name" name="name" placeholder="Morning" :value="old('name')" required />
        <x-form.input label="Start Time" name="start_time" type="time" :value="old('start_time')" required />
        <x-form.input label="End Time" name="end_time" type="time" :value="old('end_time')" required />
        <x-form.input label="Late Tolerance Minutes" name="late_tolerance_minutes" type="number" placeholder="15" :value="old('late_tolerance_minutes')" required />

        <div class="pt-2">
            <x-button type="submit" primary label="Save Shift" />
        </div>
    </form>
@endsection
