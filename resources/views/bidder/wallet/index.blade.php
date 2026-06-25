@extends('layouts.bidder')

@section('title', __('My Wallet'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/wallet-profile.css') }}">
<style>
    .modal-backdrop {
        --bs-backdrop-zindex: 0 !important;
         background: var(--bg-body) !important;
    }
    #depositModal .modal-content,#withdrawalModal .modal-content{
        background: var(--bg-body) !important;
    }
</style>
@endsection

@section('content')
<div id="wallet-container">
    @include('bidder.wallet.partials.content')
</div>
@endsection

@section('js')
<script>
function openWithdrawalModal() {
    new bootstrap.Modal(document.getElementById('withdrawalModal')).show();
}
function openDepositModal() {
    new bootstrap.Modal(document.getElementById('depositModal')).show();
}

function loadWalletContent(url, targetContainer = '#wallet-container') {
    $(targetContainer).css('opacity', '0.5');

    BidderAjax.get(url, {}, {
        onSuccess: function(response) {
            $(targetContainer).css('opacity', '1');
            if (response.success && response.html) {
                $(targetContainer).html(response.html);
                if (targetContainer === '#wallet-container') {
                    initWalletEventListeners();
                }
                window.history.pushState(null, null, url);
            } else {
                toastr.error('Failed to load wallet data.');
            }
        },
        onError: function() {
            $(targetContainer).css('opacity', '1');
            toastr.error('Failed to load wallet data.');
        }
    });
}

function initWalletEventListeners() {
    // ===== Bank Card Selection Highlight =====
    document.querySelectorAll('.bank-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.bank-card').forEach(card => {
                card.style.borderColor = 'var(--border)';
                card.style.background = '';
                card.querySelector('.bank-check-icon').style.background = '';
                card.querySelector('.bank-check-icon').style.borderColor = 'var(--border)';
            });
            const card = radio.nextElementSibling;
            card.style.borderColor = '#10b981';
            card.style.background = 'rgba(16,185,129,.04)';
            card.querySelector('.bank-check-icon').style.background = '#10b981';
            card.querySelector('.bank-check-icon').style.borderColor = '#10b981';
        });
        if (radio.checked) radio.dispatchEvent(new Event('change'));
    });

    // ===== Receipt File Preview =====
    const receiptInput = document.getElementById('receiptInput');
    if (receiptInput) {
        receiptInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            document.getElementById('receiptDropzone').style.borderColor = '#10b981';
            document.getElementById('receiptDropzone').style.background = 'rgba(16,185,129,.04)';
            if (file.type.startsWith('image/')) {
                document.getElementById('receiptImg').src = URL.createObjectURL(file);
                document.getElementById('receiptImg').style.display = 'block';
            } else {
                document.getElementById('receiptImg').style.display = 'none';
            }
            document.getElementById('receiptName').textContent = '✓ ' + file.name;
            document.getElementById('receiptPlaceholder').style.display = 'none';
            document.getElementById('receiptPreview').style.display = 'block';
        });
    }

    // ===== Drag & Drop on Dropzone =====
    const dropzone = document.getElementById('receiptDropzone');
    if (dropzone) {
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.style.borderColor = '#10b981'; });
        dropzone.addEventListener('dragleave', () => { dropzone.style.borderColor = 'var(--border)'; });
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            receiptInput.files = dt.files;
            receiptInput.dispatchEvent(new Event('change'));
        });
    }

    // ===== Withdrawal Form Submit =====
    const withdrawalForm = document.getElementById('withdrawalForm');
    if (withdrawalForm) {
        withdrawalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('withdrawSubmitBtn');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83"/></svg> {{ __("جاري المعالجة...") }}';
            btn.disabled = true;

            BidderAjax.post("{{ route('bidder.wallet.withdraw') }}", new FormData(this), {
                onSuccess: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('withdrawalModal')).hide();
                        // Reload the entire wallet stats and tables dynamically
                        loadWalletContent("{{ route('bidder.wallet.index') }}");
                    } else {
                        toastr.error(data.message || '{{ __("حدث خطأ") }}');
                    }
                },
                onComplete: function() {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            });
        });
    }

    // ===== Deposit Form Submit =====
    const depositForm = document.getElementById('depositForm');
    if (depositForm) {
        depositForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('depositSubmitBtn');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4"/></svg> {{ __("جاري الرفع...") }}';
            btn.disabled = true;

            BidderAjax.post("{{ route('bidder.wallet.deposit') }}", new FormData(this), {
                onSuccess: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('depositModal')).hide();
                        // Reload the entire wallet stats and tables dynamically
                        loadWalletContent("{{ route('bidder.wallet.index') }}");
                    } else {
                        toastr.error(data.message || '{{ __("حدث خطأ") }}');
                    }
                },
                onComplete: function() {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            });
        });
    }
}

$(document).ready(function() {
    initWalletEventListeners();

    // Transactions type filter via AJAX
    $(document).on('change', '#txTypeFilter', function(e) {
        const type = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('type', type);
        url.searchParams.delete('page'); // Reset pagination on filter change
        loadWalletContent(url.toString(), '#transactions-container');
    });

    // Transactions list pagination links click handler via AJAX
    $(document).on('click', '#transactions-container .pagination-wrapper a, #transactions-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadWalletContent(url, '#transactions-container');
        }
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        loadWalletContent(window.location.href, '#wallet-container');
    });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endsection
