@extends('layouts.admin')

@section('title', 'الصلاحيات')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Permissions Management') }}</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / {{ __('Permissions') }}</div>
    </div>
</div>

<div class="two-col">
    {{-- Permissions Table --}}
    <div class="col-wide">
        <div class="card">
            <div class="card-header">
                <h2>{{ __('All Permissions') }}</h2>
                <span class="badge badge-info">{{ $permissions->count() }} {{ __('Permission') }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Permission Name') }}</th>

                        <th>{{ __('Linked to') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                    <tr>
                        <td style="color:var(--text-muted); font-size:0.8rem;">{{ $permission->id }}</td>
                        <td style="font-weight:600;">{{ $permission->name }}</td>

                        <td><span style="color:var(--text-secondary);">{{ $permission->roles->count() }} {{ __('Role') }}</span></td>
                        <td>
                            <div class="actions-cell">
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this permission?') }}')">
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
                        <td colspan="5">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <p>{{ __('No permissions. Add the first permission from the side form.') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Permission Form --}}
    <div class="col-aside">
        <div class="card">
            <div class="card-header">
                <h2>{{ __('Add New Permission') }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">{{ __('Permission Name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Example: Delete Auctions') }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        {{ __('Add Permission') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
