@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">إرسال إشعار جديد (متعدد القنوات)</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form id="send-notification-form" action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- بيانات الإشعار -->
                <div class="mb-3">
                    <label class="form-label">عنوان الإشعار <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="مثال: خصم خاص للمزايدين">
                </div>

                <div class="mb-3">
                    <label class="form-label">محتوى الرسالة <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="4" required placeholder="اكتب تفاصيل الإشعار هنا..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">رابط (إختياري)</label>
                    <input type="url" name="action_url" class="form-control" placeholder="https://motorzad.com/offers">
                </div>

                <!-- قنوات الإرسال -->
                <div class="mb-4">
                    <label class="form-label d-block fw-bold">قنوات الإرسال <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="channels[]" value="database" id="ch_db" checked>
                        <label class="form-check-label" for="ch_db">إشعار داخلي (الموقع)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="channels[]" value="fcm" id="ch_fcm">
                        <label class="form-check-label" for="ch_fcm">إشعار موبايل (Push)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="channels[]" value="mail" id="ch_mail">
                        <label class="form-check-label" for="ch_mail">بريد إلكتروني (Email)</label>
                    </div>
                </div>

                <!-- الجمهور المستهدف -->
                <div class="mb-4">
                    <label class="form-label fw-bold">الجمهور المستهدف <span class="text-danger">*</span></label>
                    <select name="target_audience" id="target_audience" class="form-select" onchange="toggleSpecificUser(this.value)">
                        <option value="all">جميع المستخدمين</option>
                        <option value="sellers">التجار (البائعين) فقط</option>
                        <option value="bidders">المزايدين فقط</option>
                        <option value="specific">مستخدم محدد</option>
                    </select>
                </div>

                <div class="mb-4" id="specific_user_div" style="display: none;">
                    <label class="form-label">رقم تعريف المستخدم (User ID)</label>
                    <input type="number" name="specific_user_id" class="form-control" placeholder="أدخل ID المستخدم المستهدف">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> إرسال الإشعار
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function toggleSpecificUser(val) {
        if(val === 'specific') {
            document.getElementById('specific_user_div').style.display = 'block';
        } else {
            document.getElementById('specific_user_div').style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // AJAX Form Submission
        const form = document.getElementById('send-notification-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if(response.ok) {
                    toastr.success('تمت إضافة الإشعارات إلى الطابور وجاري إرسالها بالخلفية بنجاح.');
                    form.reset();
                    // Reset custom inputs
                    document.getElementById('specific_user_div').style.display = 'none';
                } else {
                    toastr.error('حدث خطأ أثناء الإرسال.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('حدث خطأ غير متوقع.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    });
</script>
@endsection
