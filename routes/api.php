<?php

use App\Http\Controllers\Api\AttendanceLogsController;
use App\Http\Controllers\Api\AttendancesController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivityLogsController;
use App\Http\Controllers\Api\BarcodesController;
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

Route::name('api.')->group(function (): void {
	Route::prefix('auth')->name('auth.')->middleware('web')->group(function (): void {
		Route::post('login', [AuthController::class, 'login'])->name('login');
		Route::middleware('auth')->group(function (): void {
			Route::post('logout', [AuthController::class, 'logout'])->name('logout');
			Route::get('me', [AuthController::class, 'me'])->name('me');
		});
	});

	Route::middleware(['web', 'auth'])->group(function (): void {
		Route::apiResource('users', UsersController::class);
		Route::post('users/{id}/reset-password', [UsersController::class, 'resetPassword'])->name('users.reset-password');
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
		Route::apiResource('barcodes', BarcodesController::class)->except(['update']);
		Route::post('barcodes/{id}/regenerate', [BarcodesController::class, 'regenerate'])->name('barcodes.regenerate');
		Route::post('barcodes/{id}/deactivate', [BarcodesController::class, 'deactivate'])->name('barcodes.deactivate');
		Route::apiResource('activity-logs', ActivityLogsController::class)->only(['index', 'show']);
		Route::apiResource('leaves', LeavesController::class);
		Route::apiResource('leave-types', LeaveTypesController::class);
	});
});
