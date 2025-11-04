@php
$colorClasses = match($color) {
    'purple' => 'from-purple-500 to-purple-600',
    'pink' => 'from-pink-500 to-pink-600',
    'blue' => 'from-blue-500 to-blue-600',
    'green' => 'from-green-500 to-green-600',
    default => 'from-purple-500 to-purple-600'
};

$iconClass = match($icon) {
    'calendar' => 'fa-duotone fa-calendar-days',
    'users' => 'fa-duotone fa-users',
    'camera' => 'fa-duotone fa-camera',
    'money' => 'fa-duotone fa-bangladeshi-taka-sign',
    default => 'fa-duotone fa-chart-mixed'
};
@endphp

<div class="bg-white/90 dark:bg-white/5 backdrop-blur-xl rounded-2xl border border-gray-300/50 dark:border-white/10 p-6 hover:border-purple-500/30 transition shadow-lg shadow-purple-500/5 dark:shadow-none">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-gray-600 dark:text-white/60 text-sm font-medium mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
            @if($change)
                <p class="text-sm mt-2 {{ str_contains($change, '+') ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $change }}
                </p>
            @endif
        </div>
        <div class="bg-gradient-to-br {{ $colorClasses }} w-12 h-12 rounded-xl flex items-center justify-center">
            <i class="{{ $iconClass }} text-white text-2xl"></i>
        </div>
    </div>
</div>
