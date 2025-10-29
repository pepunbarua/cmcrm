<x-dashboard-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            Schedule New Event
        </h1>
    </div>

    <x-card>
        @include('events._form', ['event' => null, 'order' => $order, 'teamMembers' => $teamMembers])
    </x-card>
</x-dashboard-layout>
