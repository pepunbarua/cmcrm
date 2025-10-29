<x-dashboard-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            Edit Lead - {{ $lead->client_name }}
        </h1>
    </div>

    <x-card>
        @include('leads._form', ['lead' => $lead, 'vendors' => $vendors])
    </x-card>
</x-dashboard-layout>
