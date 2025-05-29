<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseAuditLog;
use App\Models\ExpensePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function expenseIndex(Request $request)
    {
        $query = Expense::with(['category', 'user']);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }
        
        // Filter by category if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search by title or description
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }
        
        // Sort results
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'date_asc':
                    $query->orderBy('expense_date', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('expense_date', 'desc');
                    break;
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            // Default sorting by latest
            $query->latest();
        }
        
        // Get expenses with pagination
        $expenses = $query->paginate(10);
        
        // Get categories for filtering
        $categories = ExpenseCategory::active()->get();
        
        // Get statistics
        $stats = [
            'total' => Expense::sum('amount'),
            'pending' => Expense::where('status', 'pending')->sum('amount'),
            'approved' => Expense::where('status', 'approved')->sum('amount'),
            'rejected' => Expense::where('status', 'rejected')->count(),
            'projected' => Expense::sum('amount') * 1.1, // Simple projection (10% increase)
        ];
        
        // Use the existing Financials.Expences view instead of expenses.index
        return view('Financials.Expences', compact('expenses', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $categories = ExpenseCategory::active()->get();
        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf|max:2048',
        ]);
        
        // Generate receipt number in the format ASCCP-001-25
        $year = date('y'); // Get the last two digits of the current year
        $latestExpense = Expense::whereNotNull('receipt_number')
            ->where('receipt_number', 'like', "ASCCP-%-%")
            ->orderBy('id', 'desc')
            ->first();
            
        $sequenceNumber = '001';
        
        if ($latestExpense && $latestExpense->receipt_number) {
            // Extract the sequence number from the latest receipt number
            $parts = explode('-', $latestExpense->receipt_number);
            if (count($parts) === 3) {
                $lastSequence = (int) $parts[1];
                $sequenceNumber = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
            }
        }
        
        $receiptNumber = "ASCCP-{$sequenceNumber}-{$year}";
        $validated['receipt_number'] = $receiptNumber;
        
        // Handle receipt upload - only allow PDF files
        if ($request->hasFile('receipt')) {
            // If the file is not a PDF, convert it to PDF
            $file = $request->file('receipt');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension !== 'pdf') {
                // For now, we'll just reject non-PDF files
                return back()->withErrors(['receipt' => 'Only PDF files are allowed for receipts.'])->withInput();
            }
            
            // Store the PDF content directly in the database
            $filename = $receiptNumber . '.pdf';
            $fileContent = file_get_contents($file->getRealPath());
            $validated['receipt_content'] = base64_encode($fileContent); // Base64 encode for safe storage
            $validated['receipt_mime_type'] = $file->getMimeType();
            $validated['receipt_path'] = $filename; // Just store the filename for reference
        }
        
        // Set initial status based on policies
        $policy = ExpensePolicy::active()->first();
        $initialStatus = 'draft';
        
        if ($policy) {
            // Check if receipt is required by policy
            if ($policy->requires_receipt && !$request->hasFile('receipt')) {
                return back()->withErrors(['receipt' => 'A receipt is required for this expense.'])->withInput();
            }
            
            // Set status to pending if it meets policy requirements
            $initialStatus = 'pending';
        }
        
        // Create the expense
        $expense = new Expense($validated);
        $expense->user_id = Auth::id();
        $expense->status = $initialStatus;
        $expense->save();
        
        // Log the creation
        $this->logActivity($expense, 'created', null, $expense->toArray());
        
        return redirect()->route('Expenses')
            ->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'approver', 'auditLogs.user']);
        return view('Financials.ExpenseDetails', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        // Only allow editing of draft or rejected expenses
        if (!in_array($expense->status, ['draft', 'rejected'])) {
            return redirect()->route('expense.details', $expense)
                ->with('error', 'You cannot edit an expense that is pending or approved.');
        }
        
        $categories = ExpenseCategory::active()->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // Only allow updating of draft or rejected expenses
        if (!in_array($expense->status, ['draft', 'rejected'])) {
            return redirect()->route('expense.details', $expense)
                ->with('error', 'You cannot update an expense that is pending or approved.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        
        // Store old values for audit log
        $oldValues = $expense->toArray();
        
        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt_path'] = $path;
        }
        
        // Set status to pending if it was draft or rejected
        $validated['status'] = 'pending';
        
        // Update the expense
        $expense->update($validated);
        
        // Log the update
        $this->logActivity($expense, 'updated', $oldValues, $expense->toArray());
        
        return redirect()->route('expense.details', $expense)
            ->with('success', 'Expense updated successfully');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        // Only allow deletion of draft expenses
        if ($expense->status !== 'draft') {
            return redirect()->route('expense.details', $expense)
                ->with('error', 'You cannot delete an expense that is not in draft status.');
        }
        
        // Store old values for audit log
        $oldValues = $expense->toArray();
        
        // Delete receipt if exists
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        
        // Delete the expense
        $expense->delete();
        
        // Log the deletion
        $this->logActivity($expense, 'deleted', $oldValues, null);
        
        return redirect()->route('Expenses')
            ->with('success', 'Expense deleted successfully.');
    }
    
    /**
     * Submit an expense for approval.
     */
    public function submit(Expense $expense)
    {
        // Only allow submission of draft expenses
        if ($expense->status !== 'draft') {
            return redirect()->route('expense.details', $expense)
                ->with('error', 'You can only submit draft expenses for approval.');
        }
        
        // Store old values for audit log
        $oldValues = $expense->toArray();
        
        // Update status to pending
        $expense->status = 'pending';
        $expense->save();
        
        // Log the submission
        $this->logActivity($expense, 'submitted', $oldValues, $expense->toArray());
        
        return redirect()->route('expense.details', $expense)
            ->with('success', 'Expense submitted for approval.');
    }
    
    /**
     * Approve an expense.
     */
    public function approve(Request $request, Expense $expense)
    {
        // Only allow approval of pending expenses
        if ($expense->status !== 'pending') {
            return redirect()->route('expense.details', $expense)
                ->with('error', 'You can only approve pending expenses.');
        }
        
        // Store old values for audit log
        $oldValues = $expense->toArray();
        
        // Update status to approved
        $expense->status = 'approved';
        $expense->approved_by = Auth::id();
        $expense->approved_at = now();
        $expense->save();
        
        // Log the approval
        $this->logActivity($expense, 'approved', $oldValues, $expense->toArray());
        
        return redirect()->route('expense.details', $expense)
            ->with('success', 'Expense approved successfully.');
    }
    
    /**
     * Reject an expense.
     */
    public function reject(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);
        
        // Only allow rejection of pending expenses
        if ($expense->status !== 'pending') {
            return redirect()->route('expense.details', $expense)
                ->with('error', 'You can only reject pending expenses.');
        }
        
        // Store old values for audit log
        $oldValues = $expense->toArray();
        
        // Update status to rejected
        $expense->status = 'rejected';
        $expense->rejection_reason = $validated['rejection_reason'];
        $expense->save();
        
        // Log the rejection
        $this->logActivity($expense, 'rejected', $oldValues, $expense->toArray());
        
        return redirect()->route('expense.details', $expense)
            ->with('success', 'Expense rejected successfully.');
    }
    
    /**
     * Export expenses to different formats (CSV, PDF, Excel).
     */
    public function export(Request $request, $format = 'csv')
    {
        $query = Expense::with(['category', 'user', 'approver']);
        
        // Apply filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search by title or description
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }
        
        // Get expenses
        $expenses = $query->get();
        
        // Generate filename
        $filename = 'expenses_' . now()->format('Y-m-d_His');
        
        // Log the export
        $this->logActivity(null, 'exported', null, [
            'filters' => $request->all(),
            'format' => $format,
            'count' => $expenses->count(),
        ]);
        
        // Handle different export formats
        switch (strtolower($format)) {
            case 'pdf':
                return $this->exportToPdf($expenses, $filename);
                
            case 'excel':
                return $this->exportToExcel($expenses, $filename);
                
            case 'csv':
            default:
                return $this->exportToCsv($expenses, $filename);
        }
    }
    
    /**
     * Export expenses to CSV format.
     */
    private function exportToCsv($expenses, $filename)
    {
        $filename .= '.csv';
        
        // Create CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        // Create CSV content
        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Title', 'Description', 'Amount', 'Currency', 
                'Category', 'Date', 'Status', 'Submitted By', 'Approved By', 
                'Approved At', 'Rejection Reason'
            ]);
            
            // Add expense data
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->id,
                    $expense->title,
                    $expense->description,
                    $expense->amount,
                    $expense->currency,
                    $expense->category->name ?? 'Uncategorized',
                    $expense->expense_date->format('Y-m-d'),
                    ucfirst($expense->status),
                    $expense->user->name ?? 'Unknown',
                    $expense->approver ? $expense->approver->name : '',
                    $expense->approved_at ? $expense->approved_at->format('Y-m-d H:i:s') : '',
                    $expense->rejection_reason,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export expenses to PDF format.
     */
    private function exportToPdf($expenses, $filename)
    {
        $filename .= '.pdf';
        
        // Use the dompdf library to generate a professional PDF with letterhead
        $pdf = \PDF::loadView('pdf.expense-report', compact('expenses'));
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');
        
        // Return the PDF as a download
        return $pdf->download($filename);
    }
    
    /**
     * Export expenses to Excel format.
     */
    private function exportToExcel($expenses, $filename)
    {
        $filename .= '.xlsx';
        
        // For now, we'll return a CSV with Excel headers
        // In a production environment, you might want to use a library like PhpSpreadsheet
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        // Create Excel content (same as CSV for now)
        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Title', 'Description', 'Amount', 'Currency', 
                'Category', 'Date', 'Status', 'Submitted By', 'Approved By', 
                'Approved At', 'Rejection Reason'
            ]);
            
            // Add expense data
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->id,
                    $expense->title,
                    $expense->description,
                    $expense->amount,
                    $expense->currency,
                    $expense->category->name ?? 'Uncategorized',
                    $expense->expense_date->format('Y-m-d'),
                    ucfirst($expense->status),
                    $expense->user->name ?? 'Unknown',
                    $expense->approver ? $expense->approver->name : '',
                    $expense->approved_at ? $expense->approved_at->format('Y-m-d H:i:s') : '',
                    $expense->rejection_reason,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Filter expenses by category.
     */
    public function filterByCategory(Request $request, $category)
    {
        $query = Expense::with(['category', 'user']);
        
        // Filter by category
        $query->where('category_id', $category);
        
        // Apply additional filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }
        
        // Get expenses with pagination
        $expenses = $query->latest()->paginate(10);
        
        // Get categories for filtering
        $categories = ExpenseCategory::active()->get();
        $currentCategory = ExpenseCategory::find($category);
        
        // Get statistics
        $stats = [
            'total' => $query->sum('amount'),
            'pending' => $query->where('status', 'pending')->sum('amount'),
            'approved' => $query->where('status', 'approved')->sum('amount'),
            'rejected' => $query->where('status', 'rejected')->count(),
            'projected' => $query->sum('amount') * 1.1, // Simple projection (10% increase)
        ];
        
        return view('Financials.Expences', compact('expenses', 'categories', 'stats', 'currentCategory'));
    }
    
    /**
     * View audit logs for all expenses.
     */
    public function auditLogs()
    {
        $logs = ExpenseAuditLog::with(['expense', 'user'])
            ->latest()
            ->paginate(20);
            
        return view('expenses.audit-logs', compact('logs'));
    }
    
    /**
     * Log an activity for an expense.
     */
    private function logActivity(?Expense $expense, string $action, ?array $oldValues, ?array $newValues)
    {
        $log = new ExpenseAuditLog();
        $log->expense_id = $expense ? $expense->id : null;
        $log->user_id = Auth::id();
        $log->action = $action;
        $log->old_values = $oldValues;
        $log->new_values = $newValues;
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();
        $log->save();
    }
}
