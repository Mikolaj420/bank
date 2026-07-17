<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

/* ----------------------------- Uwierzytelnianie ----------------------------- */

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/* --------------------------- Obszar zalogowany ------------------------------ */

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ------------------------------ Klient -------------------------------- */
    Route::middleware('role:'.\App\Models\Role::CLIENT)->group(function () {
        Route::get('account', [AccountController::class, 'show'])->name('account.show');

        Route::get('transfers', [TransferController::class, 'index'])->name('transfers.index');
        Route::get('transfers/create', [TransferController::class, 'create'])->name('transfers.create');
        Route::post('transfers', [TransferController::class, 'store'])->name('transfers.store');
        Route::get('transfers/export', [TransferController::class, 'export'])->name('transfers.export');
    });

    /* ---------------------- Zgłoszenia (support) -------------------------- */
    // Widok listy/szczegółów dostępny dla klienta (własne) i personelu (wszystkie).
    Route::get('tickets', [SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [SupportTicketController::class, 'create'])
        ->middleware('role:'.\App\Models\Role::CLIENT)->name('tickets.create');
    Route::post('tickets', [SupportTicketController::class, 'store'])
        ->middleware('role:'.\App\Models\Role::CLIENT)->name('tickets.store');
    Route::get('tickets/{ticket}', [SupportTicketController::class, 'show'])->name('tickets.show');
    Route::post('tickets/{ticket}/reply', [SupportTicketController::class, 'reply'])
        ->middleware('role:'.\App\Models\Role::EMPLOYEE)->name('tickets.reply');

    /* ---------------------------- Pracownik ------------------------------- */
    Route::middleware('role:'.\App\Models\Role::EMPLOYEE)->group(function () {
        Route::get('employee/accounts', [EmployeeController::class, 'accounts'])->name('employee.accounts');
        Route::get('employee/accounts/{account}', [EmployeeController::class, 'showAccount'])->name('employee.accounts.show');
    });

    /* ---------------------------- Kierownik ------------------------------- */
    Route::middleware('role:'.\App\Models\Role::MANAGER)->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{transaction}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{transaction}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    /* --------------------------- Administrator ---------------------------- */
    Route::middleware('role:'.\App\Models\Role::ADMIN)->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
    });
});
