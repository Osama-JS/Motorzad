@extends('layouts.admin')

@section('title', __('Wallet Details'))

@section('css')
<style>
    .transaction-type-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .type-credit { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .type-debit { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>{{ __('Wallet Details for:') }} {{ $wallet->user->name }}</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a> / 
            <a href="{{ route('admin.wallets.index') }}">{{ __('Wallets') }}</a> / 
            {{ __('Details') }}
        </div>
    </div>
    <div class="actions">
        <button type="button" class="btn btn-primary" onclick="$('#transactionModal').modal('show')">
            {{ __('Add Financial Transaction') }}
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card blue">
            <div class="stat-value">{{ number_format($wallet->balance, 2) }}</div>
            <div class="stat-label">{{ __('Current Balance') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-value">{{ number_format($wallet->total_deposits, 2) }}</div>
            <div class="stat-label">{{ __('Total Deposits') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card red">
            <div class="stat-value">{{ number_format($wallet->total_withdrawals, 2) }}</div>
            <div class="stat-label">{{ __('Total Withdrawals') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card gold">
            <div class="stat-value">{{ number_format($wallet->debt_ceiling, 2) }}</div>
            <div class="stat-label">{{ __('Debt Ceiling') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ __('Financial Transactions History') }}</h2>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('By') }}</th>
                    <th>{{ __('Attachment') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wallet->transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <span class="transaction-type-badge {{ $transaction->type === 'credit' ? 'type-credit' : 'type-debit' }}">
                            {{ $transaction->type === 'credit' ? __('Deposit') : __('Withdraw') }}
                        </span>
                    </td>
                    <td class="{{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                        {{ $transaction->type === 'credit' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>{{ $transaction->description ?: '---' }}</td>
                    <td>{{ $transaction->creator ? $transaction->creator->name : __('System') }}</td>
                    <td>
                        @if($transaction->attachment_path)
                        <a href="{{ asset('storage/' . $transaction->attachment_path) }}" target="_blank" class="btn btn-sm btn-ghost">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </a>
                        @else
                        ---
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('No financial transactions recorded') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Transaction Modal (same as index for consistency) -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Financial Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="transactionForm" action="{{ route('admin.wallets.transactions.store', $wallet->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Operation Type') }}</label>
                        <select name="type" class="form-control" required>
                            <option value="credit">{{ __('Deposit') }} (Credit)</option>
                            <option value="debit">{{ __('Withdraw') }} (Debit)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Amount') }}</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Description / Notes') }}</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('Attachment (Optional)') }}</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Transaction') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
