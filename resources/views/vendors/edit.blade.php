<x-dashboard-layout title="Edit Vendor - CheckMate Events">
    <div class="mb-6">
        <a href="{{ route('vendors.index') }}" class="text-white/60 hover:text-white inline-flex items-center gap-2 mb-4 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Vendors
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Vendor</h1>
    </div>

    <x-card>
        <form id="vendorForm" method="POST" action="{{ route('vendors.update', $vendor) }}">
            @csrf
            @method('PUT')
            @include('vendors._form', ['vendor' => $vendor])
        </form>
    </x-card>
</x-dashboard-layout>
