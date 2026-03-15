<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AbsensiOperationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $departmentIds = $this->seedDepartments();
            $positionIds = $this->seedPositions();
            $shiftIds = $this->seedShifts();
            $leaveTypeIds = $this->seedLeaveTypes();

            [$userIds, $employeeIds] = $this->seedUsersAndEmployees($departmentIds, $positionIds);

            $this->seedUserRoles($userIds);
            $this->seedEmployeeShifts($employeeIds, $shiftIds);
            $attendanceIds = $this->seedAttendances($employeeIds);
            $this->seedAttendanceLogs($attendanceIds);
            $this->seedLeaves($employeeIds, $leaveTypeIds, $userIds);
        });
    }

    /**
     * @return array<string, string>
     */
    private function seedDepartments(): array
    {
        $departments = [
            'Human Resources',
            'Engineering',
            'Operations',
            'Finance',
            'Sales & Marketing',
        ];

        $ids = [];

        foreach ($departments as $name) {
            $ids[$name] = $this->upsertByName('departments', $name, []);
        }

        return $ids;
    }

    /**
     * @return array<string, string>
     */
    private function seedPositions(): array
    {
        $positions = [
            'HR Officer',
            'Software Engineer',
            'QA Engineer',
            'Supervisor',
            'Manager',
            'Finance Staff',
        ];

        $ids = [];

        foreach ($positions as $name) {
            $ids[$name] = $this->upsertByName('positions', $name, []);
        }

        return $ids;
    }

    /**
     * @return array<string, string>
     */
    private function seedShifts(): array
    {
        $shifts = [
            [
                'name' => 'Regular Morning',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'late_tolerance_minutes' => 15,
            ],
            [
                'name' => 'Flexi Office',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'late_tolerance_minutes' => 15,
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'late_tolerance_minutes' => 10,
            ],
        ];

        $ids = [];

        foreach ($shifts as $shift) {
            $ids[$shift['name']] = $this->upsertByName('shifts', $shift['name'], [
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'late_tolerance_minutes' => $shift['late_tolerance_minutes'],
            ]);
        }

        return $ids;
    }

    /**
     * @return array<string, string>
     */
    private function seedLeaveTypes(): array
    {
        $leaveTypes = [
            ['name' => 'Annual Leave', 'max_days' => 12],
            ['name' => 'Sick Leave', 'max_days' => 14],
            ['name' => 'Maternity Leave', 'max_days' => 90],
            ['name' => 'Personal Leave', 'max_days' => 3],
        ];

        $ids = [];

        foreach ($leaveTypes as $leaveType) {
            $ids[$leaveType['name']] = $this->upsertByName('leave_types', $leaveType['name'], [
                'max_days' => $leaveType['max_days'],
            ]);
        }

        return $ids;
    }

    /**
     * @param array<string, string> $departmentIds
     * @param array<string, string> $positionIds
     * @return array{0: array<string, string>, 1: array<string, string>}
     */
    private function seedUsersAndEmployees(array $departmentIds, array $positionIds): array
    {
        $users = [
            [
                'key' => 'admin',
                'name' => 'Admin System',
                'email' => 'admin@absensi.local',
                'is_active' => true,
                'employee' => null,
            ],
            [
                'key' => 'hr',
                'name' => 'Dina HR',
                'email' => 'hr@absensi.local',
                'is_active' => true,
                'employee' => [
                    'employee_code' => 'EMP0001',
                    'department' => 'Human Resources',
                    'position' => 'HR Officer',
                    'phone' => '081234567801',
                    'hire_date' => '2022-01-10',
                    'status' => 'active',
                ],
            ],
            [
                'key' => 'manager',
                'name' => 'Ravi Manager',
                'email' => 'manager@absensi.local',
                'is_active' => true,
                'employee' => [
                    'employee_code' => 'EMP0002',
                    'department' => 'Engineering',
                    'position' => 'Manager',
                    'phone' => '081234567802',
                    'hire_date' => '2021-06-18',
                    'status' => 'active',
                ],
            ],
            [
                'key' => 'supervisor',
                'name' => 'Bagas Supervisor',
                'email' => 'supervisor@absensi.local',
                'is_active' => true,
                'employee' => [
                    'employee_code' => 'EMP0003',
                    'department' => 'Operations',
                    'position' => 'Supervisor',
                    'phone' => '081234567803',
                    'hire_date' => '2021-11-03',
                    'status' => 'active',
                ],
            ],
            [
                'key' => 'engineer_1',
                'name' => 'Rina Engineer',
                'email' => 'rina@absensi.local',
                'is_active' => true,
                'employee' => [
                    'employee_code' => 'EMP0004',
                    'department' => 'Engineering',
                    'position' => 'Software Engineer',
                    'phone' => '081234567804',
                    'hire_date' => '2023-02-14',
                    'status' => 'active',
                ],
            ],
            [
                'key' => 'engineer_2',
                'name' => 'Yoga QA',
                'email' => 'yoga@absensi.local',
                'is_active' => true,
                'employee' => [
                    'employee_code' => 'EMP0005',
                    'department' => 'Engineering',
                    'position' => 'QA Engineer',
                    'phone' => '081234567805',
                    'hire_date' => '2023-08-01',
                    'status' => 'active',
                ],
            ],
            [
                'key' => 'finance',
                'name' => 'Nadia Finance',
                'email' => 'nadia@absensi.local',
                'is_active' => true,
                'employee' => [
                    'employee_code' => 'EMP0006',
                    'department' => 'Finance',
                    'position' => 'Finance Staff',
                    'phone' => '081234567806',
                    'hire_date' => '2022-09-22',
                    'status' => 'active',
                ],
            ],
            [
                'key' => 'inactive_employee',
                'name' => 'Former Employee',
                'email' => 'former@absensi.local',
                'is_active' => false,
                'employee' => [
                    'employee_code' => 'EMP0007',
                    'department' => 'Sales & Marketing',
                    'position' => 'Supervisor',
                    'phone' => '081234567807',
                    'hire_date' => '2020-05-12',
                    'status' => 'inactive',
                ],
            ],
        ];

        $userIds = [];
        $employeeIds = [];

        foreach ($users as $item) {
            $existingUser = DB::table('users')->where('email', $item['email'])->first();

            if ($existingUser) {
                DB::table('users')
                    ->where('id', $existingUser->id)
                    ->update([
                        'name' => $item['name'],
                        'is_active' => $item['is_active'],
                        'updated_at' => now(),
                    ]);

                $userId = $existingUser->id;
            } else {
                $userId = (string) Str::uuid();

                DB::table('users')->insert([
                    'id' => $userId,
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'password' => Hash::make('password123'),
                    'is_active' => $item['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $userIds[$item['key']] = $userId;

            if (! $item['employee']) {
                continue;
            }

            $employee = $item['employee'];
            $existingEmployee = DB::table('employees')
                ->where('employee_code', $employee['employee_code'])
                ->first();

            $payload = [
                'user_id' => $userId,
                'employee_code' => $employee['employee_code'],
                'department_id' => $departmentIds[$employee['department']],
                'position_id' => $positionIds[$employee['position']],
                'phone' => $employee['phone'],
                'hire_date' => $employee['hire_date'],
                'status' => $employee['status'],
                'updated_at' => now(),
            ];

            if ($existingEmployee) {
                DB::table('employees')
                    ->where('id', $existingEmployee->id)
                    ->update($payload);

                $employeeId = $existingEmployee->id;
            } else {
                $employeeId = (string) Str::uuid();

                DB::table('employees')->insert(array_merge($payload, [
                    'id' => $employeeId,
                    'created_at' => now(),
                ]));
            }

            $employeeIds[$item['key']] = $employeeId;
        }

        return [$userIds, $employeeIds];
    }

    /**
     * @param array<string, string> $userIds
     */
    private function seedUserRoles(array $userIds): void
    {
        $roles = DB::table('roles')->pluck('id', 'name');

        $mappings = [
            'admin' => ['Admin'],
            'hr' => ['HR'],
            'manager' => ['Manager'],
            'supervisor' => ['Supervisor'],
            'engineer_1' => ['Employee'],
            'engineer_2' => ['Employee'],
            'finance' => ['Employee'],
            'inactive_employee' => ['Employee'],
        ];

        foreach ($mappings as $userKey => $roleNames) {
            $userId = $userIds[$userKey] ?? null;

            if (! $userId) {
                continue;
            }

            foreach ($roleNames as $roleName) {
                $roleId = $roles[$roleName] ?? null;

                if (! $roleId) {
                    continue;
                }

                $exists = DB::table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->exists();

                if ($exists) {
                    // keep legacy mapping in sync, then ensure Spatie mapping exists
                } else {
                    DB::table('user_roles')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => $userId,
                        'role_id' => $roleId,
                    ]);
                }

                $spatieExists = DB::table('model_has_roles')
                    ->where('role_id', $roleId)
                    ->where('model_type', \App\Models\User::class)
                    ->where('model_id', $userId)
                    ->exists();

                if (! $spatieExists) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleId,
                        'model_type' => \App\Models\User::class,
                        'model_id' => $userId,
                    ]);
                }
            }
        }
    }

    /**
     * @param array<string, string> $employeeIds
     * @param array<string, string> $shiftIds
     */
    private function seedEmployeeShifts(array $employeeIds, array $shiftIds): void
    {
        $effectiveDate = Carbon::now()->startOfMonth()->toDateString();

        $shiftMap = [
            'hr' => 'Regular Morning',
            'manager' => 'Regular Morning',
            'supervisor' => 'Flexi Office',
            'engineer_1' => 'Flexi Office',
            'engineer_2' => 'Regular Morning',
            'finance' => 'Regular Morning',
            'inactive_employee' => 'Regular Morning',
        ];

        foreach ($shiftMap as $employeeKey => $shiftName) {
            $employeeId = $employeeIds[$employeeKey] ?? null;
            $shiftId = $shiftIds[$shiftName] ?? null;

            if (! $employeeId || ! $shiftId) {
                continue;
            }

            $exists = DB::table('employee_shifts')
                ->where('employee_id', $employeeId)
                ->where('shift_id', $shiftId)
                ->where('effective_date', $effectiveDate)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('employee_shifts')->insert([
                'id' => (string) Str::uuid(),
                'employee_id' => $employeeId,
                'shift_id' => $shiftId,
                'effective_date' => $effectiveDate,
            ]);
        }
    }

    /**
     * @param array<string, string> $employeeIds
     * @return array<string, string>
     */
    private function seedAttendances(array $employeeIds): array
    {
        $attendanceIds = [];

        $template = [
            ['dayOffset' => 0, 'check_in' => '08:03:00', 'check_out' => '17:12:00', 'status' => 'present'],
            ['dayOffset' => -1, 'check_in' => '08:16:00', 'check_out' => '17:02:00', 'status' => 'late'],
            ['dayOffset' => -2, 'check_in' => '08:01:00', 'check_out' => '17:07:00', 'status' => 'present'],
            ['dayOffset' => -3, 'check_in' => null, 'check_out' => null, 'status' => 'absent'],
            ['dayOffset' => -4, 'check_in' => '08:09:00', 'check_out' => '17:20:00', 'status' => 'present'],
        ];

        $seedEmployeeKeys = ['hr', 'manager', 'supervisor', 'engineer_1', 'engineer_2', 'finance'];

        foreach ($seedEmployeeKeys as $employeeKey) {
            $employeeId = $employeeIds[$employeeKey] ?? null;

            if (! $employeeId) {
                continue;
            }

            foreach ($template as $item) {
                $date = Carbon::today()->addDays($item['dayOffset']);
                $attendanceDate = $date->toDateString();

                $checkInTime = $item['check_in'] ? $date->format('Y-m-d').' '.$item['check_in'] : null;
                $checkOutTime = $item['check_out'] ? $date->format('Y-m-d').' '.$item['check_out'] : null;

                $existing = DB::table('attendances')
                    ->where('employee_id', $employeeId)
                    ->where('attendance_date', $attendanceDate)
                    ->first();

                $payload = [
                    'check_in_time' => $checkInTime,
                    'check_out_time' => $checkOutTime,
                    'check_in_lat' => $checkInTime ? -6.20123456 : null,
                    'check_in_lng' => $checkInTime ? 106.81678901 : null,
                    'check_out_lat' => $checkOutTime ? -6.20133456 : null,
                    'check_out_lng' => $checkOutTime ? 106.81688901 : null,
                    'check_in_method' => $checkInTime ? 'mobile' : null,
                    'check_out_method' => $checkOutTime ? 'mobile' : null,
                    'status' => $item['status'],
                    'updated_at' => now(),
                ];

                if ($existing) {
                    DB::table('attendances')
                        ->where('id', $existing->id)
                        ->update($payload);

                    $attendanceId = $existing->id;
                } else {
                    $attendanceId = (string) Str::uuid();

                    DB::table('attendances')->insert(array_merge($payload, [
                        'id' => $attendanceId,
                        'employee_id' => $employeeId,
                        'attendance_date' => $attendanceDate,
                        'created_at' => now(),
                    ]));
                }

                $attendanceIds[$employeeKey.'|'.$attendanceDate] = $attendanceId;
            }
        }

        return $attendanceIds;
    }

    /**
     * @param array<string, string> $attendanceIds
     */
    private function seedAttendanceLogs(array $attendanceIds): void
    {
        foreach ($attendanceIds as $key => $attendanceId) {
            $attendance = DB::table('attendances')->where('id', $attendanceId)->first();

            if (! $attendance) {
                continue;
            }

            DB::table('attendance_logs')->where('attendance_id', $attendanceId)->delete();

            if ($attendance->check_in_time) {
                DB::table('attendance_logs')->insert([
                    'id' => (string) Str::uuid(),
                    'attendance_id' => $attendanceId,
                    'type' => 'check_in',
                    'latitude' => $attendance->check_in_lat,
                    'longitude' => $attendance->check_in_lng,
                    'device_info' => 'Android App - Samsung SM-A546E',
                    'created_at' => $attendance->check_in_time,
                ]);
            }

            if ($attendance->check_out_time) {
                DB::table('attendance_logs')->insert([
                    'id' => (string) Str::uuid(),
                    'attendance_id' => $attendanceId,
                    'type' => 'check_out',
                    'latitude' => $attendance->check_out_lat,
                    'longitude' => $attendance->check_out_lng,
                    'device_info' => 'Android App - Samsung SM-A546E',
                    'created_at' => $attendance->check_out_time,
                ]);
            }
        }
    }

    /**
     * @param array<string, string> $employeeIds
     * @param array<string, string> $leaveTypeIds
     * @param array<string, string> $userIds
     */
    private function seedLeaves(array $employeeIds, array $leaveTypeIds, array $userIds): void
    {
        $leaves = [
            [
                'employee_key' => 'engineer_1',
                'leave_type' => 'Annual Leave',
                'start_date' => Carbon::today()->subDays(10)->toDateString(),
                'end_date' => Carbon::today()->subDays(8)->toDateString(),
                'reason' => 'Family event outside city.',
                'status' => 'approved',
                'approved_by_key' => 'manager',
                'approved_at' => Carbon::today()->subDays(11)->format('Y-m-d 09:10:00'),
            ],
            [
                'employee_key' => 'finance',
                'leave_type' => 'Sick Leave',
                'start_date' => Carbon::today()->subDays(2)->toDateString(),
                'end_date' => Carbon::today()->toDateString(),
                'reason' => 'Medical recovery and doctor advice.',
                'status' => 'approved',
                'approved_by_key' => 'hr',
                'approved_at' => Carbon::today()->subDays(3)->format('Y-m-d 10:30:00'),
            ],
            [
                'employee_key' => 'supervisor',
                'leave_type' => 'Personal Leave',
                'start_date' => Carbon::today()->addDays(5)->toDateString(),
                'end_date' => Carbon::today()->addDays(5)->toDateString(),
                'reason' => 'Personal administrative matter.',
                'status' => 'pending',
                'approved_by_key' => null,
                'approved_at' => null,
            ],
        ];

        foreach ($leaves as $leave) {
            $employeeId = $employeeIds[$leave['employee_key']] ?? null;
            $leaveTypeId = $leaveTypeIds[$leave['leave_type']] ?? null;

            if (! $employeeId || ! $leaveTypeId) {
                continue;
            }

            $approvedBy = $leave['approved_by_key'] ? ($userIds[$leave['approved_by_key']] ?? null) : null;

            $existing = DB::table('leaves')
                ->where('employee_id', $employeeId)
                ->where('leave_type_id', $leaveTypeId)
                ->where('start_date', $leave['start_date'])
                ->where('end_date', $leave['end_date'])
                ->first();

            $payload = [
                'reason' => $leave['reason'],
                'status' => $leave['status'],
                'approved_by' => $approvedBy,
                'approved_at' => $leave['approved_at'],
            ];

            if ($existing) {
                DB::table('leaves')->where('id', $existing->id)->update($payload);

                continue;
            }

            DB::table('leaves')->insert(array_merge($payload, [
                'id' => (string) Str::uuid(),
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $leave['start_date'],
                'end_date' => $leave['end_date'],
                'created_at' => now(),
            ]));
        }
    }

    /**
     * @param array<string, mixed> $extraData
     */
    private function upsertByName(string $table, string $name, array $extraData): string
    {
        $existing = DB::table($table)->where('name', $name)->first();

        if ($existing) {
            if (! empty($extraData)) {
                DB::table($table)
                    ->where('id', $existing->id)
                    ->update($extraData);
            }

            return $existing->id;
        }

        $id = (string) Str::uuid();

        DB::table($table)->insert(array_merge([
            'id' => $id,
            'name' => $name,
            'created_at' => now(),
        ], $extraData));

        return $id;
    }
}
