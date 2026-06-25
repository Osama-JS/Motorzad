<div class="container-fluid py-4">
    <!-- Premium Header -->
    <div class="premium-hero mb-4" style="padding: 2rem; min-height: auto;">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-shield-alt"></i> {{ __('أمان عالي') }}
            </div>
            <h2 style="font-size: 1.8rem;">{{ __('البيانات البنكية') }} <span class="user-name">{{ __('للسحب') }}</span></h2>
            <p class="mb-0" style="font-size: 0.9rem;">{{ __('قم بإدارة حسابك البنكي لاستقبال أموالك بأمان وسرعة. يرجى التأكد من تطابق اسم الحساب مع هويتك الموثقة.') }}</p>
        </div>
        <div class="hero-glass-stats d-none d-md-flex">
            <div class="glass-stat-item {{ $user->check_bank ? 'green' : 'gold' }}" style="min-width: 150px;">
                <div class="stat-num" style="font-size: 1.2rem;">
                    @if($user->check_bank)
                        <i class="fas fa-check-circle"></i>
                    @else
                        <i class="fas fa-clock"></i>
                    @endif
                </div>
                <div class="stat-label mt-2" style="font-size: 0.85rem;">
                    {{ $user->check_bank ? __('حساب موثق') : __('قيد المراجعة') }}
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show premium-alert shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon-bg bg-success text-white me-3 p-2 rounded-circle">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong>{{ __('نجاح!') }}</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Visual Bank Card Preview -->
        <div class="col-xl-4 col-lg-5 order-lg-2">
            <div class="premium-wallet mb-4" id="visual-bank-card">
                <div class="wallet-header">
                    <div class="wallet-title">{{ __('بطاقة الحساب المسجل') }}</div>
                    <div class="wallet-icon"><i class="fas fa-university"></i></div>
                </div>
                <div class="wallet-balance mt-3" style="font-size: 1.2rem; word-break: break-all; letter-spacing: 2px;" id="preview-iban">
                    {{ $user->iban ? $user->iban : 'SA00 0000 0000 0000 0000 0000' }}
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <div class="text-white-50" style="font-size: 0.8rem;" id="preview-account-number">
                        {{ $user->account_number ? __('رقم الحساب: ') . $user->account_number : __('رقم الحساب غير مدخل') }}
                    </div>
                    <div class="text-white-50" style="font-size: 0.8rem;" id="preview-bank-name">
                        {{ $user->bank_name ? $user->bank_name : __('اسم البنك غير محدد') }}
                    </div>
                </div>
                <div class="wallet-currency mt-1" id="preview-bic">
                    {{ $user->bic_code ? 'BIC: ' . $user->bic_code : __('رمز سويفت غير مدخل') }}
                </div>
                <div class="d-flex justify-content-between align-items-end mt-4 pt-2 border-top border-light border-opacity-25">
                    <div>
                        <div class="text-white-50 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">{{ __('اسم المستفيد') }}</div>
                        <div class="fw-bold" style="font-size: 0.9rem;" id="preview-name">
                            {{ $user->beneficiary_name ? $user->beneficiary_name : __('اسم صاحب الحساب') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-white-50 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">{{ __('الدولة') }}</div>
                        <div class="fw-bold" style="font-size: 0.8rem;" id="preview-country">
                            {{ $user->bank_country ? $user->bank_country : __('غير محدد') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guidelines Card -->
            <div class="premium-card">
                <div class="premium-card-header">
                    <h2 style="font-size: 1rem;"><i class="fas fa-info-circle text-primary"></i> {{ __('إرشادات هامة') }}</h2>
                </div>
                <div class="card-body p-4 text-muted" style="font-size: 0.85rem; line-height: 1.6;">
                    <ul class="ps-3 mb-0" style="list-style-type: circle;">
                        <li class="mb-2">{{ __('تأكد من إدخال رقم الآيبان (IBAN) بصيغة صحيحة لتجنب رفض الحوالة.') }}</li>
                        <li class="mb-2">{{ __('يجب أن يكون اسم المستفيد مطابقاً تماماً للاسم المسجل في هويتك المرفقة لدينا.') }}</li>
                        <li class="mb-2">{{ __('عند تحديث أي بيانات، سيتم إعادة الحساب لحالة "قيد المراجعة" حتى يقوم الفريق المختص بتوثيقه.') }}</li>
                        <li>{{ __('التحويلات تتم فقط للحسابات الموثقة لتوفير أقصى درجات الأمان.') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="col-xl-8 col-lg-7 order-lg-1">
            <div class="premium-card h-100">
                <div class="premium-card-header">
                    <h2 style="font-size: 1.1rem;"><i class="fas fa-edit"></i> {{ __('تحديث معلومات الحساب') }}</h2>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form id="bankDetailsForm" action="{{ route('bidder.bank-details.update') }}" method="POST" class="premium-form">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label class="form-label text-muted fw-bold mb-2">{{ __('اسم المستفيد المسجل في الحساب') }} <span class="text-danger">*</span></label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fas fa-user-tag"></i></span>
                                <input type="text" id="beneficiary_name" name="beneficiary_name" class="form-control custom-input" value="{{ old('beneficiary_name', $user->beneficiary_name) }}" placeholder="{{ __('الاسم الكامل كما هو في البنك') }}" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('اسم البنك') }} <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-building"></i></span>
                                    <select id="bank_name" name="bank_name" class="form-select custom-input" required>
                                        <option value="" data-bic="">{{ __('اختر البنك') }}</option>
                                        <option value="البنك الأهلي السعودي (SNB)" data-bic="NCBKSA" {{ old('bank_name', $user->bank_name) == 'البنك الأهلي السعودي (SNB)' ? 'selected' : '' }}>البنك الأهلي السعودي (SNB)</option>
                                        <option value="مصرف الراجحي" data-bic="RJHI SA RI" {{ old('bank_name', $user->bank_name) == 'مصرف الراجحي' ? 'selected' : '' }}>مصرف الراجحي</option>
                                        <option value="بنك الرياض" data-bic="RIAD SA RI" {{ old('bank_name', $user->bank_name) == 'بنك الرياض' ? 'selected' : '' }}>بنك الرياض</option>
                                        <option value="البنك السعودي الأول (SABB)" data-bic="SABB SA RI" {{ old('bank_name', $user->bank_name) == 'البنك السعودي الأول (SABB)' ? 'selected' : '' }}>البنك السعودي الأول (SABB)</option>
                                        <option value="البنك السعودي الفرنسي" data-bic="BSFR SA RI" {{ old('bank_name', $user->bank_name) == 'البنك السعودي الفرنسي' ? 'selected' : '' }}>البنك السعودي الفرنسي</option>
                                        <option value="مصرف الإنماء" data-bic="INMI SA RI" {{ old('bank_name', $user->bank_name) == 'مصرف الإنماء' ? 'selected' : '' }}>مصرف الإنماء</option>
                                        <option value="بنك البلاد" data-bic="ALBI SA RI" {{ old('bank_name', $user->bank_name) == 'بنك البلاد' ? 'selected' : '' }}>بنك البلاد</option>
                                        <option value="البنك العربي الوطني" data-bic="ARNB SA RI" {{ old('bank_name', $user->bank_name) == 'البنك العربي الوطني' ? 'selected' : '' }}>البنك العربي الوطني</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('رقم الحساب') }} <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" id="account_number" name="account_number" class="form-control custom-input" value="{{ old('account_number', $user->account_number) }}" placeholder="{{ __('رقم الحساب العادي') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-7 mb-4 mb-md-0">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('رقم IBN (الآيبان)') }} <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-money-check"></i></span>
                                    <input type="text" id="iban_input" name="iban" class="form-control custom-input" value="{{ old('iban', $user->iban) }}" placeholder="SA0000000000000000000000" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('رمز البنك (BIC CODE)') }} <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-barcode"></i></span>
                                    <input type="text" id="bic_code_input" name="bic_code" class="form-control custom-input" value="{{ old('bic_code', $user->bic_code) }}" placeholder="{{ __('مثال: NCBKSA') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('دولة البنك') }} <span class="text-danger">*</span></label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-globe-asia"></i></span>
                                    <select name="bank_country" id="bank_country" class="form-select custom-input" required>
                                        <option value="">{{ __('اختر الدولة') }}</option>
                                        @php
                                            $countries = ['Saudi Arabia', 'United Arab Emirates', 'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Egypt', 'Jordan'];
                                        @endphp
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" {{ old('bank_country', $user->bank_country) == $country ? 'selected' : '' }}>
                                                {{ __($country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('مدينة البنك') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-city"></i></span>
                                    <input type="text" name="bank_city" class="form-control custom-input" value="{{ old('bank_city', $user->bank_city) }}" placeholder="{{ __('مدينة الفرع (اختياري)') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('العنوان الأول') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" name="address_1" class="form-control custom-input" value="{{ old('address_1', $user->address_1) }}" placeholder="{{ __('الشارع / الحي') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">{{ __('العنوان الثاني') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="fas fa-map-pin"></i></span>
                                    <input type="text" name="address_2" class="form-control custom-input" value="{{ old('address_2', $user->address_2) }}" placeholder="{{ __('رقم المبنى (اختياري)') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-4">
                            <button type="submit" id="submitBtn" class="btn-premium primary border-0">
                                <span class="btn-text"><i class="fas fa-save"></i> {{ __('حفظ وتحديث البيانات') }}</span>
                                <span class="btn-spinner d-none"><i class="fas fa-spinner fa-spin"></i> {{ __('جاري الحفظ...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
