<div class="transactions-list" id="transactionsList">
    @forelse($transactions as $tx)
    <div class="tx-item {{ $tx->type }}">
        <div class="tx-icon {{ $tx->type }}">
            @if($tx->type === 'credit')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
            @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
            @endif
        </div>
        <div class="tx-details">
            <div class="tx-title">{{ $tx->type === 'credit' ? __('Deposit') : __('Withdrawal') }}</div>
            <div class="tx-desc">{{ $tx->description ?: '---' }}</div>
            <div class="tx-date">{{ $tx->created_at->translatedFormat('d M Y - h:i A') }}</div>
        </div>
        <div class="tx-amount {{ $tx->type }}">
            <span>{{ $tx->type === 'credit' ? '+' : '-' }} {{ number_format($tx->amount, 2) }}</span>
            <small>{{ __('SAR') }}</small>
        </div>
    </div>
    @empty
    <div class="empty-transactions">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <p>{{ __('No transactions yet.') }}</p>
    </div>
    @endforelse
</div>

{{-- Pagination Links --}}
@if(method_exists($transactions, 'links') && $transactions->hasPages())
    <div style="margin-top: 1.5rem;" class="pagination-wrapper">
        {{ $transactions->links() }}
    </div>
@endif
