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
    if (btnAdd) {
        btnAdd.addEventListener('click', () => {
            const rowCount = document.querySelectorAll('.item-row').length;
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td>
                    <input type="text" name="items[${rowCount}][description]" class="table-input input-desc" placeholder="Description of Service / Product" required>
                    <span class="view-only" style="color: black !important; font-weight: 500; text-align: left;"></span>
                </td>
                <td class="col-qty">
                    <input type="number" name="items[${rowCount}][quantity]" class="table-input input-qty" value="1" min="1" required>
                    <span class="view-only" style="color: black !important; display: block; text-align: center;"></span>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][price]" class="table-input input-price" placeholder="Unit Price" required>
                    <span class="view-only" style="color: black !important;"></span>
                </td>
                <td class="col-total item-total">Rp 0</td>
                <td class="col-action">
                    <button type="button" class="btn-remove" title="Remove Row">×</button>
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
    }

    // Attach event listener to existing first row inputs
    document.querySelectorAll('.item-row input').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });
    
    const taxRateInput = document.getElementById('tax_rate_input');
    if (taxRateInput) {
        taxRateInput.addEventListener('input', calculateTotals);
    }
    
    const firstRemoveBtn = document.querySelector('.btn-remove');
    if (firstRemoveBtn) {
        firstRemoveBtn.addEventListener('click', function(e) {
            if(document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            } else {
                alert('At least 1 item is required.');
            }
        });
    }

    // Initial calculation
    calculateTotals();

    // Prepare for Preview/Export by copying input values to view-only spans
    function syncInputsForView() {
        // Handle normal inputs
        document.querySelectorAll('.info-input, .table-input').forEach(el => {
            const viewSpan = el.parentElement.querySelector('.view-only');
            if (viewSpan) {
                if (el.tagName === 'SELECT') {
                    const isPaid = el.value === 'Lunas';
                    viewSpan.innerHTML = isPaid 
                        ? '<span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;">Paid</span>'
                        : '<span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;">Unpaid</span>';
                } else if (el.name === 'tax_rate') {
                    viewSpan.textContent = el.value + '%';
                } else if (el.classList.contains('input-price')) {
                    viewSpan.textContent = formatCurrency(parseFloat(el.value) || 0);
                } else if (el.name === 'date') {
                    if (el.value) {
                        const dateObj = new Date(el.value);
                        if (!isNaN(dateObj.getTime())) {
                            const options = { day: 'numeric', month: 'short', year: 'numeric' };
                            viewSpan.textContent = dateObj.toLocaleDateString('en-US', options);
                        } else {
                            viewSpan.textContent = el.value;
                        }
                    } else {
                        viewSpan.textContent = '-';
                    }
                } else {
                    viewSpan.textContent = el.value;
                }
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
            
            const pdf = new jspdf.jsPDF('p', 'mm', 'a5');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            
            const invNumberEl = document.querySelector('input[name="invoice_number"]');
            const invNumber = invNumberEl ? invNumberEl.value : 'Export';
            pdf.save('Invoice-' + invNumber + '.pdf');
            
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
            
            const invNumberEl = document.querySelector('input[name="invoice_number"]');
            const invNumber = invNumberEl ? invNumberEl.value : 'Export';
            
            const link = document.createElement('a');
            link.download = 'Invoice-' + invNumber + '.jpg';
            link.href = canvas.toDataURL('image/jpeg', 0.9);
            link.click();
            
            document.body.classList.remove('export-mode');
            document.body.classList.add('preview-mode');
        });
    }

    // Generate QR Code
    const qrCanvas = document.getElementById('invoice-qr');
    const sigCanvas = document.getElementById('signature-qr');
    const tokenEl = document.getElementById('invoice-token');
    const logoEl = document.getElementById('logo-base64');
    const logoBase64 = logoEl ? logoEl.value : '';

    if (qrCanvas || sigCanvas) {
        const invoiceNumberInput = document.querySelector('input[name="invoice_number"]');
        
        const drawQRWithLogo = (canvasEl, urlValue) => {
            new QRious({
                element: canvasEl,
                value: urlValue,
                size: 200,
                level: 'H'
            });

            if (logoBase64) {
                const ctx = canvasEl.getContext('2d');
                const img = new Image();
                img.src = logoBase64;
                img.onload = function() {
                    const logoSize = canvasEl.width * 0.22;
                    const x = (canvasEl.width - logoSize) / 2;
                    const y = (canvasEl.height - logoSize) / 2;

                    ctx.fillStyle = '#ffffff';
                    ctx.beginPath();
                    ctx.arc(canvasEl.width / 2, canvasEl.height / 2, (logoSize / 2) + 2, 0, 2 * Math.PI);
                    ctx.fill();

                    ctx.drawImage(img, x, y, logoSize, logoSize);
                };
            }
        };

        const generateQRs = () => {
            const invoiceNumber = invoiceNumberInput ? invoiceNumberInput.value : 'TEMP';
            const token = tokenEl ? tokenEl.value : '';
            const baseUrl = window.location.origin;
            const qrUrl = `${baseUrl}/invoices/show/${invoiceNumber}?token=${token}`;
            
            if (qrCanvas) {
                drawQRWithLogo(qrCanvas, qrUrl);
            }
            if (sigCanvas) {
                drawQRWithLogo(sigCanvas, qrUrl);
            }
        };
        
        generateQRs();
        
        // Regenerate if invoice number changes
        if (invoiceNumberInput) {
            invoiceNumberInput.addEventListener('change', generateQRs);
            invoiceNumberInput.addEventListener('input', generateQRs);
        }
    }
});
