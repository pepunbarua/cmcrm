<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/20">
            @isset($logo)
                <div class="flex justify-center mb-8">
                    {{ $logo }}
                </div>
            @endisset

            {{ $slot }}
        </div>
    </div>
</div>
