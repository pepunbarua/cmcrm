@php
$paddingClass = $noPadding ? '' : 'p-6';
@endphp

<div {{ $attributes->merge(['class' => "bg-white/90 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-gray-300/50 dark:border-white/10 shadow-xl shadow-purple-500/5 dark:shadow-none $paddingClass"]) }}>
    @if($title)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        </div>
    @endif
    
    {{ $slot }}
</div>
