<x-layouts.app title="Expenses">
    <!-- Page-specific loading spinner -->
    <div wire:loading wire:target="filter, sort, search" class="fixed inset-0 z-40 flex items-center justify-center">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        <div class="relative z-50 bg-white p-6 rounded-lg shadow-xl flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600"></div>
            <span class="text-gray-700 font-medium">Loading expenses...</span>
        </div>
    </div>
    
    <div class="px-4">
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 my-4">
                <div class="flex">
                    <div>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 my-4">
                <div class="flex">
                    <div>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h1 class="font-bold text-2xl py-4">Expense Management</h1>
            <div class="flex space-x-2">
                <a href="{{ route('newExpense') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    New Expense
                </a>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">Export</flux:button>
                    <flux:menu>
                        <a href="{{ route('expenses.export', ['format' => 'csv']) }}">
                            <flux:menu.item icon="document-text">CSV</flux:menu.item>
                        </a>
                        <a href="{{ route('expenses.export', ['format' => 'pdf']) }}">
                            <flux:menu.item icon="document">PDF</flux:menu.item>
                        </a>
                        <a href="{{ route('expenses.export', ['format' => 'excel']) }}">
                            <flux:menu.item icon="table-cells">Excel</flux:menu.item>
                        </a>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>
    </div>

    <flux:separator @class('mt-4')></flux:separator>
    
    <div class="container mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
{{--        //cards--}}


        <flux:card>
            <div class="flex justify-between items-center">
                <flux:heading class="font-semibold text-gray-600" size="md">
                    Total expense amount
                </flux:heading>
                <p class="text-sm text-green-600">{{ $stats['total'] > 0 ? '+' : '' }}{{ number_format(($stats['total'] ?? 0) / 1000, 1) }}K <span class="text-gray-500">this month</span></p>
            </div>

            <flux:text class="text-4xl font-bold text-gray-800 m-4 mt-5">
                KES {{ number_format($stats['total'] ?? 0, 2) }}
            </flux:text>

            <flux:separator></flux:separator>

            <div class="flex justify-between items-center mt-4">
                <a href="{{route('newExpense')}}" class="w-1/2 p-2 rounded-md text-white text-center font-semibold mr-2
                bg-gray-800"
                   variant="primary">Create new
                    Expense</a>
                <flux:dropdown>
                    <flux:button class="w-full" variant="filled" icon:trailing="chevron-down">Filter</flux:button>
                    <flux:menu>
                        <a href="{{ route('Expenses', ['status' => 'all']) }}">
                            <flux:menu.item>All Expenses</flux:menu.item>
                        </a>
                        <a href="{{ route('Expenses', ['status' => 'pending']) }}">
                            <flux:menu.item>Pending</flux:menu.item>
                        </a>
                        <a href="{{ route('Expenses', ['status' => 'approved']) }}">
                            <flux:menu.item>Approved</flux:menu.item>
                        </a>
                        <a href="{{ route('Expenses', ['status' => 'rejected']) }}">
                            <flux:menu.item>Rejected</flux:menu.item>
                        </a>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:card>
        
        <div class="flex justify-between flex-col">
            <div class="border-l-4 bg-yellow-50 p-4 rounded-lg border-yellow-300">
                <p class="p-2 font-medium">Pending Approvals</p>

                <span class="text-4xl font-bold text-gray-800 block my-4">
                    KES {{ number_format($stats['pending'] ?? 0, 2) }}
                </span>
                
                <div class="text-sm text-gray-500">
                    {{ $expenses->where('status', 'pending')->count() }} expense{{ $expenses->where('status', 'pending')->count() != 1 ? 's' : '' }} awaiting approval
                </div>
            </div>
            
            <div class="border-l-4 bg-green-50 p-4 mt-3 rounded-lg border-green-300">
                <div class="p-2 flex justify-between items-center">
                    <p class="font-medium">Approved</p>
                </div>

                <span class="text-4xl font-bold text-gray-800 block my-4">
                    KES {{ number_format($stats['approved'] ?? 0, 2) }}
                </span>
                
                <div class="text-sm text-gray-500">
                    {{ $expenses->where('status', 'approved')->count() }} expense{{ $expenses->where('status', 'approved')->count() != 1 ? 's' : '' }} approved
                </div>
            </div>
        </div>

        <div class="flex justify-between flex-col">
            <div class="border-l-4 bg-blue-50 p-4 rounded-lg border-blue-300">
                <p class="p-2 font-medium">Projected Expenditure</p>

                <span class="text-4xl font-bold text-gray-800 block my-4">
                    KES {{ number_format($stats['projected'] ?? 0, 2) }}
                </span>
                
                <div class="text-sm text-gray-500">
                    Based on current spending trends
                </div>
            </div>
            
            <div class="border-l-4 bg-red-50 p-4 mt-3 rounded-lg border-red-300">
                <div class="p-2 flex justify-between items-center">
                    <p class="font-medium">Rejected</p>
                </div>

                <span class="text-4xl font-bold text-gray-800 block my-4">
                    {{ $stats['rejected'] ?? 0 }}
                </span>
                
                <div class="text-sm text-gray-500">
                    {{ $expenses->where('status', 'rejected')->count() }} expense{{ $expenses->where('status', 'rejected')->count() != 1 ? 's' : '' }} rejected
                </div>
            </div>
        </div>






    </div>

    <div class="mt-12 px-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="font-bold text-2xl">Expenses List</h1>
            
            <form action="{{ route('Expenses') }}" method="GET" class="flex items-center space-x-2">
                <input type="search" name="search" value="{{ request('search') }}" 
                    class="border rounded-md px-3 py-2" placeholder="Search expenses..." />
                <flux:button type="submit">Search</flux:button>
            </form>
        </div>

        <div class="overflow-x-auto border rounded-lg shadow-sm">
            <div class="border-b">
                <div class="flex justify-between p-4 w-full">
                    <div class="flex space-x-2.5">
                        <flux:dropdown offset="-15" gap="2">
                            <flux:button icon:trailing="funnel">Filter</flux:button>
                            <flux:menu>
                                <a href="{{ route('Expenses') }}">
                                    <flux:menu.item>All Expenses</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', ['status' => 'draft']) }}">
                                    <flux:menu.item>Drafts</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', ['status' => 'pending']) }}">
                                    <flux:menu.item>Pending</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', ['status' => 'approved']) }}">
                                    <flux:menu.item>Approved</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', ['status' => 'rejected']) }}">
                                    <flux:menu.item>Rejected</flux:menu.item>
                                </a>
                            </flux:menu>
                        </flux:dropdown>
                        
                        <flux:dropdown>
                            <flux:button icon:trailing="chevron-down">Sort by</flux:button>
                            <flux:menu>
                                <a href="{{ route('Expenses', array_merge(request()->all(), ['sort' => 'date_desc'])) }}">
                                    <flux:menu.item>Newest first</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', array_merge(request()->all(), ['sort' => 'date_asc'])) }}">
                                    <flux:menu.item>Oldest first</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', array_merge(request()->all(), ['sort' => 'amount_desc'])) }}">
                                    <flux:menu.item>Highest amount</flux:menu.item>
                                </a>
                                <a href="{{ route('Expenses', array_merge(request()->all(), ['sort' => 'amount_asc'])) }}">
                                    <flux:menu.item>Lowest amount</flux:menu.item>
                                </a>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <div>
                        <flux:dropdown>
                            <flux:button variant="filled" icon:trailing="chevron-down">Export</flux:button>
                            <flux:menu>
                                <a href="{{ route('expenses.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                                    <flux:menu.item icon="document-text">CSV</flux:menu.item>
                                </a>
                                <a href="{{ route('expenses.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                                    <flux:menu.item icon="document">PDF</flux:menu.item>
                                </a>
                                <a href="{{ route('expenses.export', array_merge(request()->all(), ['format' => 'excel'])) }}">
                                    <flux:menu.item icon="table-cells">Excel</flux:menu.item>
                                </a>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </div>
            
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Title</th>
                        <th scope="col" class="px-6 py-3">Category</th>
                        <th scope="col" class="px-6 py-3">Amount</th>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $expense->id }}</td>
                            <td class="px-6 py-4">{{ $expense->title }}</td>
                            <td class="px-6 py-4">{{ $expense->category->name ?? 'Uncategorized' }}</td>
                            <td class="px-6 py-4">{{ $expense->currency }} {{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-4">{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                @if($expense->status == 'draft')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Draft</span>
                                @elseif($expense->status == 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                @elseif($expense->status == 'approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Approved</span>
                                @elseif($expense->status == 'rejected')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('expense.details', $expense->id) }}" class="text-blue-600 hover:underline">View</a>
                                    
                                    @if(in_array($expense->status, ['draft', 'rejected']))
                                        <a href="{{ route('expenses.edit', $expense->id) }}" class="text-green-600 hover:underline">Edit</a>
                                    @endif
                                    
                                    @if($expense->status == 'draft')
                                        <form action="{{ route('expenses.submit', $expense->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:underline">Submit</button>
                                        </form>
                                    @endif
                                    
                                    @if($expense->status == 'pending')
                                        <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:underline">Approve</button>
                                        </form>
                                        
                                        <button type="button" 
                                            class="text-red-600 hover:underline reject-button" 
                                            data-expense-id="{{ $expense->id }}">
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b">
                            <td colspan="7" class="px-6 py-4 text-center">No expenses found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 px-4">
            {{ $expenses->appends(request()->query())->links() }}
        </div>
    </div>
    
    <!-- Rejection Modal -->
    <flux:modal name="reject-expense-modal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Reject Expense</flux:heading>
                <flux:text class="mt-2">Please provide a reason for rejection</flux:text>
            </div>
            
            <form id="reject-form" action="" method="POST">
                @csrf
                <div class="space-y-4">
                    <flux:textarea name="rejection_reason" label="Rejection Reason" placeholder="Enter reason for rejection" required></flux:textarea>
                    
                    <div class="flex justify-end space-x-2">
                        <flux:button type="button" variant="subtle" onclick="Flux.dismissModal('reject-expense-modal')">Cancel</flux:button>
                        <flux:button type="submit" variant="filled" class="bg-red-600 hover:bg-red-700 text-white">Reject Expense</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>
    
    <!-- JavaScript for handling rejection modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectButtons = document.querySelectorAll('.reject-button');
            const rejectForm = document.getElementById('reject-form');
            
            rejectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const expenseId = this.getAttribute('data-expense-id');
                    rejectForm.action = `/expenses/${expenseId}/reject`;
                    Flux.openModal('reject-expense-modal');
                });
            });
        });
    </script>

</x-layouts.app>
