<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(): View
    {
        $rows = DB::table('shifts')
            ->orderBy('name')
            ->get(['name', 'start_time', 'end_time', 'late_tolerance_minutes'])
            ->map(fn ($row) => [
                $row->name,
                substr((string) $row->start_time, 0, 5),
                substr((string) $row->end_time, 0, 5),
                (string) $row->late_tolerance_minutes,
            ])
            ->all();

        return view('shift.index', ['rows' => $rows]);
    }

    public function create(): View
    {
        return view('shift.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0'],
        ]);

        DB::table('shifts')->insert([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'late_tolerance_minutes' => $data['late_tolerance_minutes'],
            'created_at' => now(),
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift created successfully.');
    }
}
