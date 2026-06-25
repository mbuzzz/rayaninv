@extends('layouts.app')

@section('content')
@php
    $logoPath = public_path('images/logorayan.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<div class="glass-container" style="max-width: 400px; padding: 2.5rem; display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
    <div style="text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
        <img src="{{ $logoBase64 }}" alt="Logo PT Rayan Smart Kreatif" style="width: 70px; height: 70px; object-fit: contain;">
        <h1 style="font-size: 1.2rem; font-weight: 800; letter-spacing: 0.5px; color: var(--primary-color); margin: 0; text-transform: uppercase;">PT Rayan Smart Kreatif</h1>
        <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;">Invoice Management Panel</p>
    </div>

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.75rem; border-radius: 8px; width: 100%; color: #fca5a5; font-size: 0.85rem; line-height: 1.4;">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" style="width: 100%; display: flex; flex-direction: column; gap: 1.2rem;">
        @csrf
        <div class="info-group" style="width: 100%;">
            <label style="font-size: 0.75rem; font-weight: 700; color: var(--primary-color);">USERNAME</label>
            <input type="text" name="username" class="info-input" placeholder="Enter username" value="{{ old('username') }}" required autofocus>
        </div>

        <div class="info-group" style="width: 100%;">
            <label style="font-size: 0.75rem; font-weight: 700; color: var(--primary-color);">PASSWORD</label>
            <input type="password" name="password" class="info-input" placeholder="••••••••" required>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; width: 100%;">
            <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary-color); cursor: pointer;">
            <label for="remember" style="font-size: 0.8rem; color: var(--text-secondary); cursor: pointer; user-select: none;">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
            Sign In
        </button>
    </form>
</div>
@endsection
