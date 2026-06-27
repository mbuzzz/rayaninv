@extends('layouts.app')

@section('content')
<div class="glass-container" style="max-width: 1000px;">
    
    <div class="invoice-header" style="align-items: center; border-bottom: 2px solid rgba(255, 255, 255, 0.08); padding-bottom: 1.5rem; margin-bottom: 2.5rem;">
        <div class="company-info" style="display: flex; align-items: center; gap: 1.5rem;">
            <img src="{{ asset('images/logorayan.png') }}" alt="Logo PT Rayan Smart Kreatif" style="width: 70px; height: 70px; object-fit: contain;">
            <div>
                <h1 style="color: var(--primary-color) !important; font-size: 1.6rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; margin: 0;">Invoice History</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">PT Rayan Smart Kreatif</p>
            </div>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0;">
            <a href="{{ route('invoices.export') }}" class="btn btn-secondary" style="margin: 0; text-decoration: none;">Export Excel</a>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary" style="margin: 0; display: inline-block; text-decoration: none;">+ Create New</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(74, 222, 128, 0.15); border: 1px solid rgba(74, 222, 128, 0.3); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; color: #4ade80; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2.5rem;">
        <!-- Card 1: Total Invoices -->
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); padding: 1.25rem; border-radius: 12px; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Total Invoices</span>
            <span style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary);">{{ $totalInvoices }}</span>
        </div>

        <!-- Card 2: Total Revenue -->
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); padding: 1.25rem; border-radius: 12px; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Total Revenue</span>
            <span style="font-size: 1.8rem; font-weight: 800; color: var(--primary-color);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
        </div>

        <!-- Card 3: Total Paid -->
        <div style="background: rgba(16, 185, 129, 0.03); border: 1px solid rgba(16, 185, 129, 0.12); padding: 1.25rem; border-radius: 12px; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <span style="font-size: 0.75rem; font-weight: 700; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.5px;">Paid Invoices</span>
            <span style="font-size: 1.8rem; font-weight: 800; color: #10b981;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
        </div>

        <!-- Card 4: Total Unpaid -->
        <div style="background: rgba(239, 68, 68, 0.03); border: 1px solid rgba(239, 68, 68, 0.12); padding: 1.25rem; border-radius: 12px; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <span style="font-size: 0.75rem; font-weight: 700; color: #fca5a5; text-transform: uppercase; letter-spacing: 0.5px;">Unpaid Invoices</span>
            <span style="font-size: 1.8rem; font-weight: 800; color: #ef4444;">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</span>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Date</th>
                <th>Client Name</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created By</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $inv->invoice_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($inv->date)->format('d M Y') }}</td>
                    <td>{{ $inv->customer_name }}</td>
                    <td>Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                    <td>
                        @if($inv->status == 'Lunas')
                            <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;">Paid</span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;">Unpaid</span>
                        @endif
                    </td>
                    <td style="color: var(--text-secondary); font-size: 0.85rem;">{{ $inv->creator?->username ?? '-' }}</td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('invoices.show', $inv->invoice_number) }}?token={{ substr(hash_hmac('sha256', $inv->invoice_number, config('app.key')), 0, 16) }}" class="btn btn-jpg" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: inline-block; text-decoration: none; background: linear-gradient(135deg, #10b981, #059669); box-shadow: none;">Show</a>
                            <a href="{{ route('invoices.edit', $inv->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: inline-block; text-decoration: none;">Edit</a>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus nota ini?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-pdf" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border: none; cursor: pointer; box-shadow: none;">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No invoices found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
