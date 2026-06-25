@extends('layouts.app')

@section('content')
@php
    $logoPath = public_path('images/logorayan.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<div class="preview-actions" style="display: flex;">
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary" style="margin: 0; text-decoration: none;">Daftar Nota</a>
    <button type="button" id="btn-print" class="btn btn-secondary" style="margin: 0;">Cetak (Print)</button>
    <button type="button" id="btn-export-pdf" class="btn btn-pdf" style="margin: 0;">Unduh PDF</button>
    <button type="button" id="btn-export-jpg" class="btn btn-jpg" style="margin: 0;">Unduh JPG</button>
    <a href="https://api.whatsapp.com/send?text=Halo,%20berikut%20adalah%20link%20nota%20resmi%20dari%20PT%20Rayan%20Smart%20Kreatif%20dengan%20nomor%20{{ $invoice->invoice_number }}:%20{{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn" style="margin: 0; background: linear-gradient(135deg, #25D366, #128C7E); color: white; text-decoration: none; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);">Bagikan ke WA</a>
</div>

<div class="glass-container" id="invoice-container" style="margin-top: 60px; background: white !important; box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;">
    <!-- Watermark Background -->
    <div class="watermark">
        <img src="{{ $logoBase64 }}" alt="Watermark PT Rayan Smart Kreatif">
    </div>
    
    <div class="invoice-header">
        <div class="company-info" style="display: flex; align-items: center; gap: 1.5rem;">
            <img src="{{ $logoBase64 }}" alt="Logo PT Rayan Smart Kreatif" style="width: 90px; height: 90px; object-fit: contain;">
            <div>
                <h1 style="color: black !important;">PT Rayan Smart Kreatif</h1>
                <p style="color: black !important;">Dusun Jalen 1, Desa Setail, Kec. Genteng<br>
                   Kab. Banyuwangi, Prov. Jawa Timur 68465<br>
                   Email: rayansmartkreatif@gmail.com<br>
                   Website: www.rayan.web.id</p>
            </div>
        </div>
        <div class="invoice-title" style="text-align: right;">
            <h2 style="font-size: 2.5rem; font-weight: 800; letter-spacing: 2px; color: black !important; margin-bottom: 0.5rem;">INVOICE</h2>
            <div style="font-size: 1.1rem; font-weight: 700; color: black !important;">
                INVOICE NO: {{ $invoice->invoice_number }}
            </div>
        </div>
    </div>

    <div class="customer-info" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid #e2e8f0;">
        <!-- Left Column: Customer details -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: #475569;">BILLED TO:</label>
            <div style="font-size: 1.2rem; font-weight: 700; color: black !important; text-transform: uppercase;">{{ $invoice->customer_name }}</div>
        </div>
        
        <!-- Right Column: Invoice metadata -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start;">
            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: #475569;">DATE:</label>
                <div style="font-size: 1rem; color: black !important; font-weight: 500;">{{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</div>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: #475569;">STATUS:</label>
                <div>
                    @if($invoice->status == 'Lunas')
                        <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-block;">Paid</span>
                    @else
                        <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-block;">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th class="col-desc" style="background: #f1f5f9 !important; color: black !important;">Description of Goods / Services</th>
                <th class="col-qty" style="background: #f1f5f9 !important; color: black !important; text-align: center;">Qty</th>
                <th class="col-price" style="background: #f1f5f9 !important; color: black !important;">Unit Price</th>
                <th class="col-total" style="background: #f1f5f9 !important; color: black !important; text-align: right; padding-right: 1rem !important;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td style="color: black !important; border-bottom: 1px solid #e2e8f0 !important;">{{ $item->description }}</td>
                <td class="col-qty" style="color: black !important; border-bottom: 1px solid #e2e8f0 !important; text-align: center;">{{ $item->quantity }}</td>
                <td style="color: black !important; border-bottom: 1px solid #e2e8f0 !important;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="col-total" style="color: black !important; border-bottom: 1px solid #e2e8f0 !important; text-align: right; padding-right: 1rem !important;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="invoice-summary" style="justify-content: space-between; align-items: flex-end; gap: 2rem; border-top: 2px solid #e2e8f0 !important;">
        <!-- QR Code Section -->
        <div id="qr-code-container" style="display: flex; flex-direction: column; gap: 1rem; text-align: left; padding: 0.5rem; border-radius: 10px;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: white; padding: 6px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: inline-block;">
                    <canvas id="invoice-qr" style="width: 90px; height: 90px; display: block;"></canvas>
                </div>
                <div style="font-size: 0.8rem; color: black !important; max-width: 180px; line-height: 1.4;">
                    <span style="font-weight: 600;" class="qr-title">Online Validation</span><br>
                    Scan this QR to verify invoice authenticity online.
                </div>
            </div>

            <!-- Payment Info (shown if payment is pending) -->
            @if($invoice->status !== 'Lunas')
            <div style="margin-top: 0.5rem; padding: 0.75rem 1rem; border-radius: 8px; background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; font-size: 0.8rem; line-height: 1.5; max-width: 320px;">
                <strong>Payment Information:</strong><br>
                Transfer to the official company account:<br>
                <strong>Bank Mandiri:</strong> 1430028062501<br>
                <strong>Beneficiary:</strong> PT Rayan Smart Kreatif<br>
                <em>Confirm your payment with transfer slip to admin via WA.</em>
            </div>
            @endif
        </div>

        <div class="summary-content" style="margin-top: 0; display: flex; flex-direction: column; gap: 1rem; align-items: flex-end;">
            <div style="width: 100%;">
                <div class="summary-row" style="color: black !important;">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row" style="color: black !important;">
                    <span>Tax ({{ $invoice->tax_rate }}%)</span>
                    <span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row grand-total" style="color: black !important; border-top: 2px solid #e2e8f0 !important;">
                    <span>Total Due</span>
                    <span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
            </div>
            <!-- Signature Block -->
            <div class="signature-block" style="text-align: center; width: 200px; margin-top: 1.5rem;">
                <p style="font-size: 0.8rem; color: black !important; margin-bottom: 2.5rem; font-weight: 500;">Authorized Signature,</p>
                <p style="font-size: 0.85rem; font-weight: 700; color: black !important; border-top: 1px dashed #000; padding-top: 0.3rem;">PT Rayan Smart Kreatif</p>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR Code pointing to this page's URL
    const qrCanvas = document.getElementById('invoice-qr');
    if (qrCanvas) {
        new QRious({
            element: qrCanvas,
            value: window.location.href,
            size: 180,
            level: 'H'
        });
    }

    // Print
    const btnPrint = document.getElementById('btn-print');
    if (btnPrint) {
        btnPrint.addEventListener('click', () => {
            window.print();
        });
    }

    // Export PDF
    const btnPdf = document.getElementById('btn-export-pdf');
    if (btnPdf) {
        btnPdf.addEventListener('click', async () => {
            document.body.classList.add('export-mode');
            
            const element = document.getElementById('invoice-container');
            element.style.marginTop = '0px';
            
            const canvas = await html2canvas(element, { scale: 2, useCORS: true });
            const imgData = canvas.toDataURL('image/png');
            
            const pdf = new jspdf.jsPDF('p', 'mm', 'a5');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save('Invoice-' + '{{ $invoice->invoice_number }}' + '.pdf');
            
            element.style.marginTop = '60px';
            document.body.classList.remove('export-mode');
        });
    }

    // Export JPG
    const btnJpg = document.getElementById('btn-export-jpg');
    if (btnJpg) {
        btnJpg.addEventListener('click', async () => {
            document.body.classList.add('export-mode');
            
            const element = document.getElementById('invoice-container');
            element.style.marginTop = '0px';
            
            const canvas = await html2canvas(element, { scale: 2, useCORS: true });
            
            const link = document.createElement('a');
            link.download = 'Invoice-' + '{{ $invoice->invoice_number }}' + '.jpg';
            link.href = canvas.toDataURL('image/jpeg', 0.9);
            link.click();
            
            element.style.marginTop = '60px';
            document.body.classList.remove('export-mode');
        });
    }
});
</script>
@endsection