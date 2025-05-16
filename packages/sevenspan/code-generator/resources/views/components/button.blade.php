@props([
'title',
'showPlus' => false,
])

<button {{ $attributes->merge(['class' => 'bg-red-500 text-white py-3 pl-3 pr-4 rounded-lg flex items-center']) }}>
    @if($showPlus)
    <span class="mr-1">+</span>
    @endif
    {{ $title }}
</button>