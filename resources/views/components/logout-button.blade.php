@props([
    'class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150',
    'showIcon' => true,
    'showText' => true,
    'confirmMessage' => '¿Estás seguro de que deseas cerrar sesión?',
    'size' => 'normal' // normal, small, large
])

@php
    $sizeClasses = [
        'small' => 'px-2 py-1 text-xs',
        'normal' => 'px-4 py-2 text-xs',
        'large' => 'px-6 py-3 text-sm'
    ];
    
    $iconSizes = [
        'small' => 'w-3 h-3',
        'normal' => 'w-4 h-4',
        'large' => 'w-5 h-5'
    ];
    
    $finalClass = str_replace(['px-4 py-2 text-xs'], [$sizeClasses[$size]], $class);
@endphp

<form method="POST" action="{{ route('logout') }}" class="inline-block" onsubmit="return confirmLogout(event, '{{ $confirmMessage }}')">
    @csrf
    <button type="submit" {{ $attributes->merge(['class' => $finalClass]) }}>
        @if($showIcon)
            <svg class="{{ $iconSizes[$size] }} {{ $showText ? 'mr-2' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
        @endif
        
        @if($showText)
            {{ $slot->isEmpty() ? 'Cerrar Sesión' : $slot }}
        @endif
    </button>
</form>

<script>
function confirmLogout(event, message) {
    if (message && message.trim() !== '') {
        return confirm(message);
    }
    return true;
}
</script>
