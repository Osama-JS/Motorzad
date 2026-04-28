@extends('layouts.admin')

@section('title', 'إضافة دور جديد')

@section('content')
<div class="page-header">
    <div>
        <h1>إضافة دور جديد</h1>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a> / <a href="{{ route('admin.roles.index') }}">الأدوار</a> / إضافة</div>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        رجوع
    </a>
</div>

<div style="max-width:800px;">
    <div class="card">
        <div class="card-header">
            <h2>معلومات الدور</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">اسم الدور</label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: مدير المزادات" value="{{ old('name') }}" required>
                </div>



                <div class="form-group">
                    <label class="form-label">تخصيص الصلاحيات</label>
                    <div class="checkbox-grid">
                        @foreach($permissions as $permission)
                        <label class="checkbox-item">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                            <div>
                                <div class="check-label">{{ $permission->name }}</div>

                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex; gap:0.75rem; margin-top:2rem;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        حفظ الدور
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
