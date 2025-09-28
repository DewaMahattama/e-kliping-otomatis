<!-- resources/views/components/header.blade.php -->
<header class="flex items-center justify-between px-4 py-3 bg-white shadow-md border-b border-gray-200">
    <div class="flex items-center space-x-4">
        <!-- Menu Hamburger -->
        <button class="text-gray-700 focus:outline-none md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/logo-ekliping.png') }}" alt="Logo" class="h-8 w-auto">
            <span class="text-lg font-bold text-gray-800">E KLIPING</span>
        </div>

        <!-- Home Icon -->
        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-red-600">
            <svg class="w-6 h-6 ml-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2v-4H9v4a2 2 0 01-2 2H3a2 2 0 01-2-2z"></path>
            </svg>
        </a>
    </div>
</header>
