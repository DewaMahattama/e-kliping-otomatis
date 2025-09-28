<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'e-Kliping Kabupaten Buleleng')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-poppins flex flex-col min-h-screen m-0">

    <!-- Overlay untuk sidebar -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside
        id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-md z-50 transform -translate-x-full transition-transform duration-300 ease-in-out"
    >
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo-ekliping.png') }}" alt="Logo" class="max-h-32 max-w-32" />
            </div>
            <button onclick="toggleSidebar()" class="text-xl font-bold text-black">&times;</button>
        </div>

        <nav class="py-2 px-2 space-y-2">
            <!-- Dashboard link -->
            <a
                href="{{ route('/') }}"
                class="block w-full px-2 py-2 rounded hover:bg-gray-200 font-medium text-gray-600"
            >Dashboard</a>

            <!-- Kelompok: Kliping, Analisis Sentimen, Scraping (Dinamis) -->
            <div id="tools-section" class="space-y-1" style="display:none;">
                <button
                    class="w-full px-2 py-2 rounded bg-white text-gray-600 font-medium hover:bg-gray-200 flex justify-between items-center"
                    onclick="toggleTools()"
                >
                    <span>PKL</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="tools-dropdown" class="space-y-1 pl-4" style="display:none;">
                    <!-- Analisis Sentimen link -->
                    <a
                        href="{{ url('/sentiment') }}"
                        class="block w-full px-2 py-2 rounded hover:bg-gray-200 font-medium text-gray-600"
                    >Analisis Sentimen</a>

                    <!-- Scraping Berita link -->
                    <a
                        href="{{ url('/scrapping') }}"
                        class="block w-full px-2 py-2 rounded hover:bg-gray-200 font-medium text-gray-600"
                    >Scraping</a>

                    <!-- Kliping link -->
                    <a
                        href="{{ url('/kliping') }}"
                        class="block w-full px-2 py-2 rounded hover:bg-gray-200 font-medium text-gray-600"
                    >Kliping</a>

                    <!-- Generate Kliping link -->
                    <a
                        href="{{ url('/klipings') }}"
                        class="block w-full px-2 py-2 rounded hover:bg-gray-200 font-medium text-gray-600"
                    >Generate Kliping</a>
                </div>
            </div>
        </nav>
    </aside>

    <script>
        // Fungsi untuk memeriksa apakah ada link di dalam kelompok Tools
        function checkToolsVisibility() {
            const toolsSection = document.getElementById('tools-section');
            const toolsLinks = document.querySelectorAll('#tools-section a');
            
            // Jika ada link di dalam kelompok Tools, tampilkan tombol "Tools"
            if (toolsLinks.length > 0) {
                toolsSection.style.display = 'block';
            }
        }

        // Fungsi untuk membuka/tutup dropdown Tools
        function toggleTools() {
            const dropdown = document.getElementById('tools-dropdown');
            dropdown.style.display = (dropdown.style.display === 'none' || dropdown.style.display === '') ? 'block' : 'none';
        }

        // Cek ketersediaan link di dalam grup Tools saat halaman dimuat
        document.addEventListener('DOMContentLoaded', checkToolsVisibility);
    </script>


    <!-- Header -->
    <header class="shadow-sm p-3 mb-2 flex justify-between items-center sticky top-0 z-30 bg-white w-full">
        <div class="flex items-center space-x-4">
            <!-- Button Sidebar Toggle -->
            <button onclick="toggleSidebar()" class="text-2xl font-bold text-gray-600">&#9776;</button>

            <!-- Logo dan Icon Home berdampingan -->
            <div class="flex items-center space-x-3">
                <!-- Logo -->
                <a href="{{ route('/') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo-ekliping.png') }}" alt="Logo" class="h-8 w-auto" />
                </a>

                <!-- Icon Home -->
                <a href="{{ route('/') }}" class="text-gray-600 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="w-6 h-6">
                        <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                        <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Right-side placeholder (optional) -->
        <div>
            <!-- Add right-side icons or user menu here if needed -->
        </div>
    </header>

    <!-- Konten -->
    <main class="flex-1 mt-4 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 py-4 text-center">
        <div class="max-w-7xl mx-auto px-4 text-sm">
            <span class="text-black font-semibold text-[13pt]">Copyright &copy; {{ date('Y') }}</span>
            <a
                href="https://kominfosanti.bulelengkab.go.id/"
                class="hover:text-red-700 text-red-800 font-semibold text-[13pt]"
                >Dinas Kominfo Santi Kabupaten Buleleng</a
            >
        </div>
    </footer>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
