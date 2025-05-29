@extends('layouts.app')

@section('title', 'Edit Expense Category')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Expense Category</h1>
        <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="position-relative">
                <x-loading-spinner id="form-spinner" class="d-none" overlay="false" />
                
                <form action="{{ route('expense-categories.update', $expenseCategory) }}" method="POST" id="category-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $expenseCategory->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $expenseCategory->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text color-preview" id="color-preview"></span>
                            <input type="color" class="form-control form-control-color" 
                                   id="color-picker" value="{{ old('color', $expenseCategory->color) }}">
                            <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                   id="color" name="color" value="{{ old('color', $expenseCategory->color) }}" 
                                   pattern="^#[0-9A-Fa-f]{6}$" required>
                        </div>
                        <small class="form-text text-muted">Choose a color for this category (hex format: #RRGGBB)</small>
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" 
                               {{ old('active', $expenseCategory->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('expense-categories.index') }}" class="btn btn-light me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .color-preview {
        width: 40px;
    }
    
    /* Hide the default color input */
    input[type="color"] {
        width: 0;
        padding: 0;
        border: none;
        height: 0;
        visibility: hidden;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorPicker = document.getElementById('color-picker');
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('color-preview');
        
        // Initialize color preview
        colorPreview.style.backgroundColor = colorInput.value;
        
        // Update color input when color picker changes
        colorPicker.addEventListener('input', function(e) {
            colorInput.value = e.target.value;
            colorPreview.style.backgroundColor = e.target.value;
        });
        
        // Update color picker when color input changes
        colorInput.addEventListener('input', function(e) {
            if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                colorPicker.value = e.target.value;
                colorPreview.style.backgroundColor = e.target.value;
            }
        });
        
        // Show color picker when clicking on preview
        colorPreview.addEventListener('click', function() {
            colorPicker.click();
        });
        
        // Show loading spinner when form is submitted
        document.getElementById('category-form').addEventListener('submit', function() {
            document.getElementById('form-spinner').classList.remove('d-none');
        });
    });
</script>
@endsection
