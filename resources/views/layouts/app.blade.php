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

    @yield('content')

    <!-- Invoice Logic JS -->
    <script src="{{ asset('js/invoice.js') }}"></script>
</body>
</html>
