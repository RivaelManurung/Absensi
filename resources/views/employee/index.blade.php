@extends('layouts.app')

@section('title', 'Employee')
@section('page_title', 'Employee List')
@section('page_description', 'Manage all employee profile and employment status.')

@section('content')
    <div class="mb-5">
        <x-button href="{{ route('employee.create') }}" primary label="Add Employee" />
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Code</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Department</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Position</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-4 py-3">{{ $row['employee_code'] }}</td>
                        <td class="px-4 py-3">{{ $row['name'] }}</td>
                        <td class="px-4 py-3">{{ $row['department_name'] }}</td>
                        <td class="px-4 py-3">{{ $row['position_name'] }}</td>
                        <td class="px-4 py-3">{{ $row['status'] }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('employee.edit', $row['id']) }}" class="text-sky-700 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-500">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
