@extends('layouts.admin')

@section('title', __('طلبات البائعين'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-user-tag text-primary me-2"></i> {{ __('طلبات البائعين') }}</h2>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="px-4 py-3">{{ __('المستخدم') }}</th>
                            <th class="px-4 py-3">{{ __('تاريخ الطلب') }}</th>
                            <th class="px-4 py-3">{{ __('الحالة') }}</th>
                            <th class="px-4 py-3">{{ __('ملاحظات الإدارة') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('الإجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-3 d-flex justify-content-center align-items-center rounded-circle shadow-sm" style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ mb_substr($req->user->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $req->user->first_name }} {{ $req->user->last_name }}</h6>
                                            <small class="text-muted">{{ $req->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-dark">{{ $req->created_at->format('Y-m-d') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $req->created_at->format('H:i A') }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i> {{ __('قيد الانتظار') }}</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> {{ __('مقبول') }}</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fas fa-times-circle me-1"></i> {{ __('مرفوض') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-muted" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $req->admin_notes ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($req->status === 'pending')
                                        <!-- Approve Form -->
                                        <form action="{{ route('admin.seller-requests.approve', $req->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="return confirm('{{ __('هل أنت متأكد من قبول هذا الطلب وترقية المستخدم إلى بائع؟') }}')">
                                                <i class="fas fa-check"></i> {{ __('قبول') }}
                                            </button>
                                        </form>

                                        <!-- Reject Button (Triggers Modal) -->
                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                            <i class="fas fa-times"></i> {{ __('رفض') }}
                                        </button>

                                        <!-- Reject Modal -->
                                        <div class="modal fade text-start" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('admin.seller-requests.reject', $req->id) }}" method="POST" class="modal-content border-0 shadow">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white border-0">
                                                        <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i> {{ __('رفض طلب البائع') }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <p>{{ __('أنت على وشك رفض طلب الترقية للمستخدم:') }} <strong>{{ $req->user->first_name }} {{ $req->user->last_name }}</strong></p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">{{ __('سبب الرفض (مطلوب)') }}</label>
                                                            <textarea name="admin_notes" class="form-control" rows="3" required placeholder="{{ __('اكتب سبب الرفض هنا ليظهر للمستخدم...') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-0">
                                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">{{ __('تأكيد الرفض') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted"><i class="fas fa-lock"></i> {{ __('مغلق') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted mb-3"><i class="fas fa-folder-open fa-3x opacity-50"></i></div>
                                    <h5 class="fw-bold">{{ __('لا توجد طلبات') }}</h5>
                                    <p class="mb-0">{{ __('لم يتم تقديم أي طلبات ترقية بائع حتى الآن.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($requests->hasPages())
                <div class="px-4 py-3 border-top bg-light">
                    {{ $requests->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
