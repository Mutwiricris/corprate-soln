<x-layouts.app title="Expense Details">
    {{-- Page-specific loading spinner --}}
    <div wire:loading class="fixed inset-0 z-40 flex items-center justify-center">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        <div class="relative z-50 bg-white p-6 rounded-lg shadow-xl flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600"></div>
            <span class="text-gray-700 font-medium">Loading expense details...</span>
        </div>
    </div>
    
    {{-- Breadcrumbs --}}
    <div class="px-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('Expenses') }}">Expenses</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Expense #{{ $expense->id ?? 'EXP-0931' }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 my-4">
            <div class="flex">
                <div>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-4 my-4">
            <div class="flex">
                <div>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Header with Actions --}}
    <div class="flex items-center justify-between px-4">
        <h1 class="font-bold text-3xl py-6">Expense #{{ $expense->id ?? 'EXP-0931' }}</h1>
        <div class="pt-4 flex space-x-4">
            {{-- Dropdown --}}
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Actions</flux:button>
                <flux:menu>
                    <a href="{{ route('expenses.export', ['id' => $expense->id ?? '1']) }}">
                        <flux:menu.item icon="arrow-down-tray">Download PDF</flux:menu.item>
                    </a>
                    
                    @if(($expense->status ?? '') == 'draft')
                        <form action="{{ route('expenses.submit', $expense ?? 1) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left">
                                <flux:menu.item icon="paper-airplane">Submit for Approval</flux:menu.item>
                            </button>
                        </form>
                    @endif
                    
                    @if(($expense->status ?? '') == 'pending')
                        <flux:modal.trigger name="Approval">
                            <flux:menu.item icon="check">Approve</flux:menu.item>
                        </flux:modal.trigger>
                        
                        <flux:modal.trigger name="Rejection">
                            <flux:menu.item icon="x-mark" class="text-red-600">Reject</flux:menu.item>
                        </flux:modal.trigger>
                    @endif
                    
                    <flux:menu.separator />
                    
                    @if(($expense->status ?? '') == 'draft')
                        <form action="{{ route('expenses.destroy', $expense ?? 1) }}" method="POST" class="w-full"
                              onsubmit="return confirm('Are you sure you want to delete this expense?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left">
                                <flux:menu.item icon="trash" class="text-red-600">Delete</flux:menu.item>
                            </button>
                        </form>
                    @endif
                </flux:menu>
            </flux:dropdown>

            {{-- Edit Button --}}
            @if(in_array(($expense->status ?? ''), ['draft', 'rejected']))
                <a href="{{ route('expenses.edit', $expense ?? 1) }}">
                    <flux:button>
                        Edit
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 size-4" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19.5 7.125 18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                        </svg>
                    </flux:button>
                </a>
            @endif
        </div>
    </div>

    {{-- Approval Modal --}}
    <flux:modal name="Approval" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Approve Expense</flux:heading>
                <flux:text class="mt-2">Are you sure you want to approve this expense?</flux:text>
            </div>
            <form action="{{ route('expenses.approve', $expense ?? 1) }}" method="POST">
                @csrf
                <flux:textarea name="approval_notes" label="Notes (Optional)" placeholder="Add any approval notes" />
                <div class="flex justify-end space-x-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="subtle">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="filled">Approve Expense</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    
    {{-- Rejection Modal --}}
    <flux:modal name="Rejection" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Reject Expense</flux:heading>
                <flux:text class="mt-2">Please provide a reason for rejecting this expense.</flux:text>
            </div>
            <form action="{{ route('expenses.reject', $expense ?? 1) }}" method="POST">
                @csrf
                <flux:textarea name="rejection_reason" label="Rejection Reason" placeholder="Enter reason for rejection" required />
                <div class="flex justify-end space-x-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="subtle">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="filled" class="bg-red-600 hover:bg-red-700 text-white">Reject Expense</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Expense Details Section --}}
    <div class="border rounded-xl p-8 mx-4 mb-6">
        <flux:fieldset label="Expense Details">
            <div class="space-y-6 max-w-4xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Expense ID</label>
                        <div class="text-gray-900">{{ $expense->id ?? 'EXP-0931' }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Submitted By</label>
                        <div class="text-gray-900">{{ $expense->user->name ?? 'John Doe' }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Category</label>
                        <div class="text-gray-900">{{ $expense->category->name ?? 'Office Supplies' }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Date</label>
                        <div class="text-gray-900">{{ $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '2025-05-28' }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Amount</label>
                        <div class="text-gray-900 font-semibold">{{ $expense->currency ?? 'KES' }} {{ number_format($expense->amount ?? 5000, 2) }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Status</label>
                        <div class="text-gray-900">
                            @if(($expense->status ?? 'pending') == 'draft')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">Draft</span>
                            @elseif(($expense->status ?? 'pending') == 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">Pending</span>
                            @elseif(($expense->status ?? '') == 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">Approved</span>
                            @elseif(($expense->status ?? '') == 'rejected')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800">Rejected</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 items-start gap-4">
                    <label class="text-sm font-medium text-gray-700">Description</label>
                    <div class="text-gray-900 bg-gray-50 p-3 rounded-md">
                        {{ $expense->description ?? 'Purchased new printer cartridges for the office.' }}
                    </div>
                </div>
                
                @if(($expense->status ?? '') == 'approved')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid grid-cols-2 items-center gap-4">
                            <label class="text-sm font-medium text-gray-700">Approved By</label>
                            <div class="text-gray-900">{{ $expense->approver->name ?? 'Jane Manager' }}</div>
                        </div>
                        
                        <div class="grid grid-cols-2 items-center gap-4">
                            <label class="text-sm font-medium text-gray-700">Approved On</label>
                            <div class="text-gray-900">{{ $expense->approved_at ? $expense->approved_at->format('Y-m-d H:i') : '2025-05-28' }}</div>
                        </div>
                    </div>
                @endif
                
                @if(($expense->status ?? '') == 'rejected')
                    <div class="grid grid-cols-1 items-start gap-4">
                        <label class="text-sm font-medium text-gray-700">Rejection Reason</label>
                        <div class="text-gray-900 bg-red-50 p-3 rounded-md">
                            {{ $expense->rejection_reason ?? 'Missing required documentation.' }}
                        </div>
                    </div>
                @endif
            </div>
        </flux:fieldset>
    </div>
    
    {{-- Receipt Section --}}
    <div class="border rounded-xl p-8 mx-4 mb-6">
        <flux:fieldset label="Receipt">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="grid grid-cols-2 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Receipt Number</label>
                        <div class="text-gray-900 font-mono">
                            {{ $expense->receipt_number ?? 'ASCCP-001-25' }}
                        </div>
                    </div>
                </div>
                
                @if(($expense->receipt_content ?? false) && ($expense->receipt_path ?? false))
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="flex flex-col items-center">
                            <div class="mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold">PDF Receipt</p>
                                    <p class="text-sm text-gray-500">{{ $expense->receipt_number ?? 'ASCCP-001-25' }}.pdf</p>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <a href="data:{{ $expense->receipt_mime_type ?? 'application/pdf' }};base64,{{ $expense->receipt_content }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Receipt
                                </a>
                                <a href="data:{{ $expense->receipt_mime_type ?? 'application/pdf' }};base64,{{ $expense->receipt_content }}" download="{{ $expense->receipt_number ?? 'receipt' }}.pdf" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-md text-center text-gray-500">
                        <p>No receipt uploaded for this expense.</p>
                        <p class="mt-2 text-sm">When uploaded, receipts will be stored as PDF files with the receipt number format: {{ $expense->receipt_number ?? 'ASCCP-001-25' }}.pdf</p>
                    </div>
                @endif
        </flux:fieldset>
    </div>

    {{-- Expense Audit Log Section --}}
    <div class="border rounded-xl p-8 mx-4 mb-6">
        <flux:fieldset label="Expense Activity">
            <div class="space-y-4">
                @if(isset($expense->auditLogs) && $expense->auditLogs->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($expense->auditLogs as $log)
                            <li class="py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($log->action == 'created')
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </span>
                                        @elseif($log->action == 'updated')
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </span>
                                        @elseif($log->action == 'approved')
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        @elseif($log->action == 'rejected')
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            Expense {{ ucfirst($log->action) }}
                                            @if($log->user)
                                                by {{ $log->user->name }}
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $log->created_at->format('M d, Y h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">No activity logs available for this expense.</p>
                    </div>
                @endif
            </div>
        </flux:fieldset>
    </div>
</x-layouts.app>
