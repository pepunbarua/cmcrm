<x-dashboard-layout title="Edit Package Content - CheckMate Events">
    <div class="mb-6">
        <a href="{{ route('package-contents.index') }}" class="text-white/60 hover:text-white inline-flex items-center gap-2 mb-4 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Package Contents
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Package Content</h1>
    </div>

    <x-card>
        <form id="packageContentForm" method="POST" action="{{ route('package-contents.update', $packageContent) }}">
            @csrf
            @method('PUT')
            @include('package_contents._form', ['packageContent' => $packageContent])
        </form>
    </x-card>
</x-dashboard-layout>
