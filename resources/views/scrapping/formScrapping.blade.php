@extends('layouts.app')

@section('title', 'Scraping | e-Kliping Kabupaten Buleleng')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 relative">
    <h1 class="text-3xl font-semibold text-gray-800 mb-8 border-b border-gray-300 pb-4 text-center">
      Scraping Portal Berita
    </h1>

    @if(session('status'))
      <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 border border-green-300 text-base text-center">
        {{ session('status') }}
      </div>
    @endif

    <form id="form-scraping" action="{{ route('scrapping.result') }}" method="POST" class="space-y-8">
      @csrf

      {{-- Pilihan Portal --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="portal" class="col-span-3 text-gray-900 font-semibold">
          Pilih Portal Berita
        </label>

        <select 
          name="portal" 
          id="portal"
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        >
          <option value="balipost">Bali Post</option>
          <option value="balipuspanews">Balipuspanews</option>
          <option value="koranbuleleng">Koran Buleleng</option>
          <option value="fajarbali">Fajar Bali</option>
          <option value="wartabali">Warta Bali</option>
        </select>
      </div>

      {{-- Jumlah Halaman --}}
      <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-300 pb-4">
        <label for="pages" class="col-span-3 text-gray-900 font-semibold">
          Jumlah Halaman
        </label>

        <input 
          type="number" 
          name="pages" 
          id="pages" 
          value="1" 
          min="1" 
          step="1"
          placeholder="Masukkan jumlah halaman"
          required
          class="col-span-9 border border-gray-300 rounded-lg p-3 text-gray-800 text-base focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
      </div>

      {{-- Tombol Submit --}}
      <div class="flex justify-end pt-4">
        <button 
          type="submit" 
          class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-500 transition"
        >
          Submit
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Loading --}}
<div id="modal-loading" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
  <div class="bg-white p-6 rounded shadow-lg max-w-md w-full text-center">
    <h2 class="text-xl font-bold mb-4">Loading...</h2>
    <p id="loading-text" class="mb-6 italic text-gray-600">Memulai proses...</p>
    <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-6">
      <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-in-out" style="width: 10%"></div>
    </div>
  </div>
</div>

<script>
  const modal = document.getElementById('modal-loading');
  const form = document.getElementById('form-scraping');
  const loadingText = document.getElementById('loading-text');
  const progressBar = document.getElementById('progress-bar');

  // Show modal (flex + remove hidden)
  function showModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // Hide modal (hidden + remove flex)
  function hideModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  // Update loading text steps
  let steps = [
    "Menghubungkan ke server...",
    "Mengambil daftar artikel...",
    "Menganalisis isi berita...",
    "Menyelesaikan proses scraping..."
  ];
  let stepIndex = 0;
  let progress = 10;

  function updateText() {
    loadingText.textContent = steps[stepIndex % steps.length];
    stepIndex++;
    setTimeout(updateText, 4000);
  }

  function increaseProgress() {
    progress = Math.min(progress + 10, 95);
    progressBar.style.width = progress + "%";
    setTimeout(increaseProgress, 4000);
  }

  // Polling cek status scraping
  function startPolling(batchId) {
    updateText();
    increaseProgress();

    function checkStatus() {
      fetch("{{ route('scrapping.checkStatus') }}?batch_id=" + batchId)
        .then(res => res.json())
        .then(data => {
          if (data.done) {
            loadingText.textContent = "Selesai! Mengarahkan ke hasil...";
            progressBar.style.width = "100%";
            setTimeout(() => {
              window.location.href = "{{ route('scrapping.results') }}";
            }, 1000);
          } else {
            setTimeout(checkStatus, 3000);
          }
        })
        .catch(() => {
          loadingText.textContent = "⚠️ Terjadi kesalahan saat memeriksa status.";
        });
    }

    setTimeout(checkStatus, 3000);
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    showModal();
    loadingText.textContent = 'Memulai proses...';
    progressBar.style.width = '10%';

    const formData = new FormData(this);

    fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(res => res.json())
    .then(data => {
      if(data.success && data.batchId) {
        startPolling(data.batchId);
      } else {
        alert('Gagal memulai scraping');
        hideModal();
      }
    })
    .catch(() => {
      alert('Terjadi kesalahan saat mengirim data');
      hideModal();
    });
  });
</script>
@endsection
