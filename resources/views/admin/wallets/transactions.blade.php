@extends('layouts.admin')

@section('title', __('سجل الحركات المالية والمحفظة'))

@section('css')
<style>
    .transaction-type-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-block;
    }
    .type-credit { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .type-debit { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
    
    .filter-card {
        background: var(--card-bg, #fff);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 16px;
        padding: 1.5rem;
        height: 100%;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -8px rgba(0,0,0,0.15);
    }
    .stat-card.blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .stat-card.green { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
    .stat-card.red { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }
    .stat-card.gold { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 600;
    }
    
    /* Responsive adjustment for table controls */
    .dataTables_wrapper .dataTables_filter, 
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 1rem;
    }

    /* Custom CSS for Radio Buttons styled as Action Badges */
    .type-btn-radio {
        display: none;
    }
    .type-btn-label {
        flex: 1;
        text-align: center;
        padding: 0.65rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        margin: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .type-btn-label.credit-lbl {
        border-color: #10b981;
        color: #10b981;
        background: transparent;
    }
    .type-btn-radio:checked + .type-btn-label.credit-lbl {
        background: #10b981;
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .type-btn-label.debit-lbl {
        border-color: #ef4444;
        color: #ef4444;
        background: transparent;
    }
    .type-btn-radio:checked + .type-btn-label.debit-lbl {
        background: #ef4444;
        color: #fff;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }
</style>
@endsection

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">{{ __('سجل الحركات المالية للمحفظة') }} — {{ $wallet->user->name }}</h1>
        <div class="breadcrumb text-muted mt-1">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">{{ __('الرئيسية') }}</a> / 
            <a href="{{ route('admin.wallets.index') }}" class="text-decoration-none">{{ __('المحافظ') }}</a> / 
            <span>{{ __('سجل المعاملات') }}</span>
        </div>
    </div>
    <div class="actions">
        <button type="button" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
            <i class="fas fa-plus me-2"></i> {{ __('إضافة معاملة جديدة') }}
        </button>
    </div>
</div>

<!-- بطاقات الإحصائيات العلوية للتحديث الديناميكي -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card blue">
            <div class="stat-value"><span id="top_balance">{{ number_format($wallet->balance, 2) }}</span></div>
            <div class="stat-label">{{ __('الرصيد المتاح الحالي') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card green">
            <div class="stat-value"><span id="top_deposits">{{ number_format($wallet->total_deposits, 2) }}</span></div>
            <div class="stat-label">{{ __('إجمالي الإيداعات') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card red">
            <div class="stat-value"><span id="top_withdrawals">{{ number_format($wallet->total_withdrawals, 2) }}</span></div>
            <div class="stat-label">{{ __('إجمالي السحوبات') }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card gold">
            <div class="stat-value">{{ number_format($wallet->debt_ceiling, 2) }}</div>
            <div class="stat-label">{{ __('سقف الدين') }} (<span id="top_debt_usage">{{ $wallet->debt_usage }}</span>%)</div>
        </div>
    </div>
</div>

<!-- شريط الفلترة المتقدمة (التاريخ ونوع المعاملة) -->
<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label fw-bold text-muted small">{{ __('من تاريخ') }}</label>
            <input type="date" id="filter_date_from" class="form-control">
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label fw-bold text-muted small">{{ __('إلى تاريخ') }}</label>
            <input type="date" id="filter_date_to" class="form-control">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label fw-bold text-muted small">{{ __('نوع المعاملة') }}</label>
            <select id="filter_type" class="form-select">
                <option value="All">{{ __('الكل (All)') }}</option>
                <option value="Credit">{{ __('إيداع (Credit)') }}</option>
                <option value="Debit">{{ __('سحب (Debit)') }}</option>
            </select>
        </div>
        <div class="col-12 col-md-1 text-end">
            <button type="button" id="reset_filters" class="btn btn-light w-100" title="{{ __('إعادة ضبط الفلاتر') }}">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
</div>

<!-- جدول عرض البيانات (DataTables) -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="transactionsTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('التاريخ') }}</th>
                        <th>{{ __('النوع') }}</th>
                        <th>{{ __('المبلغ') }}</th>
                        <th>{{ __('البيان / الوصف') }}</th>
                        <th>{{ __('بواسطة') }}</th>
                        <th>{{ __('المرفق') }}</th>
                        <th>{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- سيتم ملء البيانات ديناميكياً بواسطة DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('modals')
<!-- نافذة منبثقة: إضافة معاملة جديدة -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="modal-title fw-bold fs-5" id="addTransactionModalLabel">{{ __('إضافة معاملة جديدة') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- النموذج المخصص للإرسال عبر AJAX -->
            <form id="addTransactionForm" action="{{ route('transactions.store', $wallet->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- 1. حقل المبلغ -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('المبلغ') }}</label>
                        <input type="number" name="amount" class="form-control form-control-lg px-3 py-2" step="0.01" min="0.01" placeholder="{{ __('أدخل المبلغ') }}" required style="border-radius: 8px;">
                    </div>

                    <!-- 2. نوع المعاملة (أزرار الراديو التفاعلية) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('نوع المعاملة') }}</label>
                        <div class="d-flex gap-3">
                            <input type="radio" name="type" id="type_credit" value="credit" class="type-btn-radio" required>
                            <label for="type_credit" class="type-btn-label credit-lbl">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                <span>{{ __('إيداع') }}</span>
                            </label>

                            <input type="radio" name="type" id="type_debit" value="debit" class="type-btn-radio" required>
                            <label for="type_debit" class="type-btn-label debit-lbl">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                <span>{{ __('سحب') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- 3. الحقول المشروطة التي تظهر عند اختيار "سحب" (Debit) -->
                    <div id="debitFields" style="display: none;">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted mb-2">{{ __('Maturity Time') }}</label>
                            <input type="datetime-local" name="maturity_time" class="form-control px-3 py-2" style="border-radius: 8px;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted mb-2">{{ __('Payment Method') }}</label>
                            <select name="payment_method" class="form-select px-3 py-2" style="border-radius: 8px;">
                                <option value="Manual (Cash / Bank Transfer)">{{ __('Manual (Cash / Bank Transfer)') }}</option>
                                <option value="Automatic Transfer">{{ __('Automatic Transfer') }}</option>
                                <option value="Other">{{ __('Other') }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- 4. الوصف -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('الوصف') }}</label>
                        <textarea name="description" class="form-control px-3 py-2" rows="3" placeholder="{{ __('ملاحظات اختيارية...') }}" maxlength="500" style="border-radius: 8px;"></textarea>
                    </div>
                    
                    <!-- 5. رفع الملف -->
                    <div class="mb-1">
                        <label class="form-label fw-bold small text-muted mb-2 d-flex align-items-center gap-1">
                            <span>Upload File</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </label>
                        <input type="file" name="attachment" class="form-control px-3 py-2" accept=".jpeg,.png,.webp,.pdf" style="border-radius: 8px;">
                        <div class="form-text text-muted small mt-2 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                            <span>ⓘ Supported formats: Images (JPEG, PNG, WebP), Documents (PDF). Max size: 10MB</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4 d-flex justify-content-start gap-2">
                    <button type="submit" class="btn px-4 py-2 fw-bold text-white shadow-sm" style="background-color: #6366f1; border-radius: 8px;">Submit</button>
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نافذة منبثقة: تعديل معاملة مالية -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="modal-title fw-bold fs-5" id="editTransactionModalLabel">{{ __('تعديل المعاملة المالية') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- النموذج المخصص للإرسال عبر AJAX -->
            <form id="editTransactionForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_transaction_id" name="transaction_id">
                <div class="modal-body p-4">
                    <!-- 1. حقل المبلغ -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('المبلغ') }}</label>
                        <input type="number" name="amount" id="edit_amount" class="form-control form-control-lg px-3 py-2" step="0.01" min="0.01" placeholder="{{ __('أدخل المبلغ') }}" required style="border-radius: 8px;">
                    </div>

                    <!-- 2. نوع المعاملة (أزرار الراديو التفاعلية) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('نوع المعاملة') }}</label>
                        <div class="d-flex gap-3">
                            <input type="radio" name="type" id="edit_type_credit" value="credit" class="type-btn-radio" required>
                            <label for="edit_type_credit" class="type-btn-label credit-lbl">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                <span>{{ __('إيداع') }}</span>
                            </label>

                            <input type="radio" name="type" id="edit_type_debit" value="debit" class="type-btn-radio" required>
                            <label for="edit_type_debit" class="type-btn-label debit-lbl">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                <span>{{ __('سحب') }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- 3. الحقول المشروطة التي تظهر عند اختيار "سحب" (Debit) -->
                    <div id="editDebitFields" style="display: none;">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted mb-2">{{ __('Maturity Time') }}</label>
                            <input type="datetime-local" name="maturity_time" id="edit_maturity_time" class="form-control px-3 py-2" style="border-radius: 8px;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted mb-2">{{ __('Payment Method') }}</label>
                            <select name="payment_method" id="edit_payment_method" class="form-select px-3 py-2" style="border-radius: 8px;">
                                <option value="Manual (Cash / Bank Transfer)">{{ __('Manual (Cash / Bank Transfer)') }}</option>
                                <option value="Automatic Transfer">{{ __('Automatic Transfer') }}</option>
                                <option value="Other">{{ __('Other') }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- 4. الوصف -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted mb-2">* {{ __('الوصف') }}</label>
                        <textarea name="description" id="edit_description" class="form-control px-3 py-2" rows="3" placeholder="{{ __('ملاحظات اختيارية...') }}" maxlength="500" style="border-radius: 8px;"></textarea>
                    </div>
                    
                    <!-- 5. رفع الملف -->
                    <div class="mb-1">
                        <label class="form-label fw-bold small text-muted mb-2 d-flex align-items-center gap-1">
                            <span>Upload File</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </label>
                        <input type="file" name="attachment" class="form-control px-3 py-2" accept=".jpeg,.png,.webp,.pdf" style="border-radius: 8px;">
                        <div class="form-text text-muted small mt-2 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                            <span>ⓘ Supported formats: Images (JPEG, PNG, WebP), Documents (PDF). Max size: 10MB</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4 d-flex justify-content-start gap-2">
                    <button type="submit" class="btn px-4 py-2 fw-bold text-white shadow-sm" style="background-color: #6366f1; border-radius: 8px;">Update</button>
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // 1. تهيئة DataTables مع دعم الفلترة المتقدمة عبر AJAX
        let transactionsTable = $('#transactionsTable').DataTable({
            processing: true,
            serverSide: false, // يعمل بكفاءة مع Yajra أو الاستجابة المباشرة
            order: [[0, 'desc']],
            ajax: {
                url: "{{ route('transactions.index', $wallet->id) }}",
                type: "GET",
                data: function(d) {
                    // إرسال قيم الفلاتر المخصصة مع كل طلب
                    d.date_from = $('#filter_date_from').val();
                    d.date_to   = $('#filter_date_to').val();
                    d.type      = $('#filter_type').val();
                }
            },
            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'type', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'description', name: 'description' },
                { data: 'created_by', name: 'created_by' },
                { data: 'attachment', name: 'attachment', orderable: false, searchable: false },
                { 
                    data: 'id', 
                    orderable: false, 
                    searchable: false,
                    render: function(data) {
                        return `<button type="button" class="btn btn-sm btn-outline-primary edit-transaction-btn" data-id="${data}" title="{{ __('تعديل المعاملة') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>`;
                    }
                }
            ],
            language: {
                sProcessing: "{{ __('جاري التحميل...') }}",
                sLengthMenu: "{{ __('عرض _MENU_ سجل') }}",
                sZeroRecords: "{{ __('لا توجد حركات مالية مطابقة') }}",
                sInfo: "{{ __('عرض _START_ إلى _END_ من أصل _TOTAL_ سجل') }}",
                sSearch: "{{ __('بحث سريع:') }}",
                oPaginate: {
                    sFirst: "{{ __('الأول') }}",
                    sPrevious: "{{ __('السابق') }}",
                    sNext: "{{ __('التالي') }}",
                    sLast: "{{ __('الأخير') }}"
                }
            }
        });

        // 2. إعادة تحميل الجدول تلقائياً عند تغيير قيم الفلاتر
        $('#filter_date_from, #filter_date_to, #filter_type').on('change', function() {
            transactionsTable.ajax.reload();
        });

        // زر إعادة ضبط الفلاتر
        $('#reset_filters').on('click', function() {
            $('#filter_date_from').val('');
            $('#filter_date_to').val('');
            $('#filter_type').val('All');
            transactionsTable.ajax.reload();
        });

        // إظهار وإخفاء الحقول المشروطة في نموذج الإضافة
        $('#addTransactionForm input[name="type"]').on('change', function() {
            if ($(this).val() === 'debit') {
                $('#debitFields').slideDown(200);
            } else {
                $('#debitFields').slideUp(200);
            }
        });

        // إظهار وإخفاء الحقول المشروطة في نموذج التعديل
        $('#editTransactionForm input[name="type"]').on('change', function() {
            if ($(this).val() === 'debit') {
                $('#editDebitFields').slideDown(200);
            } else {
                $('#editDebitFields').slideUp(200);
            }
        });

        // 3. معالجة إرسال نموذج المعاملة عبر AJAX كما ورد في المتطلبات
        $('#addTransactionForm').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let formData = new FormData(this); // لاستيعاب الملف المرفق
            let submitBtn = form.find('button[type="submit"]');
            
            // تعطيل الزر لتجنب الإرسال المزدوج
            submitBtn.prop('disabled', true).text('جاري المعالجة...');

            $.ajax({
                url: form.attr('action'), // المسار الذي أنشأناه في الخطوة 1
                method: 'POST',
                data: formData,
                processData: false, // ضروري لرفع الملفات
                contentType: false, // ضروري لرفع الملفات
                success: function(response) {
                    // 1. إغلاق النافذة المنبثقة (Modal)
                    $('#addTransactionModal').modal('hide');
                    
                    // 2. إظهار رسالة نجاح باستخدام Toastr
                    toastr.success(response.message || 'تمت إضافة المعاملة بنجاح.');
                    
                    // 3. تفريغ الحقول لإدخال قادم وإخفاء الحقول المشروطة
                    form.trigger("reset");
                    $('#debitFields').slideUp(0);
                    
                    // 4. تحديث جدول البيانات (DataTables) بدون عمل Refresh للصفحة
                    $('#transactionsTable').DataTable().ajax.reload();
                    
                    // 5. تحديث أرقام الرصيد العلوية في الصفحة ديناميكياً
                    if (response.wallet) {
                        $('#top_balance').text(response.wallet.balance);
                        $('#top_deposits').text(response.wallet.total_deposits);
                        $('#top_withdrawals').text(response.wallet.total_withdrawals);
                        $('#top_debt_usage').text(response.wallet.debt_usage);
                    }
                },
                error: function(xhr) {
                    // التعامل مع الأخطاء (مثل نقص الحقول أو تجاوز السقف)
                    let errors = xhr.responseJSON?.errors || xhr.responseJSON?.message || 'حدث خطأ أثناء معالجة الطلب.';
                    let errorString = typeof errors === 'string' ? errors : JSON.stringify(errors);
                    toastr.error('حدث خطأ: ' + errorString);
                },
                complete: function() {
                    // إعادة تفعيل الزر
                    submitBtn.prop('disabled', false).text('Submit');
                }
            });
        });

        // جلب بيانات المعاملة عند الضغط على زر التعديل
        $('#transactionsTable').on('click', '.edit-transaction-btn', function() {
            let id = $(this).data('id');
            let baseUrl = "{{ url('admin/wallets/' . $wallet->id . '/transactions') }}";
            
            $.ajax({
                url: `${baseUrl}/${id}/edit`,
                type: 'GET',
                success: function(data) {
                    $('#edit_transaction_id').val(data.id);
                    $('#edit_amount').val(data.amount);
                    $('#edit_description').val(data.description);
                    
                    if (data.type === 'credit') {
                        $('#edit_type_credit').prop('checked', true);
                        $('#editDebitFields').slideUp(0);
                    } else {
                        $('#edit_type_debit').prop('checked', true);
                        $('#editDebitFields').slideDown(0);
                        
                        if (data.maturity_time) {
                            // تحويل الصيغة لتناسب datetime-local
                            let d = new Date(data.maturity_time);
                            // تعويض التوقيت المحلي لتجنب فرق التوقيت
                            let localIso = new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                            $('#edit_maturity_time').val(localIso);
                        } else {
                            $('#edit_maturity_time').val('');
                        }
                        
                        if (data.payment_method) {
                            $('#edit_payment_method').val(data.payment_method);
                        }
                    }
                    
                    $('#editTransactionModal').modal('show');
                },
                error: function() {
                    toastr.error('تعذر جلب بيانات المعاملة.');
                }
            });
        });

        // إرسال نموذج التعديل عبر AJAX
        $('#editTransactionForm').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let id = $('#edit_transaction_id').val();
            let formData = new FormData(this);
            let submitBtn = form.find('button[type="submit"]');
            let baseUrl = "{{ url('admin/wallets/' . $wallet->id . '/transactions') }}";
            
            submitBtn.prop('disabled', true).text('جاري التحديث...');
            
            $.ajax({
                url: `${baseUrl}/${id}/update`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#editTransactionModal').modal('hide');
                    toastr.success(response.message || 'تم التعديل بنجاح.');
                    transactionsTable.ajax.reload(null, false);
                    
                    if (response.wallet) {
                        $('#top_balance').text(response.wallet.balance);
                        $('#top_deposits').text(response.wallet.total_deposits);
                        $('#top_withdrawals').text(response.wallet.total_withdrawals);
                        $('#top_debt_usage').text(response.wallet.debt_usage);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors || xhr.responseJSON?.message || 'حدث خطأ أثناء التحديث.';
                    let errorString = typeof errors === 'string' ? errors : JSON.stringify(errors);
                    toastr.error('حدث خطأ: ' + errorString);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Update');
                }
            });
        });

        // تهيئة النموذج وإخفاء الحقول عند كل فتح للنافذة
        $('#addTransactionModal').on('show.bs.modal', function() {
            $('#addTransactionForm').trigger("reset");
            $('#debitFields').slideUp(0);
        });
    });
</script>
@endsection
