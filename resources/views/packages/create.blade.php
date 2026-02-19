<x-dashboard-layout title="Create Package - CheckMate Events">
    <div class="mb-6">
        <a href="{{ route('packages.index') }}" class="text-white/60 hover:text-white inline-flex items-center gap-2 mb-4 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Packages
        </a>
        <h1 class="text-2xl font-bold text-white">Create Package</h1>
    </div>

    <x-card>
        <form id="packageForm" method="POST" action="{{ route('packages.store') }}">
            @csrf
            @include('packages._form')
        </form>
    </x-card>
</x-dashboard-layout>
