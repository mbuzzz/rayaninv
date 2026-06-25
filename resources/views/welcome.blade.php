<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Online - PT Rayan Smart Kreatif</title>
    <!-- Glassmorphism CSS -->
    <link rel="stylesheet" href="{{ asset('css/glassmorphism.css') }}">
    <!-- html2canvas and jsPDF libraries for export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>

    <div class="glass-container" id="invoice-container">
        
        <div class="invoice-header">
            <div class="company-info" style="display: flex; align-items: center; gap: 1.5rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PT Rayan Smart Kreatif" style="width: 80px; height: 80px; object-fit: contain; border-radius: 12px;">
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
                    No: <input type="text" class="table-input" style="width: 120px; display: inline-block; padding: 0.2rem 0.5rem;" placeholder="INV-001">
                </div>
            </div>
        </div>

        <div class="customer-info">
            <div class="info-group">
                <label>Diberikan Kepada:</label>
                <input type="text" class="info-input" placeholder="Nama Pelanggan / Perusahaan">
            </div>
            <div class="info-group" style="align-items: flex-end;">
                <label>Tanggal:</label>
                <input type="date" class="info-input" style="width: auto;">
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
                <tr class="item-row">
                    <td><input type="text" class="table-input input-desc" placeholder="Nama Barang / Jasa"></td>
                    <td class="col-qty"><input type="number" class="table-input input-qty" value="1" min="1"></td>
                    <td><input type="number" class="table-input input-price" placeholder="Harga Satuan"></td>
                    <td class="col-total item-total">Rp 0</td>
                    <td class="col-action">
                        <button class="btn-remove" title="Hapus Baris">×</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button id="btn-add-item" class="btn-add">+ Tambah Baris Barang/Jasa</button>

        <div class="invoice-summary">
            <div class="summary-content">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span>PPN (11%)</span>
                    <span id="tax">Rp 0</span>
                </div>
                <div class="summary-row grand-total">
                    <span>Total Tagihan</span>
                    <span id="grand-total">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button id="btn-export-pdf" class="btn btn-pdf">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Unduh PDF
            </button>
            <button id="btn-export-jpg" class="btn btn-jpg">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Unduh JPG
            </button>
        </div>

    </div>

    <!-- Invoice Logic JS -->
    <script src="{{ asset('js/invoice.js') }}"></script>
</body>
</html>
