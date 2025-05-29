<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the expense categories.
     */
    public function index()
    {
        $categories = ExpenseCategory::with('expenses')
            ->withCount('expenses')
            ->orderBy('name')
            ->get();
            
        // Calculate tracking data for each category
        $categoriesWithTracking = $categories->map(function ($category) {
            $trackingData = $this->createExpenseTrack($category);
            $category->tracking = $trackingData;
            return $category;
        });
        
        return view('expense-categories.index', compact('categoriesWithTracking'));
    }


   
    /**
     * Show the form for creating a new expense category.
     */
    public function create()
    {
        return view('expense-categories.create');
    }

    /**
     * Store a newly created expense category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|starts_with:#',
            'assign_amount' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);
        
        // Handle checkbox value
        $validated['active'] = $request->has('active');
        
        ExpenseCategory::create($validated);
        
        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category created successfully.');
    }

    /**
     * Display the specified expense category.
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        $categories = ExpenseCategory::with('expenses')
            ->withCount('expenses')
            ->orderBy('name')
            ->get();
            
        // Calculate tracking data for each category
        $categoriesWithTracking = $categories->map(function ($category) {
            $trackingData = $this->createExpenseTrack($category);
            $category->tracking = $trackingData;
            return $category;
        });
        // Eager load expenses to avoid N+1 query problem
        $expenseCategory->load(['expenses' => function($query) {
            $query->limit(10);
        }]);
        
        return view('expense-categories.show', compact('categoriesWithTracking', 'expenseCategory'));
    }

    /**
     * Show the form for editing the specified expense category.
     */
    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense-categories.edit', compact('expenseCategory'));
    }
    
    /**
     * Update the specified expense category in storage.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|starts_with:#',
            'assign_amount' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);
        
        // Handle checkbox value
        $validated['active'] = $request->has('active');
        
        $expenseCategory->update($validated);
        
        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    /**
     * Calculate the remaining budget for an expense category
     *
     * @param ExpenseCategory $expenseCategory
     * @return array
     */
    public function createExpenseTrack(ExpenseCategory $expenseCategory): array
    {
        // Get assigned budget amount
        $assignedAmount = $expenseCategory->assign_amount ?? 0;
    
        // Calculate total approved expenses for this category
        $totalExpenses = $expenseCategory->expenses()
            ->where('status', 'approved')
            ->sum('amount');
    
        // Calculate remaining amount
        $remainingAmount = $assignedAmount - $totalExpenses;
    
        // Calculate percentage used
        $percentageUsed = $assignedAmount > 0
            ? round(($totalExpenses / $assignedAmount) * 100, 2)
            : 0;
    
        // Determine status based on remaining and assigned
        $status = $this->getBudgetStatus($remainingAmount, $assignedAmount);
    
        return [
            'assigned_amount' => $assignedAmount,
            'total_expenses' => $totalExpenses,
            'remaining_amount' => $remainingAmount,
            'percentage_used' => $percentageUsed,
            'percentage_remaining' => max(0, 100 - $percentageUsed),
            'status' => $status,
            'is_over_budget' => $remainingAmount < 0,
            'category_name' => $expenseCategory->name,
            'category_color' => $expenseCategory->color ?? '#6B7280',
        ];
    }
    

    /**
     * Get budget status based on remaining amount and usage percentage
     *
     * @param float $remainingAmount
     * @param float $assignedAmount
     * @return string
     */
    private function getBudgetStatus(float $remainingAmount, float $assignedAmount): string
    {
        if ($remainingAmount < 0) {
            return 'over_budget';
        }
        
        if ($assignedAmount == 0) {
            return 'no_budget';
        }
        
        $percentageUsed = ($assignedAmount - $remainingAmount) / $assignedAmount * 100;
        
        if ($percentageUsed >= 90) {
            return 'critical';
        } elseif ($percentageUsed >= 75) {
            return 'warning';
        } elseif ($percentageUsed >= 50) {
            return 'moderate';
        } else {
            return 'good';
        }
    }

    /**
     * Get expense tracking data for multiple categories
     *
     * @param Collection|null $categories
     * @return Collection
     */
    public function getExpenseTrackingData($categories = null): Collection
    {
        if (!$categories) {
            $categories = ExpenseCategory::where('active', true)->get();
        }
        
        return $categories->map(function ($category) {
            return $this->createExpenseTrack($category);
        });
    }

    /**
     * Get summary statistics for all expense categories
     *
     * @return array
     */
    public function getExpenseTrackingSummary(): array
    {
        $categories = ExpenseCategory::where('active', true)->get();
        $trackingData = $this->getExpenseTrackingData($categories);
        
        $totalAssigned = $trackingData->sum('assigned_amount');
        $totalSpent = $trackingData->sum('total_expenses');
        $totalRemaining = $trackingData->sum('remaining_amount');
        
        $overBudgetCategories = $trackingData->where('is_over_budget', true)->count();
        $criticalCategories = $trackingData->where('status', 'critical')->count();
        
        return [
            'total_assigned' => $totalAssigned,
            'total_spent' => $totalSpent,
            'total_remaining' => $totalRemaining,
            'overall_percentage_used' => $totalAssigned > 0 
                ? round(($totalSpent / $totalAssigned) * 100, 2) 
                : 0,
            'categories_count' => $categories->count(),
            'over_budget_count' => $overBudgetCategories,
            'critical_count' => $criticalCategories,
            'categories_data' => $trackingData
        ];
    }

    /**
     * Remove the specified expense category from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        // Check if category is in use
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Cannot delete category that is in use by expenses.');
        }
        
        $expenseCategory->delete();
        
        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}