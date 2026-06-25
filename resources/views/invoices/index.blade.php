@extends('layouts.app')

@section('content')
<div class="glass-container" style="max-width: 1000px;">
    
    <div class="invoice-header" style="align-items: center;">
        <div class="company-info" style="display: flex; align-items: center; gap: 1.5rem;">
            <img src="{{ asset('images/logorayan.png') }}" alt="Logo PT Rayan Smart Kreatif" style="width: 70px; height: 70px; object-fit: contain;">
            <div>
                <h1>Daftar Nota</h1>
                <p>PT Rayan Smart Kreatif</p>
            </div>
        </div>
        <div class="actions" style="margin-top: 0;">
            <a href="{{ route('invoices.create') }}" class="btn btn-add" style="margin: 0; display: inline-block; text-decoration: none;">+ Buat Nota Baru</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(74, 222, 128, 0.2); border: 1px solid rgba(74, 222, 128, 0.5); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; color: #166534;">
            {{ session('success') }}
        </div>
    @endif

    <table class="invoice-table">
        <thead>
            <tr>
                <th>No. Nota</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Status</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td style="font-weight: 600;">{{ $inv->invoice_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($inv->date)->format('d M Y') }}</td>
                    <td>{{ $inv->customer_name }}</td>
                    <td>Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                    <td>
                        @if($inv->status == 'Lunas')
                            <span style="background: rgba(74, 222, 128, 0.2); color: #166534; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">Lunas</span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.2); color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600;">Belum Lunas</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('invoices.show', $inv->invoice_number) }}?token={{ substr(hash_hmac('sha256', $inv->invoice_number, config('app.key')), 0, 16) }}" class="btn btn-jpg" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; display: inline-block; text-decoration: none; background: linear-gradient(135deg, #10b981, #059669); box-shadow: none;">Tampilkan</a>
                            <a href="{{ route('invoices.edit', $inv->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; display: inline-block; text-decoration: none;">Lihat/Edit</a>
                            
                            <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus nota ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-pdf" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; border: none; cursor: pointer;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada nota yang dibuat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
