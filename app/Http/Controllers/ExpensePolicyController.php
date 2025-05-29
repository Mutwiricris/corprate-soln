<?php

namespace App\Http\Controllers;

use App\Models\ExpensePolicy;
use Illuminate\Http\Request;

class ExpensePolicyController extends Controller
{
    /**
     * Display a listing of the expense policies.
     */
    public function index()
    {
        $policies = ExpensePolicy::all();
        return view('expense-policies.index', compact('policies'));
    }

    /**
     * Show the form for creating a new expense policy.
     */
    public function create()
    {
        return view('expense-policies.create');
    }

    /**
     * Store a newly created expense policy in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_policies',
            'description' => 'nullable|string',
            'threshold_amount' => 'required|numeric|min:0',
            'requires_receipt' => 'boolean',
            'approval_levels' => 'required|integer|min:1|max:5',
            'approval_hierarchy' => 'nullable|json',
            'category_restrictions' => 'nullable|json',
            'active' => 'boolean',
        ]);
        
        // Format JSON fields
        if ($request->has('approval_hierarchy') && is_array($request->approval_hierarchy)) {
            $validated['approval_hierarchy'] = json_encode($request->approval_hierarchy);
        }
        
        if ($request->has('category_restrictions') && is_array($request->category_restrictions)) {
            $validated['category_restrictions'] = json_encode($request->category_restrictions);
        }
        
        ExpensePolicy::create($validated);
        
        return redirect()->route('expense-policies.index')
            ->with('success', 'Expense policy created successfully.');
    }

    /**
     * Display the specified expense policy.
     */
    public function show(ExpensePolicy $expensePolicy)
    {
        return view('expense-policies.show', compact('expensePolicy'));
    }

    /**
     * Show the form for editing the specified expense policy.
     */
    public function edit(ExpensePolicy $expensePolicy)
    {
        return view('expense-policies.edit', compact('expensePolicy'));
    }

    /**
     * Update the specified expense policy in storage.
     */
    public function update(Request $request, ExpensePolicy $expensePolicy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_policies,name,' . $expensePolicy->id,
            'description' => 'nullable|string',
            'threshold_amount' => 'required|numeric|min:0',
            'requires_receipt' => 'boolean',
            'approval_levels' => 'required|integer|min:1|max:5',
            'approval_hierarchy' => 'nullable|json',
            'category_restrictions' => 'nullable|json',
            'active' => 'boolean',
        ]);
        
        // Format JSON fields
        if ($request->has('approval_hierarchy') && is_array($request->approval_hierarchy)) {
            $validated['approval_hierarchy'] = json_encode($request->approval_hierarchy);
        }
        
        if ($request->has('category_restrictions') && is_array($request->category_restrictions)) {
            $validated['category_restrictions'] = json_encode($request->category_restrictions);
        }
        
        $expensePolicy->update($validated);
        
        return redirect()->route('expense-policies.index')
            ->with('success', 'Expense policy updated successfully.');
    }

    /**
     * Remove the specified expense policy from storage.
     */
    public function destroy(ExpensePolicy $expensePolicy)
    {
        $expensePolicy->delete();
        
        return redirect()->route('expense-policies.index')
            ->with('success', 'Expense policy deleted successfully.');
    }
}
