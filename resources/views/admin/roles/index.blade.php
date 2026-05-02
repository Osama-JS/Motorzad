@extends('layouts.admin')

@section('title', 'الأدوار')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Roles Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Roles') }}</div>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        {{ __('Add New Role') }}
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card gold">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="stat-value">{{ $roles->count() }}</div>
        <div class="stat-label">{{ __('All Roles') }}</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-value">{{ $roles->sum(function($role) { return $role->users->count(); }) }}</div>
        <div class="stat-label">{{ __('Number of Users') }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('All Roles') }}</h2>
        <span class="badge badge-info">{{ $roles->count() }} {{ __('Role') }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped w-100">
            <thead>
                <tr>
                    <th>{{ __('Role Name') }}</th>

                    <th>{{ __('Permissions') }}</th>
                    <th>{{ __('Number of Users') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td style="font-weight:700;">{{ $role->name }}</td>

                    <td>
                        <div style="display:flex; flex-wrap:wrap; gap:0.3rem;">
                            @foreach($role->permissions->take(3) as $perm)
                                <span class="badge badge-primary">{{ $perm->name }}</span>
                            @endforeach
                            @if($role->permissions->count() > 3)
                                <span class="badge" style="background:rgba(100,116,139,0.1); color:var(--text-muted);">+{{ $role->permissions->count() - 3 }}</span>
                            @endif
                        </div>
                    </td>
                    <td><span style="color:var(--text-secondary);">{{ $role->users->count() }}</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn-icon-only edit" title="{{ __('Edit') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-only delete" title="{{ __('Delete') }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <p>{{ __('No roles defined. Create the first role now.') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
