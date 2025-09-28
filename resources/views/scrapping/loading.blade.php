@extends('layouts.app')

@section('title', 'Proses Scraping')

@section('content')
<div class="max-w-3xl mx-auto p-6 text-center">
    <h1 class="text-2xl font-bold mb-4">Loading...</h1>
    <p id="loading-text" class="mb-6 text-gray-600 italic">
        Memulai proses...
    </p>

    <!-- Loader Spinner -->
    <div class="flex justify-center items-center opacity-0 animate-fadeIn">
        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent border-solid rounded-full animate-spin shadow-md"></div>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-6">
        <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-in-out" style="width: 10%"></div>
    </div>
</div>

<style>
@keyframes fadeIn {
    to { opacity: 1; }
}
.animate-fadeIn {
    animation: fadeIn 1s ease-out forwards;
}
</style>

<script>
    const portal = "{{ $portal }}";
    const batchId = "{{ $batchId }}";

    let steps = [
        "Menghubungkan ke server...",
        "Mengambil daftar artikel...",
        "Menganalisis isi berita...",
        "Menyelesaikan proses scraping..."
    ];
    let stepIndex = 0;
    let progress = 10;

    function updateText() {
        const text = steps[stepIndex % steps.length];
        document.getElementById("loading-text").innerText = text;
        stepIndex++;
        setTimeout(updateText, 4000);
    }

    function increaseProgress() {
        progress = Math.min(progress + 10, 95);
        document.getElementById("progress-bar").style.width = progress + "%";
        setTimeout(increaseProgress, 4000);
    }

    function checkStatus() {
        fetch("{{ route('scrapping.checkStatus') }}?batch_id=" + batchId)
            .then(res => res.json())
            .then(data => {
                if (data.done) {
                    document.getElementById("loading-text").innerText = "Selesai! Mengarahkan ke hasil...";
                    document.getElementById("progress-bar").style.width = "100%";
                    setTimeout(() => {
                        window.location.href = "{{ route('scrapping.results') }}";
                    }, 1000);
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(error => {
                document.getElementById("loading-text").innerText = "⚠️ Terjadi kesalahan saat memeriksa status. Silakan muat ulang.";
                console.error("Status check error:", error);
            });
    }

    setTimeout(checkStatus, 3000);
    updateText();
    increaseProgress();
</script>
@endsection
