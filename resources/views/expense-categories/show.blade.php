<x-layouts.app title="Expense Categories">

<!-- //breadcrumb -->
 <div class=" px-3 py-4">

    <div class="px-4 py-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('Expenses') }}">Expenses Categories</flux:breadcrumbs.item>
            <!-- <flux:breadcrumbs.item></flux:breadcrumbs.item> -->
        </flux:breadcrumbs>
    </div>

<div class="container-fluid px-4">
    <div class=" flex justify-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Category Details</h1>
        <div>
            <a href="{{ route('expense-categories.edit', $expenseCategory) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="row">
    <div class="w-full md:w-1/2 xl:w-full mb-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-full flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Category Information</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $expenseCategory->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $expenseCategory->active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="p-4 flex-grow">
            <div class="mb-6 text-center">
                <div class="w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center shadow-sm"
                     style="background-color: {{ $expenseCategory->color }}">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7a1 1 0 011.414-1.414L8 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900">{{ $expenseCategory->name }}</h4>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-gray-800">{{ $expenseCategory->description ?? 'No description provided.' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Color</label>
                    <div class="flex items-center mt-1">
                        <span class="w-5 h-5 rounded-full mr-2 border border-gray-300" style="background-color: {{ $expenseCategory->color }}"></span>
                        <span class="text-gray-800 font-mono">{{ $expenseCategory->color }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Created</label>
                    <p class="mt-1 text-gray-800">{{ $expenseCategory->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="mt-1 text-gray-800">{{ $expenseCategory->updated_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
        
        <div class="w-full md:w-1/2 xl:w-full mb-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-full flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Related Expenses</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $expenseCategory->expenses_count ?? $expenseCategory->expenses->count() }}
            </span>
        </div>
        <div class="p-4 flex-grow">
            @if($expenseCategory->expenses->isEmpty())
                <div class="text-center py-10">
                    <svg class="mx-auto w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9V5l-3 3-3-3v4M3 13l4 4L3 21m18-8l-4 4 4 4M8 13h8a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2z"></path>
                    </svg>
                    <p class="text-gray-500">No expenses found in this category.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($expenseCategory->expenses as $expense)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($expense->description, 30) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @switch($expense->status)
                                            @case('draft')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                                @break
                                            @case('submitted')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Submitted</span>
                                                @break
                                            @case('approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($expense->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
    </div>
    
    
</div>
</div>
</x-layouts.app>

@section('styles')
<style>
    .color-swatch {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        vertical-align: middle;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .category-color-badge {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        if (confirm(`Are you sure you want to delete the category "${name}"?`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable if needed
        if (document.getElementById('expensesTable')) {
            $('#expensesTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 5,
                "language": {
                    "emptyTable": "No expenses found in this category"
                }
            });
        }
    });
</script>
@endsection
