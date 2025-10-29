<x-dashboard-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
            Edit Order - {{ $order->order_number }}
        </h1>
    </div>

    <x-card>
        @include('orders._form', ['order' => $order, 'lead' => null])
    </x-card>
</x-dashboard-layout>
