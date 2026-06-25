@extends('layouts.bidder')

@section('title', __('البيانات البنكية'))

@section('content')
<div id="bank-details-container">
    @include('bidder.bank-details.partials.content')
</div>
@endsection

@section('css')
<style>
/* Custom Premium Form Styles */
.premium-alert {
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: var(--radius-lg);
    background: var(--bg-card);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.input-group-custom {
    position: relative;
    display: flex;
    align-items: center;
}

.input-group-custom .input-icon {
    position: absolute;
    inset-inline-start: 1rem;
    color: var(--text-muted);
    z-index: 10;
    transition: all 0.3s ease;
}

.custom-input {
    background-color: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0.85rem 1rem;
    padding-inline-start: 2.75rem;
    font-size: 0.95rem;
    color: var(--text);
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

[data-theme="light"] .custom-input {
    background-color: #f8fafc;
}

.custom-input:focus {
    background-color: var(--bg-card);
    border-color: var(--brand-red-light);
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    outline: none;
}

.custom-input:focus + .input-icon,
.custom-input:focus ~ .input-icon {
    color: var(--brand-red-light);
}

.form-select.custom-input {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-size: 16px 12px;
}

/* Card Animations */
#visual-bank-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
#visual-bank-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}
[data-theme="light"] #visual-bank-card:hover {
    box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
}
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('beneficiary_name');
    const bankSelect = document.getElementById('bank_name');
    const accountInput = document.getElementById('account_number');
    const ibanInput = document.getElementById('iban_input');
    const bicInput = document.getElementById('bic_code_input');
    const countrySelect = document.getElementById('bank_country');
    
    const previewName = document.getElementById('preview-name');
    const previewBank = document.getElementById('preview-bank-name');
    const previewAccount = document.getElementById('preview-account-number');
    const previewIban = document.getElementById('preview-iban');
    const previewBic = document.getElementById('preview-bic');
    const previewCountry = document.getElementById('preview-country');

    // Live sync for Name
    nameInput.addEventListener('input', function() {
        previewName.textContent = this.value || '{{ __('اسم صاحب الحساب') }}';
    });

    // Live sync for Bank Name & auto-fill BIC
    bankSelect.addEventListener('change', function() {
        previewBank.textContent = this.options[this.selectedIndex].text || '{{ __('اسم البنك غير محدد') }}';
        
        let selectedBic = this.options[this.selectedIndex].getAttribute('data-bic');
        if (selectedBic) {
            bicInput.value = selectedBic;
            previewBic.textContent = 'BIC: ' + selectedBic;
            bicInput.classList.add('border-success');
            setTimeout(() => bicInput.classList.remove('border-success'), 1000);
        }
    });

    // Live sync for Account Number
    accountInput.addEventListener('input', function() {
        previewAccount.textContent = this.value ? '{{ __('رقم الحساب: ') }}' + this.value : '{{ __('رقم الحساب غير مدخل') }}';
    });

    // Live sync and formatting for IBAN
    ibanInput.addEventListener('input', function() {
        // Remove spaces for clean processing
        let rawVal = this.value.replace(/\s+/g, '').toUpperCase();
        
        // Add space every 4 characters for formatting in preview
        let formatted = rawVal.match(/.{1,4}/g)?.join(' ') || '';
        
        this.value = rawVal; // Keep input without spaces, or add spaces if preferred. Usually input is better without spaces for submission.
        previewIban.textContent = formatted || 'SA00 0000 0000 0000 0000 0000';
    });

    // Live sync for BIC
    bicInput.addEventListener('input', function() {
        previewBic.textContent = this.value ? 'BIC: ' + this.value.toUpperCase() : '{{ __('رمز سويفت غير مدخل') }}';
        this.value = this.value.toUpperCase();
    });

    // Live sync for Country
    countrySelect.addEventListener('change', function() {
        previewCountry.textContent = this.options[this.selectedIndex].text || '{{ __('غير محدد') }}';
    });

    // AJAX Form Submission
    const form = document.getElementById('bankDetailsForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpinner = submitBtn.querySelector('.btn-spinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');

        const formData = new FormData(form);

        BidderAjax.post(form.action, formData, {
            onSuccess: function(data) {
                if (data.success) {
                    // Update badge in header if it exists
                    const headerBadge = document.querySelector('.glass-stat-item');
                    if (headerBadge) {
                        headerBadge.className = 'glass-stat-item gold';
                        headerBadge.querySelector('.stat-num').innerHTML = '<i class="fas fa-clock"></i>';
                        headerBadge.querySelector('.stat-label').textContent = '{{ __('قيد المراجعة') }}';
                    }

                    // Show success toast or alert
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('نجاح!') }}',
                            text: data.message,
                            confirmButtonText: '{{ __('حسناً') }}',
                            confirmButtonColor: '#10b981' // green
                        });
                    } else {
                        alert(data.message);
                    }
                } else {
                    alert('{{ __('حدث خطأ أثناء حفظ البيانات') }}');
                }
            },
            onComplete: function() {
                // Restore button state
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
            }
        });
    });
});
</script>
@endsection
