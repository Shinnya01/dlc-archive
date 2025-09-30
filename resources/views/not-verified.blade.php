<x-layouts.guest>
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-50 p-6">
    <div class="bg-white shadow-lg rounded-2xl p-10 w-full max-w-md text-center">
        
        <p class="text-3xl lg:text-7xl font-extrabold text-red-600 mb-8">
            NOT VERIFIED
        </p>

        <p class="text-gray-700 mb-6">
            Your account has not been verified yet. <br>
            Please wait for admin approval, we'll notify you.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-md flex items-center justify-center gap-2 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
                </svg>
                Log Out
            </button>
        </form>
    </div>
</div>
</x-layouts.guest>