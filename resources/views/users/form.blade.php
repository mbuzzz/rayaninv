@extends('layouts.app')

@section('content')
<div class="glass-container" style="max-width: 550px;">
    <div class="invoice-header" style="border-bottom: 2px solid rgba(255, 255, 255, 0.08); padding-bottom: 1.5rem; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color) !important; font-size: 1.4rem; font-weight: 800; text-transform: uppercase; margin: 0;">
            {{ isset($user) ? 'Edit User' : 'Create User' }}
        </h1>
    </div>

    <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $user->username ?? '') }}" required>
            @error('username') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password {{ isset($user) ? '(leave empty to keep current)' : '' }}</label>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                   {{ isset($user) ? '' : 'required' }}>
            @error('password') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="role_id">Role</label>
            <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror">
                <option value="">— Select Role —</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                        {{ $role->display_name }}
                    </option>
                @endforeach
            </select>
            @error('role_id') <div style="color: #f87171; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="margin: 0;">
                {{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary" style="margin: 0; text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
@endsection
