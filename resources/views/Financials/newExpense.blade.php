<x-layouts.app title="New Expense">
    <div class="mt-7 px-3 py-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('Expenses') }}">Expenses</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>New Expense</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <h1 class="font-bold text-3xl py-6 p-4">Create New Expense</h1>

    <div class="border rounded-xl p-14">
        <flux:fieldset label="Add New Expense">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-6xl">
                @csrf
                
                <!-- Display validation errors if any -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div>
                                <p class="text-sm text-red-700">Please fix the following errors:</p>
                                <ul class="list-disc pl-5 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <flux:input name="title" label="Expense Title" placeholder="e.g. Office Supplies" 
                            value="{{ old('title') }}" required class="w-full" />
                    </div>
                    
                    <!-- Category -->
                    <div>
                        <flux:select name="category_id" label="Category" required>
                            <option value="" disabled selected>Select a category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <flux:textarea name="description" label="Description" placeholder="Enter a detailed description" 
                        value="{{ old('description') }}" rows="3" class="w-full" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Amount -->
                    <div>
                        <flux:input name="amount" type="number" step="0.01" label="Amount" 
                            placeholder="e.g. 50000" value="{{ old('amount') }}" required />
                    </div>
                    
                    <!-- Currency -->
                    <div>
                        <flux:select name="currency" label="Currency" required>
                            <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES (Kenyan Shilling)</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP (British Pound)</option>
                        </flux:select>
                    </div>
                    
                    <!-- Date -->
                    <div>
                        <flux:input name="expense_date" type="date" label="Expense Date" 
                            value="{{ old('expense_date', date('Y-m-d')) }}" required />
                    </div>
                </div>
                
                <!-- Receipt Upload -->
                <div class="border-t pt-6 mt-6">
                    <flux:heading size="sm" class="mb-4">Receipt Upload</flux:heading>
                    <div class="mb-4">
                        <label for="receipt" class="block text-sm font-medium text-gray-700 mb-1">Upload Receipt (PDF only)</label>
                        <input type="file" id="receipt" name="receipt" accept=".pdf"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Only PDF files are accepted (max 2MB)</p>
                            <p class="mt-1">A receipt number will be automatically generated in the format: <span class="font-mono font-medium">ASCCP-001-25</span></p>
                            <p class="mt-1">Where:</p>
                            <ul class="list-disc pl-5 mt-1">
                                <li><span class="font-mono">ASCCP</span> - AscendCorp receipt prefix</li>
                                <li><span class="font-mono">001</span> - Sequential number</li>
                                <li><span class="font-mono">25</span> - Year (2025)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="border-t pt-6 mt-6 flex justify-end space-x-3">
                    <a href="{{ route('Expenses') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <flux:button type="submit" variant="primary">Submit Expense</flux:button>
                </div>
            </form>
        </flux:fieldset>
    </div>
</x-layouts.app>
