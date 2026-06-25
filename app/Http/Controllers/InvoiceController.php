<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $today = \Carbon\Carbon::now()->format('Ymd');
        $countToday = \App\Models\Invoice::whereDate('created_at', \Carbon\Carbon::today())->count();
        $nextNumber = 'INV-' . $today . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
        
        return view('invoices.form', compact('nextNumber'));
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
        return view('invoices.form', compact('invoice'));
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

    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Nota berhasil dihapus.');
    }
}
