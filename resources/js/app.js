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

document.addEventListener('DOMContentLoaded', initCurrencyFormatters);
window.initCurrencyFormatters = initCurrencyFormatters;
window.formatRupiah = formatRupiah;
window.cleanRupiah = cleanRupiah;
