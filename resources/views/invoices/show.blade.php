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
        <div class="invoice-title">
            <h2 style="color: black !important;">NOTA</h2>
            <div style="font-size: 1rem; color: black !important; margin-top: 0.5rem; font-weight: 500;">
                No: {{ $invoice->invoice_number }}
            </div>
        </div>
    </div>

    <div class="customer-info" style="margin-bottom: 2rem;">
        <div class="info-group">
            <label style="color: black !important;">Diberikan Kepada:</label>
            <div style="padding-top: 5px; font-size: 1.1rem; font-weight: 600; color: black !important;">{{ $invoice->customer_name }}</div>
        </div>
        
        <div class="info-group" style="align-items: flex-end;">
            <label style="color: black !important;">Tanggal:</label>
            <div style="padding-top: 5px; color: black !important;">{{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</div>
        </div>
        
        <div class="info-group" style="align-items: flex-end;">
            <label style="color: black !important;">Status Pembayaran:</label>
            <div style="padding-top: 5px;">
                @if($invoice->status == 'Lunas')
                    <span style="background: rgba(74, 222, 128, 0.2); color: #166534; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">Lunas</span>
                @else
                    <span style="background: rgba(239, 68, 68, 0.2); color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">Belum Lunas</span>
                @endif
            </div>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th class="col-desc" style="background: #f1f5f9 !important; color: black !important;">Deskripsi Barang / Jasa</th>
                <th class="col-qty" style="background: #f1f5f9 !important; color: black !important; text-align: center;">Jumlah</th>
                <th class="col-price" style="background: #f1f5f9 !important; color: black !important;">Harga Satuan</th>
                <th class="col-total" style="background: #f1f5f9 !important; color: black !important; text-align: right; padding-right: 1rem !important;">Total Harga</th>
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
                    <span style="font-weight: 600;" class="qr-title">Validasi Online</span><br>
                    Scan QR ini untuk memverifikasi keaslian nota secara online.
                </div>
            </div>

            <!-- Payment Info (shown if payment is pending) -->
            @if($invoice->status !== 'Lunas')
            <div style="margin-top: 0.5rem; padding: 0.75rem 1rem; border-radius: 8px; background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; font-size: 0.8rem; line-height: 1.5; max-width: 320px;">
                <strong>Informasi Pembayaran:</strong><br>
                Transfer ke rekening resmi perusahaan:<br>
                <strong>Bank Mandiri:</strong> 1430028062501<br>
                <strong>A.N.:</strong> PT Rayan Smart Kreatif<br>
                <em>Konfirmasikan bukti transfer ke admin via WA.</em>
            </div>
            @endif
        </div>

        <div class="summary-content" style="margin-top: 0;">
            <div class="summary-row" style="color: black !important;">
                <span>Subtotal</span>
                <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row" style="color: black !important;">
                <span>Pajak ({{ $invoice->tax_rate }}%)</span>
                <span>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row grand-total" style="color: black !important; border-top: 2px solid #e2e8f0 !important;">
                <span>Total Tagihan</span>
                <span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
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
            
            const pdf = new jspdf.jsPDF('p', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save('Nota-' + '{{ $invoice->invoice_number }}' + '.pdf');
            
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
            link.download = 'Nota-' + '{{ $invoice->invoice_number }}' + '.jpg';
            link.href = canvas.toDataURL('image/jpeg', 0.9);
            link.click();
            
            element.style.marginTop = '60px';
            document.body.classList.remove('export-mode');
        });
    }
});
</script>
@endsection