<x-dashboard-layout>
    <div class="p-6" x-data="leadDialer()">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fa-duotone fa-bullseye text-purple-600 mr-2"></i>Lead Dialer
            </h1>
            <p class="text-gray-600 dark:text-white/60">Call leads efficiently with smart prioritization</p>
        </div>

        <!-- Lock Timer -->
        <div class="mb-4 bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-duotone fa-lock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-900 dark:text-yellow-300">Lead Locked</p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-400">This lead is exclusively assigned to you</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-mono font-bold text-yellow-900 dark:text-yellow-300" x-text="timeRemaining"></span>
                    <button @click="extendLock" class="px-3 py-1 text-sm bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        Extend
                    </button>
                </div>
            </div>
        </div>

        <!-- Lead Information Card -->
        <x-card class="mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Client Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lead->client_name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-white/60 capitalize">{{ str_replace('_', ' ', $lead->event_type) }} Event</p>
                        </div>
                        @php
                            $activityStatus = $lead->formatted_activity_status;
                        @endphp
                        <span class="px-3 py-1 bg-{{ $activityStatus['color'] }}-100 dark:bg-{{ $activityStatus['color'] }}-500/20 text-{{ $activityStatus['color'] }}-900 dark:text-{{ $activityStatus['color'] }}-300 rounded-lg text-sm font-medium capitalize">
                            {{ $activityStatus['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-white/60 mb-1">
                                <i class="fa-duotone fa-phone mr-2"></i>Phone
                            </p>
                            <a href="tel:{{ $lead->client_phone }}" class="text-lg font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                                {{ $lead->client_phone }}
                            </a>
                        </div>
                        
                        @if($lead->client_email)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-white/60 mb-1">
                                <i class="fa-duotone fa-envelope mr-2"></i>Email
                            </p>
                            <a href="mailto:{{ $lead->client_email }}" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                                {{ $lead->client_email }}
                            </a>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-600 dark:text-white/60 mb-1">
                                <i class="fa-duotone fa-calendar mr-2"></i>Event Date
                            </p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $lead->event_date->format('M d, Y') }}
                                <span class="text-xs text-gray-500">({{ $lead->event_date->diffForHumans() }})</span>
                            </p>
                        </div>

                        @if($lead->budget_range)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-white/60 mb-1">
                                <i class="fa-duotone fa-money-bill mr-2"></i>Budget
                            </p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $lead->budget_range }}</p>
                        </div>
                        @endif

                        @if($lead->vendor)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-white/60 mb-1">
                                <i class="fa-duotone fa-building mr-2"></i>Venue
                            </p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $lead->vendor->name }}</p>
                        </div>
                        @endif
                    </div>

                    @if($lead->notes)
                    <div class="mt-4 p-3 bg-gray-100 dark:bg-white/5 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-white/70">{{ $lead->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Previous Activities -->
                <div class="border-l border-gray-200 dark:border-white/10 pl-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Previous Activities</h3>
                    @if($lead->leadActivities && $lead->leadActivities->count() > 0)
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($lead->leadActivities->take(5) as $activity)
                            <div class="text-sm">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fa-duotone fa-{{ $activity->activity_type === 'call' ? 'phone' : 'comment' }} text-purple-600"></i>
                                    <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $activity->activity_type }}</span>
                                    @if($activity->lead_interest_level)
                                    <span class="px-2 py-0.5 rounded text-xs
                                        {{ $activity->lead_interest_level === 'hot' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $activity->lead_interest_level === 'warm' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $activity->lead_interest_level === 'cold' ? 'bg-blue-100 text-blue-700' : '' }}">
                                        {{ $activity->lead_interest_level }}
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-white/50">{{ $activity->created_at->diffForHumans() }}</p>
                                @if($activity->notes)
                                <p class="text-xs text-gray-600 dark:text-white/60 mt-1">{{ Str::limit($activity->notes, 100) }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-white/50">No previous activities</p>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Activity Type Selection (Step 1) -->
        <x-card class="mb-6" x-show="currentStep === 'activity_type'">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Select Activity Type</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button @click="selectActivityType('call')" class="p-4 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-500/10 transition">
                    <i class="fa-duotone fa-phone text-3xl text-purple-600 dark:text-purple-400 mb-2"></i>
                    <p class="font-semibold text-gray-900 dark:text-white">Call</p>
                </button>
                <button @click="selectActivityType('whatsapp')" class="p-4 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition">
                    <i class="fa-brands fa-whatsapp text-3xl text-green-600 dark:text-green-400 mb-2"></i>
                    <p class="font-semibold text-gray-900 dark:text-white">WhatsApp</p>
                </button>
                <button @click="selectActivityType('email')" class="p-4 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition">
                    <i class="fa-duotone fa-envelope text-3xl text-blue-600 dark:text-blue-400 mb-2"></i>
                    <p class="font-semibold text-gray-900 dark:text-white">Email</p>
                </button>
                <button @click="selectActivityType('note')" class="p-4 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 transition">
                    <i class="fa-duotone fa-note text-3xl text-yellow-600 dark:text-yellow-400 mb-2"></i>
                    <p class="font-semibold text-gray-900 dark:text-white">Note</p>
                </button>
            </div>
        </x-card>

        <!-- Call Outcome Selection (Step 2 - for calls) -->
        <x-card class="mb-6" x-show="currentStep === 'call_outcome'">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Call Outcome</h3>
                <button @click="currentStep = 'activity_type'" class="text-sm text-gray-600 dark:text-white/60 hover:text-purple-600">
                    <i class="fa-duotone fa-arrow-left mr-2"></i>Back
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button @click="selectCallOutcome('answered')" class="p-3 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition">
                    <i class="fa-duotone fa-phone-volume text-2xl text-green-600 mb-2"></i>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Answered</p>
                </button>
                <button @click="selectCallOutcome('not_answered')" class="p-3 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 transition">
                    <i class="fa-duotone fa-phone-slash text-2xl text-yellow-600 mb-2"></i>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Not Answered</p>
                </button>
                <button @click="selectCallOutcome('busy')" class="p-3 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-500/10 transition">
                    <i class="fa-duotone fa-phone-office text-2xl text-orange-600 mb-2"></i>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Busy</p>
                </button>
                <button @click="selectCallOutcome('switched_off')" class="p-3 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                    <i class="fa-duotone fa-power-off text-2xl text-red-600 mb-2"></i>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Switched Off</p>
                </button>
                <button @click="selectCallOutcome('wrong_number')" class="p-3 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500/10 transition">
                    <i class="fa-duotone fa-ban text-2xl text-gray-600 mb-2"></i>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Wrong Number</p>
                </button>
                <button @click="selectCallOutcome('voicemail')" class="p-3 border-2 border-gray-300 dark:border-white/20 rounded-xl hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition">
                    <i class="fa-duotone fa-voicemail text-2xl text-blue-600 mb-2"></i>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Voicemail</p>
                </button>
            </div>
        </x-card>

        <!-- Activity Details Form (Step 3) -->
        <x-card x-show="currentStep === 'details'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Activity Details</h3>
                <button @click="currentStep = activityData.activity_type === 'call' ? 'call_outcome' : 'activity_type'" class="text-sm text-gray-600 dark:text-white/60 hover:text-purple-600">
                    <i class="fa-duotone fa-arrow-left mr-2"></i>Back
                </button>
            </div>

            <!-- Call Timer (shown when call is in progress) -->
            <div x-show="callInProgress" class="mb-6 p-4 bg-green-100 dark:bg-green-900/20 border border-green-300 dark:border-green-700 rounded-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-red-600 rounded-full animate-pulse"></div>
                        <div>
                            <p class="text-sm font-medium text-green-900 dark:text-green-300">Call in Progress</p>
                            <p class="text-xs text-green-700 dark:text-green-400">Recording duration... Please end call before submitting</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-2xl font-mono font-bold text-green-900 dark:text-green-300" x-text="callDuration"></span>
                        <button @click="endCall()" type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                            <i class="fa-duotone fa-phone-hangup mr-2"></i>End Call
                        </button>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitActivity">
                <!-- Interest Level -->
                <div class="mb-6" x-show="activityData.activity_type === 'call' && activityData.call_outcome === 'answered'">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">Interest Level</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <button type="button" @click="activityData.lead_interest_level = 'hot'" 
                            :class="activityData.lead_interest_level === 'hot' ? 'border-red-500 bg-red-50 dark:bg-red-500/20' : 'border-gray-300 dark:border-white/20'"
                            class="p-3 border-2 rounded-xl transition">
                            <i class="fa-duotone fa-fire text-2xl text-red-600 mb-1"></i>
                            <p class="font-semibold text-sm">Hot</p>
                            <p class="text-xs text-gray-600 dark:text-white/60">Very Interested</p>
                        </button>
                        <button type="button" @click="activityData.lead_interest_level = 'warm'"
                            :class="activityData.lead_interest_level === 'warm' ? 'border-orange-500 bg-orange-50 dark:bg-orange-500/20' : 'border-gray-300 dark:border-white/20'"
                            class="p-3 border-2 rounded-xl transition">
                            <i class="fa-duotone fa-sun text-2xl text-orange-600 mb-1"></i>
                            <p class="font-semibold text-sm">Warm</p>
                            <p class="text-xs text-gray-600 dark:text-white/60">Interested</p>
                        </button>
                        <button type="button" @click="activityData.lead_interest_level = 'cold'"
                            :class="activityData.lead_interest_level === 'cold' ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/20' : 'border-gray-300 dark:border-white/20'"
                            class="p-3 border-2 rounded-xl transition">
                            <i class="fa-duotone fa-snowflake text-2xl text-blue-600 mb-1"></i>
                            <p class="font-semibold text-sm">Cold</p>
                            <p class="text-xs text-gray-600 dark:text-white/60">Low Interest</p>
                        </button>
                        <button type="button" @click="activityData.lead_interest_level = 'not_interested'"
                            :class="activityData.lead_interest_level === 'not_interested' ? 'border-gray-500 bg-gray-50 dark:bg-gray-500/20' : 'border-gray-300 dark:border-white/20'"
                            class="p-3 border-2 rounded-xl transition">
                            <i class="fa-duotone fa-ban text-2xl text-gray-600 mb-1"></i>
                            <p class="font-semibold text-sm">Not Interested</p>
                        </button>
                        <button type="button" @click="activityData.lead_interest_level = 'converted'"
                            :class="activityData.lead_interest_level === 'converted' ? 'border-green-500 bg-green-50 dark:bg-green-500/20' : 'border-gray-300 dark:border-white/20'"
                            class="p-3 border-2 rounded-xl transition">
                            <i class="fa-duotone fa-check-circle text-2xl text-green-600 mb-1"></i>
                            <p class="font-semibold text-sm">Converted</p>
                        </button>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea x-model="activityData.notes" rows="4" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="What was discussed? Any important points to remember..."></textarea>
                </div>

                <!-- Follow-up Required -->
                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="activityData.follow_up_required" class="w-5 h-5 text-purple-600 rounded">
                        <span class="font-medium text-gray-900 dark:text-white">Follow-up Required</span>
                    </label>
                </div>

                <!-- Follow-up Date & Time -->
                <div x-show="activityData.follow_up_required" class="mb-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Follow-up Date</label>
                        <input type="date" x-model="activityData.next_follow_up_date" 
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Follow-up Time</label>
                        <input type="time" x-model="activityData.next_follow_up_time"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-xl">
                    </div>
                </div>

                <!-- Follow-up Notes -->
                <div x-show="activityData.follow_up_required" class="mb-6">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Follow-up Notes</label>
                    <textarea x-model="activityData.follow_up_notes" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-xl"
                        placeholder="What should be discussed in the follow-up?"></textarea>
                </div>

                <!-- Assign To -->
                <div x-show="activityData.follow_up_required" class="mb-6">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        <i class="fa-duotone fa-user-tag mr-2"></i>Assign Follow-up To
                    </label>
                    <select x-model="activityData.assigned_to"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 rounded-xl text-gray-900 dark:text-white">
                        <option value="">Select User</option>
                        @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ auth()->id() == $user->id ? '(Me)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3">
                    <button type="submit" 
                        :disabled="callInProgress"
                        :class="callInProgress ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg'"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl transition font-semibold">
                        <i class="fa-duotone fa-check mr-2"></i>
                        <span x-text="callInProgress ? 'End Call First' : 'Save & Continue'"></span>
                    </button>
                    <button type="button" @click="skipLead" class="px-6 py-3 bg-gray-200 dark:bg-white/10 text-gray-900 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-white/20 transition">
                        Skip Lead
                    </button>
                </div>
            </form>
        </x-card>

        <!-- Exit Button (Always visible) -->
        <div class="mt-6 text-center">
            <a href="{{ route('call-queue.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/30 transition">
                <i class="fa-duotone fa-door-open"></i>
                Exit Lead Dialer
            </a>
        </div>
    </div>

    <script>
        function leadDialer() {
            return {
                currentStep: 'activity_type',
                lockExpiresAt: '{{ $lead->lock_expires_at }}',
                timeRemaining: '',
                callInProgress: false,
                callStartTime: null,
                callDuration: '00:00',
                callTimer: null,
                activityData: {
                    activity_type: '',
                    call_outcome: null,
                    lead_interest_level: null,
                    notes: '',
                    follow_up_required: false,
                    next_follow_up_date: '',
                    next_follow_up_time: '',
                    follow_up_notes: '',
                    assigned_to: '{{ auth()->id() }}',
                    call_duration: 0,
                    call_started_at: null,
                    call_ended_at: null
                },

                init() {
                    this.startTimer();
                    setInterval(() => this.startTimer(), 1000);
                },

                startTimer() {
                    const now = new Date();
                    const expires = new Date(this.lockExpiresAt);
                    const diff = expires - now;

                    if (diff <= 0) {
                        this.timeRemaining = 'Expired';
                        return;
                    }

                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);
                    this.timeRemaining = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                    // Auto-extend when 2 minutes left
                    if (minutes === 2 && seconds === 0) {
                        this.extendLock();
                    }
                },

                async extendLock() {
                    try {
                        const response = await fetch('{{ route("call-queue.leads.extend-lock", $lead) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.lockExpiresAt = data.expires_at;
                            showToast('Lock extended by 15 minutes', 'success');
                        }
                    } catch (error) {
                        console.error('Error extending lock:', error);
                    }
                },

                selectActivityType(type) {
                    this.activityData.activity_type = type;
                    if (type === 'call') {
                        this.currentStep = 'call_outcome';
                    } else {
                        this.currentStep = 'details';
                    }
                },

                selectCallOutcome(outcome) {
                    this.activityData.call_outcome = outcome;
                    
                    // Start timer and open details if answered
                    if (outcome === 'answered') {
                        this.startCallTimer();
                        this.currentStep = 'details'; // Automatically open details
                    } else {
                        // For other outcomes, move to details immediately
                        this.currentStep = 'details';
                    }
                },

                startCallTimer() {
                    this.callInProgress = true;
                    this.callStartTime = new Date();
                    this.activityData.call_started_at = this.callStartTime.toISOString();
                    
                    this.callTimer = setInterval(() => {
                        const now = new Date();
                        const diff = Math.floor((now - this.callStartTime) / 1000);
                        const minutes = Math.floor(diff / 60);
                        const seconds = diff % 60;
                        this.callDuration = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        this.activityData.call_duration = diff;
                    }, 1000);
                },

                endCall() {
                    if (this.callTimer) {
                        clearInterval(this.callTimer);
                        this.callTimer = null;
                    }
                    this.callInProgress = false;
                    this.activityData.call_ended_at = new Date().toISOString();
                    this.currentStep = 'details';
                },

                async submitActivity() {
                    try {
                        const response = await fetch('{{ route("call-queue.leads.activity", $lead) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.activityData)
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            showToast('Activity recorded successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = data.next_lead_url;
                            }, 1000);
                        } else {
                            showToast(data.message || 'Error recording activity', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('An error occurred. Please try again.', 'error');
                    }
                },

                async skipLead() {
                    if (!confirm('Are you sure you want to skip this lead?')) return;

                    try {
                        const response = await fetch('{{ route("call-queue.leads.skip", $lead) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.location.href = data.next_lead_url;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            }
        }
    </script>
</x-dashboard-layout>
