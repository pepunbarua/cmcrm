<x-dashboard-layout>
    <div class="p-6">
        <x-card>
            <div class="text-center py-16">
                <div class="text-purple-500 dark:text-purple-400 mb-6">
                    <i class="fa-duotone fa-phone-slash text-8xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No Leads Available</h2>
                <p class="text-gray-600 dark:text-white/60 mb-6 max-w-md mx-auto">
                    Great job! There are no leads to call right now. Check back later or explore other queue sections.
                </p>
                <div class="flex gap-3 justify-center">
                    <a href="{{ route('call-queue.index') }}" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-lg transition">
                        <i class="fa-duotone fa-dashboard mr-2"></i>
                        Go to Dashboard
                    </a>
                    <a href="{{ route('call-queue.pending') }}" class="px-6 py-3 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-white/20 transition">
                        <i class="fa-duotone fa-list mr-2"></i>
                        View Pending Calls
                    </a>
                </div>
            </div>
        </x-card>
    </div>
</x-dashboard-layout>
