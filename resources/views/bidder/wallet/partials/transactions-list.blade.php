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
        <div class="tx-amount {{ $tx->type }}" style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
            <div style="display: flex; align-items: baseline; gap: 4px;">
                <span style="font-weight: 700; font-size: 1.1rem; letter-spacing: -0.5px;">{{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }}</span>
                <small style="font-size: 0.75rem; opacity: 0.7; font-weight: 600;">{{ __('SAR') }}</small>
            </div>
            <a href="{{ route('bidder.wallet.invoice', $tx->id) }}" target="_blank" class="tx-invoice-btn" style="
                font-size: 0.7rem; 
                color: var(--text-muted, #888); 
                text-decoration: none; 
                display: inline-flex; 
                align-items: center; 
                gap: 5px; 
                padding: 4px 10px; 
                border-radius: 20px; 
                background: var(--bg-hover, rgba(0,0,0,0.03)); 
                border: 1px solid transparent;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-weight: 600;
            " onmouseover="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.color='#10b981'; this.style.borderColor='rgba(16, 185, 129, 0.3)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='var(--bg-hover, rgba(0,0,0,0.03))'; this.style.color='var(--text-muted, #888)'; this.style.borderColor='transparent'; this.style.transform='translateY(0)';">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                {{ __('الفاتورة') }}
            </a>
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
