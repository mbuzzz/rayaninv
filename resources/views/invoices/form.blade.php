@extends('layouts.app')

@section('content')
@php
    $logoPath = public_path('images/logorayan.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<div class="preview-actions">
    <button type="button" id="btn-close-preview" class="btn btn-secondary" style="margin: 0;">Kembali Edit</button>
    <button type="button" id="btn-print" class="btn btn-secondary" style="margin: 0;">Cetak (Print)</button>
    <button type="button" id="btn-export-pdf" class="btn btn-pdf" style="margin: 0;">Unduh PDF</button>
    <button type="button" id="btn-export-jpg" class="btn btn-jpg" style="margin: 0;">Unduh JPG</button>
</div>

<form action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}" method="POST" id="invoice-form">
    @csrf
    <input type="hidden" id="invoice-token" value="{{ $token }}">
    @if(isset($invoice))
        @method('PUT')
    @endif

    <div class="glass-container" id="invoice-container">
        <!-- Watermark Background -->
        <div class="watermark">
            <img src="{{ $logoBase64 }}" alt="Watermark PT Rayan Smart Kreatif">
        </div>
        
        <div class="invoice-header">
            <div class="company-info" style="display: flex; align-items: center; gap: 1.5rem;">
                <img src="{{ $logoBase64 }}" alt="Logo PT Rayan Smart Kreatif" style="width: 90px; height: 90px; object-fit: contain;">
                <div>
                    <h1>PT Rayan Smart Kreatif</h1>
                    <p>Dusun Jalen 1, Desa Setail, Kec. Genteng<br>
                       Kab. Banyuwangi, Prov. Jawa Timur 68465<br>
                       Email: rayansmartkreatif@gmail.com<br>
                       Website: www.rayan.web.id</p>
                </div>
            </div>
            <div class="invoice-title" style="text-align: right;">
                <h2 style="font-size: 2.5rem; font-weight: 800; letter-spacing: 2px; color: var(--text-primary);">INVOICE</h2>
                <div style="font-size: 1.1rem; margin-top: 0.5rem; font-weight: 700; color: var(--primary-color);">
                    INVOICE NO: {{ $invoice->invoice_number ?? $nextNumber ?? '' }}
                    <input type="hidden" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number ?? $nextNumber ?? '') }}">
                </div>
                @error('invoice_number')
                    <div style="color: red; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="customer-info" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
            <!-- Left Column: Customer details -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="info-group">
                    <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: var(--primary-color);">BILLED TO:</label>
                    <input type="text" name="customer_name" class="info-input" placeholder="Client Name / Company" value="{{ old('customer_name', $invoice->customer_name ?? '') }}" style="font-weight: 600;" required>
                    @error('customer_name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <!-- Right Column: Invoice metadata -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; align-items: start;">
                <div class="info-group">
                    <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: var(--primary-color);">DATE:</label>
                    <input type="date" name="date" class="info-input" value="{{ old('date', $invoice->date ?? date('Y-m-d')) }}" required>
                </div>
                
                <div class="info-group">
                    <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: var(--primary-color);">STATUS:</label>
                    <select name="status" class="info-input" style="cursor: pointer; font-weight: 600;">
                        <option value="Belum Lunas" {{ (old('status', $invoice->status ?? '') == 'Belum Lunas') ? 'selected' : '' }}>Unpaid</option>
                        <option value="Lunas" {{ (old('status', $invoice->status ?? '') == 'Lunas') ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <div class="info-group" style="grid-column: span 2;">
                    <label style="font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; color: var(--primary-color);">TAX / VAT (%):</label>
                    <input type="number" step="0.01" min="0" max="100" id="tax_rate_input" name="tax_rate" class="info-input" value="{{ old('tax_rate', $invoice->tax_rate ?? 11) }}" required>
                </div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="col-desc">Description of Goods / Services</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-price">Unit Price</th>
                    <th class="col-total">Total Price</th>
                    <th class="col-action"></th>
                </tr>
            </thead>
            <tbody id="invoice-items">
                @php
                    $items = old('items', isset($invoice) ? $invoice->items : [ ['description'=>'', 'quantity'=>1, 'price'=>''] ]);
                @endphp

                @foreach($items as $index => $item)
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[{{ $index }}][description]" class="table-input input-desc" placeholder="Description of Service / Product" value="{{ is_array($item) ? ($item['description'] ?? '') : $item->description }}" required>
                    </td>
                    <td class="col-qty">
                        <input type="number" name="items[{{ $index }}][quantity]" class="table-input input-qty" value="{{ is_array($item) ? ($item['quantity'] ?? 1) : $item->quantity }}" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][price]" class="table-input input-price" placeholder="Unit Price" value="{{ is_array($item) ? ($item['price'] ?? '') : (float)$item->price }}" required>
                    </td>
                    <td class="col-total item-total">Rp 0</td>
                    <td class="col-action">
                        <button type="button" class="btn-remove" title="Hapus Baris">×</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" id="btn-add-item" class="btn-add">+ Add Item / Service</button>

        <div class="invoice-summary" style="justify-content: space-between; align-items: flex-end; gap: 2rem;">
            <!-- QR Code Section -->
            <div id="qr-code-container" style="display: flex; align-items: center; gap: 1rem; text-align: left; padding: 0.5rem; border-radius: 10px; background: rgba(255, 255, 255, 0.15);">
                <div style="background: white; padding: 6px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: inline-block;">
                    <canvas id="invoice-qr" style="width: 90px; height: 90px; display: block;"></canvas>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary); max-width: 180px; line-height: 1.4;">
                    <span style="font-weight: 600; color: var(--text-primary);" class="qr-title">Online Validation</span><br>
                    Scan this QR to verify invoice authenticity online.
                </div>
            </div>

            <div class="summary-content" style="margin-top: 0; display: flex; flex-direction: column; gap: 1rem; align-items: flex-end;">
                <div style="width: 100%;">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (<span id="tax-label">{{ old('tax_rate', $invoice->tax_rate ?? 11) }}</span>%)</span>
                        <span id="tax">Rp 0</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>Total Due</span>
                        <span id="grand-total">Rp 0</span>
                    </div>
                </div>
                <!-- Signature Block -->
                <div class="signature-block" style="text-align: center; width: 200px; margin-top: 1.5rem;">
                    <p style="font-size: 0.8rem; color: black !important; margin-bottom: 2.5rem; font-weight: 500;">Authorized Signature,</p>
                    <p style="font-size: 0.85rem; font-weight: 700; color: black !important; border-top: 1px dashed #000; padding-top: 0.3rem;">PT Rayan Smart Kreatif</p>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary" style="text-decoration: none;">Kembali</a>
            <button type="button" id="btn-preview" class="btn btn-secondary">
                Preview & Cetak
            </button>
            <button type="submit" class="btn btn-primary">
                Simpan Nota
            </button>
        </div>

    </div>
</form>
@endsection
