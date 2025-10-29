@php
$classes = match($variant) {
    'primary' => 'bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white shadow-lg shadow-purple-500/50 dark:shadow-purple-500/50',
    'secondary' => 'bg-purple-100 dark:bg-white/10 hover:bg-purple-200 dark:hover:bg-white/20 text-purple-900 dark:text-white border border-purple-200 dark:border-white/20',
    default => 'bg-purple-600 hover:bg-purple-700 text-white'
};
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => "w-full py-3 px-5 rounded-xl font-semibold transition-all transform active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-transparent flex items-center $classes"]) }}
>
    {{ $slot }}
</button>
