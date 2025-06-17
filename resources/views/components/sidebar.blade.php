<div class="w-44 flex-shrink-0 mr-8">
<a href="{{ route('code-generator.index') }}" 
       wire:navigate 
       class="py-2 pl-6 pr-2 mb-4 block  {{ request()->routeIs('code-generator.index') ? 'text-blue-500' : 'text-gray-700'}}">
        Rest API
    </a>
    <a href="{{ route('code-generator.logs') }}"
       wire:navigate 
       class="py-2 pl-6 pr-2 mb-4 block {{ request()->routeIs('code-generator.logs') ? 'text-blue-500' : 'text-gray-700' }}">
        Logs
    </a>
</div>