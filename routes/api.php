<?php

use App\Http\Controllers\Api\AttendanceLogsController;
use App\Http\Controllers\Api\AttendancesController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\EmployeeShiftsController;
use App\Http\Controllers\Api\LeavesController;
use App\Http\Controllers\Api\LeaveTypesController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PositionsController;
use App\Http\Controllers\Api\RolePermissionsController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\ShiftsController;
use App\Http\Controllers\Api\UserRolesController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UsersController::class);
Route::apiResource('roles', RolesController::class);
Route::apiResource('permissions', PermissionsController::class);
Route::apiResource('role-permissions', RolePermissionsController::class);
Route::apiResource('user-roles', UserRolesController::class);
Route::apiResource('employees', EmployeesController::class);
Route::apiResource('departments', DepartmentsController::class);
Route::apiResource('positions', PositionsController::class);
Route::apiResource('shifts', ShiftsController::class);
Route::apiResource('employee-shifts', EmployeeShiftsController::class);
Route::apiResource('attendances', AttendancesController::class);
Route::apiResource('attendance-logs', AttendanceLogsController::class);
Route::apiResource('leaves', LeavesController::class);
Route::apiResource('leave-types', LeaveTypesController::class);
