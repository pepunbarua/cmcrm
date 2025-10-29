<x-app-layout title="Login - CheckMate Events">
    <x-toast />
    
    <x-auth-card>
        <x-slot:logo>
            <div class="flex justify-center">
                <img src="{{ asset('assets/images/logo.webp') }}" alt="CheckMate Events" class="h-24 w-auto">
            </div>
        </x-slot:logo>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome Back!</h1>
            <p class="text-white/70">Sign in to your CheckMate Events account</p>
        </div>

        <form id="loginForm" method="POST" class="space-y-1">
            @csrf

            <div id="errorMessage" class="hidden mb-4 p-3 rounded-lg bg-red-500/20 border border-red-500/50 text-red-300 text-sm"></div>

            <x-input 
                name="email" 
                type="email" 
                label="Email Address"
                placeholder="Enter your email"
                required
                autofocus
            />

            <x-input 
                name="password" 
                type="password" 
                label="Password"
                placeholder="Enter your password"
                required
            />

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-white/5 text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                    <span class="ml-2 text-sm text-white/80">Remember me</span>
                </label>
                <a href="#" class="text-sm text-purple-400 hover:text-purple-300 transition">Forgot password?</a>
            </div>

            <x-button type="submit" variant="primary" id="loginButton">
                <span id="buttonText">Sign In</span>
                <span id="buttonLoader" class="hidden">
                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </x-button>

            <div class="mt-6 text-center">
                <p class="text-white/60 text-sm">
                    Don't have an account? 
                    <a href="#" class="text-purple-400 hover:text-purple-300 font-semibold transition">Create Account</a>
                </p>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-white/10">
            <p class="text-center text-xs text-white/50">
                CheckMate Events - Photography & Cinematography CRM
            </p>
            <p class="text-center text-xs text-white/40 mt-1">
                Powered by <span class="text-purple-400 font-semibold">Devswire</span>
            </p>
        </div>
    </x-auth-card>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const button = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            const buttonLoader = document.getElementById('buttonLoader');
            const errorDiv = document.getElementById('errorMessage');
            
            // Disable button and show loader
            button.disabled = true;
            buttonText.classList.add('hidden');
            buttonLoader.classList.remove('hidden');
            errorDiv.classList.add('hidden');
            
            try {
                const formData = new FormData(form);
                const response = await fetch('{{ route('login.post') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showToast(data.message, 'error');
                    errorDiv.textContent = data.message;
                    errorDiv.classList.remove('hidden');
                    button.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonLoader.classList.add('hidden');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
                button.disabled = false;
                buttonText.classList.remove('hidden');
                buttonLoader.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
