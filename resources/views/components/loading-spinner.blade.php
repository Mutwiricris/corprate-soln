@props(['id' => null, 'overlay' => false, 'class' => ''])

@if(isset($attributes['wire:loading']))
    <div {{ $attributes }} class="{{ $overlay ? 'fixed inset-0 z-50' : 'absolute inset-0 z-10' }} flex items-center justify-center {{ $overlay ? 'bg-black bg-opacity-50' : 'bg-white bg-opacity-75' }} {{ $class }}">
        <flux:spinner size="xl" class="{{ $overlay ? 'text-white' : 'text-primary' }}" />
    </div>
@else
    <div id="{{ $id }}" class="{{ $overlay ? 'fixed inset-0 z-50' : 'absolute inset-0 z-10' }} flex items-center justify-center {{ $overlay ? 'bg-black bg-opacity-50' : 'bg-white bg-opacity-75' }} {{ $class }}">
        <flux:spinner size="xl" class="{{ $overlay ? 'text-white' : 'text-primary' }}" />
    </div>
@endif
