<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->get();
        
        // Compute statistics
        $totalInvoices = $invoices->count();
        $totalRevenue = $invoices->sum('total');
        $totalPaid = $invoices->where('status', 'Lunas')->sum('total');
        $totalUnpaid = $invoices->where('status', 'Belum Lunas')->sum('total');
        
        return view('invoices.index', compact(
            'invoices', 
            'totalInvoices', 
            'totalRevenue', 
            'totalPaid', 
            'totalUnpaid'
        ));
    }

    public function exportExcel()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Invoice History');
        
        // Set Header Style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Dark slate
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '475569'],
                ],
            ],
        ];

        // Column Headers
        $headers = [
            'Invoice Number', 
            'Date', 
            'Customer Name', 
            'Subtotal (IDR)', 
            'Tax Rate', 
            'Tax Amount (IDR)', 
            'Total Amount (IDR)', 
            'Status'
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // Populate Data
        $rowNum = 2;
        foreach ($invoices as $inv) {
            $sheet->setCellValue('A' . $rowNum, $inv->invoice_number);
            $sheet->setCellValue('B' . $rowNum, $inv->date);
            $sheet->setCellValue('C' . $rowNum, $inv->customer_name);
            $sheet->setCellValue('D' . $rowNum, $inv->subtotal);
            $sheet->setCellValue('E' . $rowNum, ($inv->tax_rate / 100)); // Will format as %
            $sheet->setCellValue('F' . $rowNum, $inv->tax);
            $sheet->setCellValue('G' . $rowNum, $inv->total);
            $sheet->setCellValue('H' . $rowNum, $inv->status);
            
            // Format cells
            $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('Rp#,##0');
            $sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('Rp#,##0');
            $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('Rp#,##0');
            $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Highlight status
            $statusStyle = [
                'font' => [
                    'bold' => true,
                ]
            ];
            if ($inv->status === 'Lunas') {
                $statusStyle['font']['color'] = ['rgb' => '15803D']; // green
            } else {
                $statusStyle['font']['color'] = ['rgb' => 'B91C1C']; // red
            }
            $sheet->getStyle('H' . $rowNum)->applyFromArray($statusStyle);
            
            // Thin borders for data rows
            $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('E2E8F0'));
            
            $rowNum++;
        }
        
        $fileName = 'invoice_history_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function create()
    {
        $today = \Carbon\Carbon::now()->format('Ymd');
        $countToday = \App\Models\Invoice::whereDate('created_at', \Carbon\Carbon::today())->count();
        $nextNumber = 'INV-' . $today . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
        $token = substr(hash_hmac('sha256', $nextNumber, config('app.key')), 0, 16);
        
        return view('invoices.form', compact('nextNumber', 'token'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'customer_name' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|in:Lunas,Belum Lunas',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ($item['quantity'] * $item['price']);
        }
        $tax = $subtotal * ($validated['tax_rate'] / 100);
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'invoice_number' => $validated['invoice_number'],
            'customer_name' => $validated['customer_name'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'tax_rate' => $validated['tax_rate'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Nota berhasil disimpan.');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $token = substr(hash_hmac('sha256', $invoice->invoice_number, config('app.key')), 0, 16);
        return view('invoices.form', compact('invoice', 'token'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'customer_name' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|in:Lunas,Belum Lunas',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ($item['quantity'] * $item['price']);
        }
        $tax = $subtotal * ($validated['tax_rate'] / 100);
        $total = $subtotal + $tax;

        $invoice->update([
            'invoice_number' => $validated['invoice_number'],
            'customer_name' => $validated['customer_name'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'tax_rate' => $validated['tax_rate'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        // Delete old items and recreate
        $invoice->items()->delete();
        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Nota berhasil diperbarui.');
    }

    public function show(Request $request, Invoice $invoice)
    {
        $expectedToken = substr(hash_hmac('sha256', $invoice->invoice_number, config('app.key')), 0, 16);
        if ($request->query('token') !== $expectedToken) {
            abort(403, 'Tautan verifikasi tidak sah atau telah kedaluwarsa.');
        }
        $invoice->load('items');
        return view('invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Nota berhasil dihapus.');
    }
}
