<x-layouts.app title="Create Expense Category">


<div class="px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Create Expense Category</h1>
        <a href="{{ route('expense-categories.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i> Back to Categories
        </a>
    </div>

    <div class="bg-white border rounded-lg p-6">
        <form action="{{ route('expense-categories.store') }}" method="POST" id="category-form">
            @csrf

            {{-- Category Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       class="mt-1 block w-full p-3 border @error('name')  border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1 block w-full border @error('description') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Color --}}
            <div class="mb-4">
                <label for="color" class="block text-sm font-medium text-gray-700">Color <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-4 mt-1">
                    <div id="color-preview" class="w-8 h-8 rounded border" style="background-color: {{ old('color', '#3498db') }}"></div>
                    <input type="color" id="color-picker" value="{{ old('color', '#3498db') }}" class="hidden">
                    <input type="text" id="color" name="color"
                           value="{{ old('color', '#3498db') }}"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           class="w-full border @error('color') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <p class="text-sm text-gray-500 mt-1">Choose a color for this category (e.g. #3498db)</p>
                @error('color')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active --}}
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="active" name="active" value="1"
                           class="form-checkbox text-blue-600"
                           {{ old('active', '1') == '1' ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end items-center gap-2">
                <button type="reset"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-sm font-medium rounded-md">
                    Reset
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const colorInput = document.getElementById('color');
        const colorPicker = document.getElementById('color-picker');
        const colorPreview = document.getElementById('color-preview');

        // Set preview on load
        colorPreview.style.backgroundColor = colorInput.value;

        // When text input changes
        colorInput.addEventListener('input', function (e) {
            if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                colorPicker.value = e.target.value;
                colorPreview.style.backgroundColor = e.target.value;
            }
        });

        // When color picker changes
        colorPicker.addEventListener('input', function (e) {
            colorInput.value = e.target.value;
            colorPreview.style.backgroundColor = e.target.value;
        });

        // Click preview to open color picker
        colorPreview.addEventListener('click', () => colorPicker.click());
    });
</script>
@endsection

</x-layouts.app>
