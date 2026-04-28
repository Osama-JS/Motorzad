@extends('layouts.admin')

@section('title', 'الأدوار')

@section('content')
<div class="page-header">
    <div>
        <h1>إدارة الأدوار</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a> / الأدوار</div>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        إضافة دور جديد
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2>جميع الأدوار</h2>
        <span class="badge badge-info">{{ $roles->count() }} دور</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>اسم الدور</th>

                <th>الصلاحيات</th>
                <th>عدد المستخدمين</th>
                <th>الإجراءات</th>
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
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn-icon-only edit" title="تعديل">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon-only delete" title="حذف">
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <p>لا توجد أدوار مُعرّفة. أنشئ أول دور الآن.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
