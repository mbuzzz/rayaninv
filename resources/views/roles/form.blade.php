@extends('layouts.app')

@section('content')
<div class="glass-container" style="max-width: 500px;">
    <div class="invoice-header" style="border-bottom: 2px solid rgba(255, 255, 255, 0.08); padding-bottom: 1.5rem; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color) !important; font-size: 1.4rem; font-weight: 800; text-transform: uppercase; margin: 0;">
            {{ isset($role) ? 'Edit Role' : 'Create Role' }}
        </h1>
    </div>

    <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <div class="form-group">
            <label for="name">Role Name (slug)</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $role->name ?? '') }}" required>
            @error('name') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="display_name">Display Name</label>
            <input type="text" name="display_name" id="display_name" class="form-control @error('display_name') is-invalid @enderror"
                   value="{{ old('display_name', $role->display_name ?? '') }}" required>
            @error('display_name') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="margin: 0;">
                {{ isset($role) ? 'Update Role' : 'Create Role' }}
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary" style="margin: 0; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
@endsection
