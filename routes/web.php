<?php

use App\Http\Controllers\ExpenseController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/analytics', function () {
    return view('platform.analytics');
})->name('analytics');

// Main Expenses route
Route::get('/Expenses', [\App\Http\Controllers\ExpenseController::class, 'expenseIndex'])->name('Expenses');

// Expense Management System Routes
Route::middleware(['auth'])->group(function () {
    // Basic Expense Routes
    Route::prefix('expenses')->group(function () {
        // Create new expense form
        Route::get('/new', [ExpenseController::class, 'create'])->name('newExpense');
        
        // Store new expense
        Route::post('/store', [ExpenseController::class, 'store'])->name('expenses.store');
        
        // Show expense details
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
        
        // Edit expense form
        Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        
        // Update expense
        Route::put('/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        
        // Delete expense
        Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        
        // Expense Workflow Routes
        Route::post('/{expense}/submit', [ExpenseController::class, 'submit'])->name('expenses.submit');
        Route::post('/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
        
        // Export expenses
        Route::get('/export/{format?}', [ExpenseController::class, 'export'])->name('expenses.export');
        
        // Filter expenses by category
        Route::get('/category/{category}', [ExpenseController::class, 'filterByCategory'])->name('expenses.category');
        
        // Audit logs
        Route::get('/audit-logs', [ExpenseController::class, 'auditLogs'])->name('expenses.audit-logs');
    });
    
    // Expense Categories Management
    Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class);
    
    // Expense Policies Management
    Route::resource('expense-policies', \App\Http\Controllers\ExpensePolicyController::class);
});

// Legacy routes for backward compatibility
Route::prefix('Expenses')->group(function () {
    Route::get('/new', function () {
        $categories = \App\Models\ExpenseCategory::active()->get();
        return view('Financials.newExpense', compact('categories'));
    })->name('newExpense');
    
    Route::get('/details/{expense?}', function ($expense = null) {
        if ($expense) {
            $expense = \App\Models\Expense::with(['category', 'user', 'approver'])->findOrFail($expense);
        }
        return view('Financials.ExpenseDetails', compact('expense'));
    })->name('expense.details');
});



//expense-categories

Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class);

// User Settings Routes
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
