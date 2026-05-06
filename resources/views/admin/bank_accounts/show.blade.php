@extends('layouts.admin')

@section('title', __('Bank Account Details'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white pb-0">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">{{ __('Account Information') }}</h6>
                        <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            @if($bank_account->logo_path)
                                <img src="{{ asset('storage/' . $bank_account->logo_path) }}" class="rounded shadow" width="80" alt="{{ $bank_account->bank_name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                                    <i class="fas fa-university fa-2x text-secondary"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h4 class="mb-0 font-weight-bolder text-primary">{{ $bank_account->bank_name }}</h4>
                            <p class="text-sm mb-0">
                                @if($bank_account->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Beneficiary Name') }}</label>
                            <h6 class="text-sm font-weight-bold">{{ $bank_account->beneficiary_name }}</h6>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('IBAN') }}</label>
                            <h6 class="text-sm font-weight-bold"><code>{{ $bank_account->iban }}</code></h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Created At') }}</label>
                            <h6 class="text-sm">{{ $bank_account->created_at->format('Y-m-d H:i') }}</h6>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Last Updated') }}</label>
                            <h6 class="text-sm">{{ $bank_account->updated_at->format('Y-m-d H:i') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 text-center">
                <div class="card-body">
                    <h6 class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-3">{{ __('Usage Statistics') }}</h6>
                    <div class="row">
                        <div class="col-12">
                            <h2 class="font-weight-bolder text-primary mb-0">{{ $stats['total_count'] }}</h2>
                            <p class="text-sm text-uppercase font-weight-bold">{{ __('Total Transfers') }}</p>
                        </div>
                    </div>
                    <hr class="horizontal dark mt-3 mb-3">
                    <button class="btn btn-primary btn-sm w-100" onclick="editAccount({{ $bank_account->id }})">
                        <i class="fas fa-edit me-1"></i> {{ __('Edit Account') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
