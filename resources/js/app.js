import './bootstrap';

// Auto-format Rupiah / Mata Uang
function formatRupiah(value) {
    if (value === null || value === undefined || value === '') return '';
    let str = value.toString().trim();
    
    // Hilangkan sufiks desimal database .00 jika ada
    str = str.replace(/\.00$/, '');
    
    let numberString = str.replace(/[^0-9]/g, '');
    if (!numberString) return '';
    return new Intl.NumberFormat('id-ID').format(numberString);
}

function cleanRupiah(value) {
    if (value === null || value === undefined || value === '') return '';
    let str = value.toString().trim().replace(/\.00$/, '');
    return str.replace(/[^0-9]/g, '');
}

function initCurrencyFormatters() {
    const selector = '.input-currency, [data-currency], input[name^="harga"], input[name="harga_satuan"], input[name="nominal_pengeluaran"], input[name="harga_toping"]';
    const inputs = document.querySelectorAll(selector);

    inputs.forEach(input => {
        if (input.dataset.currencyBound) return;
        input.dataset.currencyBound = 'true';

        // Format nilai awal jika ada
        if (input.value) {
            input.value = formatRupiah(input.value);
        }

        input.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            const originalLength = this.value.length;
            
            this.value = formatRupiah(this.value);
            
            const newLength = this.value.length;
            const diff = newLength - originalLength;
            this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });

        // Pada saat form disubmit, bersihkan format titik agar menjadi angka murni untuk database
        const form = input.closest('form');
        if (form && !form.dataset.currencySubmitBound) {
            form.dataset.currencySubmitBound = 'true';
            form.addEventListener('submit', function() {
                const currencyInputs = form.querySelectorAll(selector);
                currencyInputs.forEach(curInput => {
                    curInput.value = cleanRupiah(curInput.value);
                });
            });
        }
    });
}

// Proteksi Global Anti Double-Submit (Mencegah Lag / Klik Ganda Tersimpan 2 Kali)
function initAntiDoubleSubmit() {
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // Abaikan form yang mengontrol AJAX khusus sendiri
        if (form.id === 'formCheckout' || form.dataset.ajax === 'true') return;

        if (form.dataset.submitting === 'true') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }

        form.dataset.submitting = 'true';

        const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitBtns.forEach(btn => {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            if (btn.tagName === 'BUTTON' && !btn.querySelector('.fa-spinner')) {
                btn.dataset.originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Menyimpan...';
            }
        });

        // Reset proteksi setelah 7 detik jika halaman tidak berpindah (misal ada error validasi client)
        setTimeout(() => {
            form.dataset.submitting = 'false';
            submitBtns.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                if (btn.dataset.originalContent) {
                    btn.innerHTML = btn.dataset.originalContent;
                }
            });
        }, 7000);
    }, true);
}

document.addEventListener('DOMContentLoaded', () => {
    initCurrencyFormatters();
    initAntiDoubleSubmit();
});

window.initCurrencyFormatters = initCurrencyFormatters;
window.initAntiDoubleSubmit = initAntiDoubleSubmit;
window.formatRupiah = formatRupiah;
window.cleanRupiah = cleanRupiah;
