@props(['active' => false, 'label' => ''])

<li @class(['px-4 py-2 cursor-pointer', 'font-bold text-blue-600 border-b-2 border-blue-600' => $active])>
    {{ $label }}
</li>
