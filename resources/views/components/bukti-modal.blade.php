{{-- Modal preview file bukti (gambar/PDF), dipakai lewat showBuktiModal(url) --}}
<div id="buktiModalOverlay"
     class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4"
     onclick="if(event.target === this) closeBuktiModal()">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] flex flex-col overflow-hidden">
        <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-700">Preview Bukti</p>
            <div class="flex items-center gap-3">
                <a id="buktiModalDownload" href="#" target="_blank" download
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Unduh
                </a>
                <button type="button" onclick="closeBuktiModal()" class="text-gray-400 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
        <div id="buktiModalBody" class="flex-1 overflow-auto bg-gray-50 flex items-center justify-center p-3">
            {{-- konten (img/iframe) di-inject lewat JS --}}
        </div>
    </div>
</div>

<script>
function showBuktiModal(url) {
    const overlay = document.getElementById('buktiModalOverlay');
    const body = document.getElementById('buktiModalBody');
    const downloadLink = document.getElementById('buktiModalDownload');

    downloadLink.href = url;

    const isPdf = url.toLowerCase().split('?')[0].endsWith('.pdf');
    if (isPdf) {
        body.innerHTML = `<iframe src="${url}" class="w-full h-[70vh] rounded"></iframe>`;
    } else {
        body.innerHTML = `<img src="${url}" class="max-w-full max-h-[70vh] object-contain rounded">`;
    }

    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBuktiModal() {
    document.getElementById('buktiModalOverlay').classList.add('hidden');
    document.getElementById('buktiModalBody').innerHTML = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeBuktiModal();
});
</script>
