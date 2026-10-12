<div id="notif-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-3"></div>

<script>
// Buat fungsi menempel ke scope global window agar bisa dipanggil dari partial view mana saja
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('notif-container');
    if (!container) return;

    const id = Date.now();
    const isSuccess = type === 'success';
    const bgColor = isSuccess ? 'bg-green-50' : 'bg-red-50';
    const textColor = isSuccess ? 'text-green-800' : 'text-red-800';
    const borderColor = isSuccess ? 'border-green-100' : 'border-red-100';

    const toastHtml = `
        <div id="toast-${id}" class="flex items-center p-4 w-80 ${textColor} rounded-2xl ${bgColor} border ${borderColor} shadow-lg transition-all duration-500 transform translate-x-full opacity-0" role="alert">
            <div class="ms-3 text-sm font-bold">${message}</div>
            <button type="button" class="ms-auto p-1.5 hover:opacity-70" onclick="window.removeToast(${id})">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);

    // Kasih delay sedikit lebih lama (200ms) biar transisi Tailwind-nya sempat membaca state awal objek HTML
    setTimeout(() => {
        const toast = document.getElementById(`toast-${id}`);
        if (toast) {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }
    }, 200);

    // Hapus Otomatis setelah 4 detik
    setTimeout(() => window.removeToast(id), 4000);
}

window.removeToast = function(id) {
    const toast = document.getElementById(`toast-${id}`);
    if (toast) {
        toast.classList.add('opacity-0', 'scale-95');
        setTimeout(() => toast.remove(), 500);
    }
}

// GUNAKAN TRICK INI JON: Jalankan saat window benar-benar SELESAI me-load seluruh aset (termasuk CSS/JS)
window.addEventListener('load', function() {
    @if(session('success'))
        setTimeout(() => {
            window.showToast("{{ session('success') }}", 'success');
        }, 100);
    @endif

    @if(session('error'))
        setTimeout(() => {
            window.showToast("{{ session('error') }}", 'error');
        }, 100);
    @endif
});
</script>