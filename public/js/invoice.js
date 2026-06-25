document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('invoice-items');
    const btnAdd = document.getElementById('btn-add-item');
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const grandTotalEl = document.getElementById('grand-total');

    // Format currency to IDR
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    };

    // Calculate totals
    const calculateTotals = () => {
        let subtotal = 0;
        const rows = document.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const price = parseFloat(row.querySelector('.input-price').value) || 0;
            const total = qty * price;
            
            row.querySelector('.item-total').textContent = formatCurrency(total);
            subtotal += total;
        });

        const taxRateInput = document.getElementById('tax_rate_input');
        let taxRate = 11; // default fallback
        if (taxRateInput) {
            taxRate = parseFloat(taxRateInput.value) || 0;
            const taxLabel = document.getElementById('tax-label');
            if (taxLabel) taxLabel.textContent = taxRate;
        }

        const tax = subtotal * (taxRate / 100);
        const grandTotal = subtotal + tax;

        subtotalEl.textContent = formatCurrency(subtotal);
        taxEl.textContent = formatCurrency(tax);
        grandTotalEl.textContent = formatCurrency(grandTotal);
    };

    // Add new row
    btnAdd.addEventListener('click', () => {
        const rowCount = document.querySelectorAll('.item-row').length;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td><input type="text" name="items[${rowCount}][description]" class="table-input input-desc" placeholder="Nama Barang / Jasa" required></td>
            <td class="col-qty"><input type="number" name="items[${rowCount}][quantity]" class="table-input input-qty" value="1" min="1" required></td>
            <td><input type="number" name="items[${rowCount}][price]" class="table-input input-price" placeholder="Harga Satuan" required></td>
            <td class="col-total item-total">Rp 0</td>
            <td class="col-action">
                <button type="button" class="btn-remove" title="Hapus Baris">×</button>
            </td>
        `;
        tableBody.appendChild(tr);

        // Attach event listeners to new inputs
        const inputs = tr.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        // Attach event listener to new remove button
        tr.querySelector('.btn-remove').addEventListener('click', function() {
            tr.remove();
            calculateTotals();
        });
    });

    // Attach event listener to existing first row
    document.querySelectorAll('.item-row input').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });
    
    const taxRateInput = document.getElementById('tax_rate_input');
    if (taxRateInput) {
        taxRateInput.addEventListener('input', calculateTotals);
    }
    
    document.querySelector('.btn-remove').addEventListener('click', function(e) {
        if(document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('tr').remove();
            calculateTotals();
        } else {
            alert('Minimal harus ada 1 barang/jasa.');
        }
    });

    // Initial calculation
    calculateTotals();

    // Prepare for Preview/Export
    function syncInputsForView() {
        document.querySelectorAll('input, select').forEach(el => {
            if(el.tagName === 'SELECT') {
                el.setAttribute('data-value', el.options[el.selectedIndex].text);
            } else {
                el.setAttribute('value', el.value);
            }
        });
    }

    const btnPreview = document.getElementById('btn-preview');
    if(btnPreview) {
        btnPreview.addEventListener('click', () => {
            syncInputsForView();
            document.body.classList.add('preview-mode');
        });
    }

    const btnClosePreview = document.getElementById('btn-close-preview');
    if(btnClosePreview) {
        btnClosePreview.addEventListener('click', () => {
            document.body.classList.remove('preview-mode');
        });
    }

    const btnPrint = document.getElementById('btn-print');
    if(btnPrint) {
        btnPrint.addEventListener('click', () => {
            window.print();
        });
    }

    // Export PDF
    const btnPdf = document.getElementById('btn-export-pdf');
    if(btnPdf) {
        btnPdf.addEventListener('click', async () => {
            document.body.classList.remove('preview-mode');
            document.body.classList.add('export-mode');
            
            const element = document.getElementById('invoice-container');
            const canvas = await html2canvas(element, { scale: 2, useCORS: true });
            const imgData = canvas.toDataURL('image/png');
            
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save('Nota-' + (document.querySelector('input[name="invoice_number"]').value || 'Export') + '.pdf');
            
            document.body.classList.remove('export-mode');
            document.body.classList.add('preview-mode');
        });
    }

    // Export JPG
    const btnJpg = document.getElementById('btn-export-jpg');
    if(btnJpg) {
        btnJpg.addEventListener('click', async () => {
            document.body.classList.remove('preview-mode');
            document.body.classList.add('export-mode');
            
            const element = document.getElementById('invoice-container');
            const canvas = await html2canvas(element, { scale: 2, useCORS: true });
            
            const link = document.createElement('a');
            link.download = 'Nota-' + (document.querySelector('input[name="invoice_number"]').value || 'Export') + '.jpg';
            link.href = canvas.toDataURL('image/jpeg', 0.9);
            link.click();
            
            document.body.classList.remove('export-mode');
            document.body.classList.add('preview-mode');
        });
    }
});
