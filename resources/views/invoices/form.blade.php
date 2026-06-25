@extends('layouts.app')

@section('content')
<div class="preview-actions">
    <button type="button" id="btn-close-preview" class="btn btn-secondary" style="margin: 0;">Kembali Edit</button>
    <button type="button" id="btn-print" class="btn btn-secondary" style="margin: 0;">Cetak (Print)</button>
    <button type="button" id="btn-export-pdf" class="btn btn-pdf" style="margin: 0;">Unduh PDF</button>
    <button type="button" id="btn-export-jpg" class="btn btn-jpg" style="margin: 0;">Unduh JPG</button>
</div>

<form action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}" method="POST" id="invoice-form">
    @csrf
    @if(isset($invoice))
        @method('PUT')
    @endif

    <div class="glass-container" id="invoice-container">
        <!-- Watermark Background -->
        <div class="watermark">
            <img src="{{ asset('images/logorayan.png') }}" alt="Watermark PT Rayan Smart Kreatif">
        </div>
        
        <div class="invoice-header">
            <div class="company-info" style="display: flex; align-items: center; gap: 1.5rem;">
                <img src="{{ asset('images/logorayan.png') }}" alt="Logo PT Rayan Smart Kreatif" style="width: 90px; height: 90px; object-fit: contain;">
                <div>
                    <h1>PT Rayan Smart Kreatif</h1>
                    <p>Dusun Jalen 1, Desa Setail, Kec. Genteng<br>
                       Kab. Banyuwangi, Prov. Jawa Timur 68465<br>
                       Email: rayansmartkreatif@gmail.com<br>
                       Website: www.rayan.web.id</p>
                </div>
            </div>
            <div class="invoice-title">
                <h2>NOTA</h2>
                <div style="font-size: 1rem; color: var(--text-secondary); margin-top: 0.5rem; font-weight: 500;">
                    No: <input type="text" name="invoice_number" class="table-input" style="width: 200px; display: inline-block; padding: 0.2rem 0.5rem;" placeholder="INV-20260101-0001" value="{{ old('invoice_number', $invoice->invoice_number ?? $nextNumber ?? '') }}" readonly required>
                </div>
                @error('invoice_number')
                    <div style="color: red; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="customer-info">
            <div class="info-group">
                <label>Diberikan Kepada:</label>
                <input type="text" name="customer_name" class="info-input" placeholder="Nama Pelanggan / Perusahaan" value="{{ old('customer_name', $invoice->customer_name ?? '') }}" required>
                @error('customer_name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            
            <div class="info-group">
                <label>Status Pembayaran:</label>
                <select name="status" class="info-input">
                    <option value="Belum Lunas" {{ (old('status', $invoice->status ?? '') == 'Belum Lunas') ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Lunas" {{ (old('status', $invoice->status ?? '') == 'Lunas') ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div class="info-group" style="align-items: flex-end;">
                <label>Tanggal:</label>
                <input type="date" name="date" class="info-input" style="width: auto;" value="{{ old('date', $invoice->date ?? date('Y-m-d')) }}" required>
            </div>
            
            <div class="info-group" style="align-items: flex-end;">
                <label>Pajak / PPN (%):</label>
                <input type="number" step="0.01" min="0" max="100" id="tax_rate_input" name="tax_rate" class="info-input" style="width: 100px;" value="{{ old('tax_rate', $invoice->tax_rate ?? 11) }}" required>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="col-desc">Deskripsi Barang / Jasa</th>
                    <th class="col-qty">Jumlah</th>
                    <th class="col-price">Harga Satuan</th>
                    <th class="col-total">Total Harga</th>
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
                        <input type="text" name="items[{{ $index }}][description]" class="table-input input-desc" placeholder="Nama Barang / Jasa" value="{{ is_array($item) ? ($item['description'] ?? '') : $item->description }}" required>
                    </td>
                    <td class="col-qty">
                        <input type="number" name="items[{{ $index }}][quantity]" class="table-input input-qty" value="{{ is_array($item) ? ($item['quantity'] ?? 1) : $item->quantity }}" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][price]" class="table-input input-price" placeholder="Harga Satuan" value="{{ is_array($item) ? ($item['price'] ?? '') : (float)$item->price }}" required>
                    </td>
                    <td class="col-total item-total">Rp 0</td>
                    <td class="col-action">
                        <button type="button" class="btn-remove" title="Hapus Baris">×</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" id="btn-add-item" class="btn-add">+ Tambah Baris Barang/Jasa</button>

        <div class="invoice-summary" style="justify-content: space-between; align-items: flex-end; gap: 2rem;">
            <!-- QR Code Section -->
            <div id="qr-code-container" style="display: flex; align-items: center; gap: 1rem; text-align: left; padding: 0.5rem; border-radius: 10px; background: rgba(255, 255, 255, 0.15);">
                <div style="background: white; padding: 6px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: inline-block;">
                    <canvas id="invoice-qr" style="width: 90px; height: 90px; display: block;"></canvas>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary); max-width: 180px; line-height: 1.4;">
                    <span style="font-weight: 600; color: var(--text-primary);" class="qr-title">Validasi Online</span><br>
                    Scan QR ini untuk memverifikasi keaslian nota secara online.
                </div>
            </div>

            <div class="summary-content" style="margin-top: 0;">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span>Pajak (<span id="tax-label">{{ old('tax_rate', $invoice->tax_rate ?? 11) }}</span>%)</span>
                    <span id="tax">Rp 0</span>
                </div>
                <div class="summary-row grand-total">
                    <span>Total Tagihan</span>
                    <span id="grand-total">Rp 0</span>
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
