@extends('layouts.app')

@section('title', 'Shift')
@section('page_title', 'Shift List')
@section('page_description', 'Configure working shifts and tolerance rules.')

@section('content')
    <div class="mb-5">
        <x-button href="{{ route('shift.create') }}" primary label="Create Shift" />
    </div>

    <x-table.table
        :headers="['Shift Name', 'Start', 'End', 'Late Tolerance (min)']"
        :rows="$rows ?? []"
    />
@endsection
