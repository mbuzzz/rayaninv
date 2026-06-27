@extends('layouts.app')

@section('content')
<div class="glass-container" style="max-width: 700px;">
    <div class="invoice-header" style="align-items: center; border-bottom: 2px solid rgba(255, 255, 255, 0.08); padding-bottom: 1.5rem; margin-bottom: 2.5rem;">
        <div class="company-info">
            <h1 style="color: var(--primary-color) !important; font-size: 1.6rem; font-weight: 800; text-transform: uppercase; margin: 0;">Roles</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">Manage user roles</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary" style="margin: 0; text-decoration: none;">+ Create Role</a>
    </div>

    @if(session('success'))
        <div style="background: rgba(74, 222, 128, 0.15); border: 1px solid rgba(74, 222, 128, 0.3); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; color: #4ade80; font-size: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; color: #f87171; font-size: 0.9rem;">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Display Name</th>
                <th>Users</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $role->name }}</td>
                    <td>{{ $role->display_name }}</td>
                    <td>{{ $role->users_count ?? $role->users()->count() }}</td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: inline-block; text-decoration: none;">Edit</a>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Delete this role?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-pdf" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border: none; cursor: pointer; box-shadow: none;">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No roles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
