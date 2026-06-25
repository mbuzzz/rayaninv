<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Online - PT Rayan Smart Kreatif</title>
    <!-- Glassmorphism CSS -->
    <link rel="stylesheet" href="{{ asset('css/glassmorphism.css') }}">
    <!-- html2canvas, jsPDF, and QRious libraries for export & verification -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
</head>
<body>

    @auth
    @php
        $logoPath = public_path('images/logorayan.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp
    <!-- Admin Navigation Bar -->
    <nav class="admin-nav" style="background: rgba(18, 19, 21, 0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; box-shadow: 0 4px 30px rgba(0,0,0,0.3);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <img src="{{ $logoBase64 }}" alt="Logo" style="height: 35px; width: 35px; object-fit: contain;">
            <span style="font-weight: 800; color: var(--primary-color); letter-spacing: 0.5px; font-size: 1.1rem; text-transform: uppercase;">Rayan Inv</span>
        </div>
        <div style="display: flex; align-items: center; gap: 2rem;">
            <a href="{{ route('invoices.index') }}" style="color: var(--text-primary); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-primary)'">History</a>
            <a href="{{ route('invoices.create') }}" style="color: var(--text-primary); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-primary)'">+ New Invoice</a>
            <a href="{{ route('invoices.export') }}" style="color: var(--text-primary); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-primary)'">Export Excel</a>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600;">Admin</span>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin: 0; display: inline-block; box-shadow: none;">Logout</button>
            </form>
        </div>
    </nav>
    <div class="content-wrapper" style="margin-top: 80px; width: 100%; display: flex; justify-content: center;">
        @yield('content')
    </div>
    @else
        @yield('content')
    @endauth

    <!-- Invoice Logic JS -->
    <script src="{{ asset('js/invoice.js') }}"></script>
</body>
</html>
