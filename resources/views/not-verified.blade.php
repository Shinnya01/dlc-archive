<div class="flex flex-col items-center justify-center min-h-screen bg-gray-50 p-6">
    <p class="text-6xl md:text-9xl font-extrabold text-red-600 text-center mb-8">NOT VERIFIED</p>

    <p class="text-center text-gray-700 mb-6">
        Your account has not been verified yet. Please contact the administrator for assistance.
    </p>

    <form method="POST" action="{{ route('logout') }}" class="w-full max-w-xs">
        @csrf
        <button 
            type="submit" 
            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-md flex items-center justify-center gap-2"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
            </svg>
            Log Out
        </button>
    </form>
</div>