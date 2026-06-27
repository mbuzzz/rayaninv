@extends('layouts.app')

@section('content')
<div class="glass-container" style="max-width: 900px;">
    <div class="invoice-header" style="align-items: center; border-bottom: 2px solid rgba(255, 255, 255, 0.08); padding-bottom: 1.5rem; margin-bottom: 2.5rem;">
        <div class="company-info">
            <h1 style="color: var(--primary-color) !important; font-size: 1.6rem; font-weight: 800; text-transform: uppercase; margin: 0;">Users</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">Manage system users</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary" style="margin: 0; text-decoration: none;">+ Create User</a>
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
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th style="text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $user->username }}</td>
                    <td>{{ $user->name }}</td>
                    <td style="color: var(--text-secondary); font-size: 0.85rem;">{{ $user->email }}</td>
                    <td>
                        @if($user->role)
                            <span style="background: rgba(212, 175, 55, 0.15); color: #d4af37; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">{{ $user->role->display_name }}</span>
                        @else
                            <span style="color: var(--text-secondary); font-size: 0.8rem;">—</span>
                        @endif
                    </td>
                    <td style="font-size: 0.85rem; color: var(--text-secondary);">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: inline-block; text-decoration: none;">Edit</a>
                            @if(!$user->isAdmin() || $loop->first)
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user?');" style="margin:0;">
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
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
