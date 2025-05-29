<x-layouts.app title="Expense Categories">

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Expense Categories</h1>
                <p class="text-sm text-gray-600 mt-1">Manage your expense categories and organize your spending</p>
            </div>

            <!-- Add Category Button -->
            <flux:modal.trigger name="create-category">
                <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Category
                </button>
            </flux:modal.trigger>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    @if($categoriesWithTracking->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No categories yet</h3>
            <p class="text-gray-600 mb-6 max-w-sm mx-auto">Get started by creating your first expense category to organize your spending.</p>
            <flux:modal.trigger name="create-category">
                <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create your first category
                </button>
            </flux:modal.trigger>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categoriesWithTracking as $category)
                <a href="{{ route('expense-categories.show', $category->id) }}" class="block">
                    <div class="relative bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200 h-full flex flex-col">
                        <div class="absolute top-0 left-0 w-full h-1 rounded-t-lg" style="background-color: {{ $category->color ?? '#6B7280' }}"></div>

                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center flex-1 min-w-0 mr-3">
                                <div class="w-3 h-3 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $category->color ?? '#6B7280' }}"></div>
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $category->name }}</h3>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $category->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $category->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        @if($category->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $category->description }}</p>
                        @else
                            <p class="text-gray-400 text-sm mb-4 italic">No description provided</p>
                        @endif

                        <div class="mb-4 space-y-3 mt-auto"> {{-- mt-auto pushes this section to the bottom --}}
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Budget:</span>
                                <span class="font-semibold text-gray-900">${{ number_format($category->assign_amount ?? 0, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Spent:</span>
                                <span class="font-semibold {{ ($category->tracking['is_over_budget'] ?? false) ? 'text-red-600' : 'text-gray-900' }}">
                                    ${{ number_format($category->tracking['total_expenses'] ?? 0, 2) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Remaining:</span>
                                <span class="font-semibold {{ ($category->tracking['is_over_budget'] ?? false) ? 'text-red-600' : 'text-green-600' }}">
                                    ${{ number_format($category->tracking['remaining_amount'] ?? 0, 2) }}
                                </span>
                            </div>

                            @if($category->assign_amount && $category->assign_amount > 0)
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-500">Progress</span>
                                        <span class="font-medium {{ ($category->tracking['percentage_used'] ?? 0) > 100 ? 'text-red-600' : 'text-gray-700' }}">
                                            {{ number_format($category->tracking['percentage_used'] ?? 0, 1) }}%
                                        </span>
                                    </div>

                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden relative">
                                        @php
                                            $percentage = min(100, $category->tracking['percentage_used'] ?? 0);
                                            $overBudget = ($category->tracking['percentage_used'] ?? 0) > 100;

                                            // Determine progress bar color based on usage
                                            if ($overBudget) {
                                                $barColor = 'bg-red-500';
                                            } elseif ($percentage >= 90) {
                                                $barColor = 'bg-red-400';
                                            } elseif ($percentage >= 75) {
                                                $barColor = 'bg-yellow-400';
                                            } elseif ($percentage >= 50) {
                                                $barColor = 'bg-blue-400';
                                            } else {
                                                $barColor = 'bg-green-400';
                                            }
                                        @endphp

                                        <div class="h-full {{ $barColor }} transition-all duration-300 ease-out rounded-full"
                                             style="width: {{ $percentage }}%"></div>

                                        @if($overBudget)
                                            <div class="absolute top-0 right-0 w-0.5 h-full bg-red-600 animate-pulse"></div>
                                        @endif
                                    </div>

                                    <div class="flex items-center text-xs">
                                        @if($overBudget)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Over Budget
                                            </span>
                                        @elseif($percentage >= 90)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Critical
                                            </span>
                                        @elseif($percentage >= 75)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Warning
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                On Track
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <span class="text-gray-400 text-sm italic">No budget assigned</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-4">
                            <span class="text-xs text-gray-500 font-mono">{{ $category->color ?? '#6B7280' }}</span>
                            <div class="flex items-center space-x-2">
                                <flux:modal.trigger name="edit-category-{{ $category->id }}">
                                    <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-500 hover:bg-blue-50 rounded-md transition">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                </flux:modal.trigger>
                                <button onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-500 hover:bg-red-50 rounded-md transition">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>

                        <form id="delete-form-{{ $category->id }}" action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

    <!-- Create Category Modal -->
    <flux:modal name="create-category" title="Create Expense Category" class="sm:max-w-md">
        <form id="category-form" action="{{ route('expense-categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border @error('name') border-red-300 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                       placeholder="e.g., Food & Dining, Transportation"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="w-full px-3 py-2 border @error('description') border-red-300 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                          placeholder="Optional description for this category">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color Picker -->
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Color <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-3">
                    <div id="color-preview" 
                         class="w-10 h-10 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-gray-400 transition shadow-sm" 
                         style="background-color: {{ old('color', '#3B82F6') }}"
                         title="Click to open color picker"></div>
                    <input type="color" 
                           id="color-picker" 
                           value="{{ old('color', '#3B82F6') }}" 
                           class="sr-only">
                    <input type="text" 
                           id="color" 
                           name="color"
                           value="{{ old('color', '#3B82F6') }}"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           class="flex-1 px-3 py-2 border @error('color') border-red-300 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-mono text-sm"
                           placeholder="#3B82F6"
                           required>
                </div>
                <p class="mt-1 text-xs text-gray-500">Choose a color to help identify this category</p>
                @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assign Amount -->
            <div>
                <label for="assign_amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Assign Amount <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" 
                           id="assign_amount" 
                           name="assign_amount" 
                           value="{{ old('assign_amount') }}"
                           step="0.01"
                           min="0"
                           class="w-full pl-7 pr-3 py-2 border @error('assign_amount') border-red-300 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="0.00"
                           required>
                </div>
                @error('assign_amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" 
                       id="active" 
                       name="active" 
                       value="1"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       {{ old('active', '1') == '1' ? 'checked' : '' }}>
                <label for="active" class="ml-2 text-sm text-gray-700">
                    Make this category active
                </label>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                        onclick="document.getElementById('category-form').reset(); updateColorPreview();">
                    Reset
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Create Category
                </button>
            </div>
        </form>
    </flux:modal>

    @push('scripts')
    <script>
        // Delete confirmation
        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete the category "${name}"?\n\nThis action cannot be undone.`)) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        }

        // Color picker functionality
        document.addEventListener('DOMContentLoaded', function() {
            const colorInput = document.getElementById('color');
            const colorPicker = document.getElementById('color-picker');
            const colorPreview = document.getElementById('color-preview');

            if (colorInput && colorPicker && colorPreview) {
                // Update preview when text input changes
                colorInput.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                        colorPicker.value = value;
                        colorPreview.style.backgroundColor = value;
                    }
                });

                // Update text input when color picker changes
                colorPicker.addEventListener('input', function(e) {
                    colorInput.value = e.target.value;
                    colorPreview.style.backgroundColor = e.target.value;
                });

                // Open color picker when preview is clicked
                colorPreview.addEventListener('click', function() {
                    colorPicker.click();
                });
            }

            // Global function for reset button
            window.updateColorPreview = function() {
                const defaultColor = '#3B82F6';
                if (colorInput && colorPicker && colorPreview) {
                    colorInput.value = defaultColor;
                    colorPicker.value = defaultColor;
                    colorPreview.style.backgroundColor = defaultColor;
                }
            };
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[x-data*="show"]');
            alerts.forEach(alert => {
                if (alert.__x && alert.__x.$data.show) {
                    alert.__x.$data.show = false;
                }
            });
        }, 5000);
    </script>
    @endpush
</x-layouts.app>