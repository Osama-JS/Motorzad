@extends('layouts.bidder')

@section('title', app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions')

@section('css')
<style>
/* ===== PREMIUM WON AUCTIONS VIEW ===== */
.bids-header {
    background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(22, 33, 62, 0.98));
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.bids-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.15), transparent 50%), 
                radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1), transparent 50%);
    pointer-events: none;
}
.bids-header-inner {
    position: relative;
    z-index: 2;
}
.bids-header h1 {
    font-size: 2.2rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}
.bids-header p {
    opacity: 0.8;
    font-size: 1.05rem;
    max-width: 600px;
}

/* Stats Widgets Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.stat-widget {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s;
}
.stat-widget:hover {
    transform: translateY(-3px);
}
.stat-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-icon-wrap.won { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.stat-icon-wrap.value { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.stat-icon-wrap.action { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.stat-info {
    display: flex;
    flex-direction: column;
}
.stat-val {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--text);
}
.stat-lbl {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
}

/* Wide List Row design */
.bids-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.bid-row-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.25rem;
    display: flex;
    gap: 1.5rem;
    align-items: center;
    transition: all 0.3s;
}
.bid-row-card:hover {
    border-color: var(--text-muted);
}
.bid-img-wrap {
    width: 140px;
    height: 95px;
    border-radius: 12px;
    overflow: hidden;
    background: #0b0f19;
    flex-shrink: 0;
    position: relative;
}
.bid-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.bid-details {
    flex: 1;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
    gap: 1.5rem;
    align-items: center;
}
.veh-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.veh-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text);
    text-decoration: none;
    transition: color 0.3s;
}
.veh-title:hover {
    color: var(--brand-red);
}
.veh-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.price-tag {
    display: flex;
    flex-direction: column;
}
.price-tag .label {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 700;
    margin-bottom: 0.25rem;
}
.price-tag .amount {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text);
}
.price-tag .amount.won-amount {
    color: #10b981;
}

/* Bid State Badges styling */
.bid-state-badge {
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    width: fit-content;
}
.bid-state-badge.won {
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #10b981;
}
.pay-state-badge.paid {
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #10b981;
}
.pay-state-badge.pending {
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

/* Tabs & Filters */
.filters-bar {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.auc-tabs {
    display: flex;
    gap: 0.5rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    padding: 0.35rem;
    border-radius: 14px;
}
.auc-tab {
    padding: 0.65rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-muted);
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    cursor: pointer;
}
.auc-tab:hover {
    color: var(--text);
    background: var(--bg-hover);
}
.auc-tab.active {
    background: var(--brand-red);
    color: white !important;
    box-shadow: 0 4px 15px rgba(229, 62, 62, 0.25);
}

.action-col {
    display: flex;
    justify-content: flex-end;
}
.btn-action-view {
    background: #10b981;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.6rem 1.2rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    cursor: pointer;
}
.btn-action-view:hover {
    background: #059669;
    color: white;
    transform: translateY(-1px);
}
.btn-action-view.secondary-outline {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text);
    box-shadow: none;
}
.btn-action-view.secondary-outline:hover {
    background: var(--bg-hover);
    color: var(--text);
}

/* ===================================================
   COMPLETE PURCHASE MODAL (cp-*)
   =================================================== */
.cp-modal {
    --cp-accent: #10b981;
    --cp-accent-2: #059669;
    --cp-accent-glow: rgba(16, 185, 129, 0.18);
    --cp-gold: #f59e0b;
    --cp-surface: var(--bg-card-solid, #ffffff);
    --cp-surface-2: var(--bg-body, #f0f2f5);
    --cp-line: var(--border, rgba(0,0,0,0.08));
}
.cp-modal .modal-dialog {
    max-width: 980px;
}
.cp-modal.fade .modal-dialog {
    transform: translateY(24px) scale(0.97);
    opacity: 0;
    transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease;
}
.cp-modal.show .modal-dialog {
    transform: none;
    opacity: 1;
}
.cp-modal .modal-content {
    background: var(--cp-surface);
    border: 1px solid var(--cp-line);
    border-radius: 24px;
    box-shadow: 0 30px 80px -20px rgba(2, 6, 23, 0.45), 0 0 0 1px rgba(255,255,255,0.02) inset;
    color: var(--text);
    overflow: hidden;
}
.modal-backdrop.show {
    opacity: 0.65;
    backdrop-filter: blur(4px);
}

/* ---- Hero header ---- */
.cp-hero {
    position: relative;
    padding: 1.75rem 2rem 1.5rem;
    background: linear-gradient(135deg, #0b1324 0%, #0f1f2e 55%, #0a2a22 100%);
    color: #fff;
    overflow: hidden;
    flex-shrink: 0;
}
.cp-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 85% -10%, rgba(16, 185, 129, 0.35), transparent 45%),
        radial-gradient(circle at 0% 120%, rgba(59, 130, 246, 0.22), transparent 50%);
    pointer-events: none;
}
.cp-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
    background-size: 28px 28px;
    mask-image: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent);
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent);
    pointer-events: none;
}
.cp-hero-top {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.cp-trophy {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    color: #fff;
    background: linear-gradient(135deg, #34d399, #059669);
    box-shadow: 0 10px 30px -6px rgba(16, 185, 129, 0.6), inset 0 1px 0 rgba(255,255,255,0.35);
    animation: cpFloat 3.5s ease-in-out infinite;
}
.cp-hero-text {
    flex: 1;
    min-width: 0;
}
.cp-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #6ee7b7;
    margin-bottom: 0.2rem;
}
.cp-eyebrow .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #34d399;
    box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7);
    animation: cpPulse 1.8s infinite;
}
.cp-hero h2 {
    font-size: 1.45rem;
    font-weight: 900;
    margin: 0;
    color: #fff;
    line-height: 1.3;
}
.cp-close {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.8);
    display: grid;
    place-items: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.25s ease;
}
.cp-close:hover {
    background: rgba(255,255,255,0.14);
    color: #fff;
    transform: rotate(90deg);
}

/* Progress stepper */
.cp-stepper {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    margin-top: 1.5rem;
    gap: 0.5rem;
}
.cp-stage {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    font-size: 0.82rem;
    font-weight: 700;
    color: rgba(255,255,255,0.5);
    white-space: nowrap;
}
.cp-stage-dot {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    font-size: 0.78rem;
    font-weight: 900;
    border: 1.5px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.04);
    flex-shrink: 0;
}
.cp-stage.done { color: #a7f3d0; }
.cp-stage.done .cp-stage-dot {
    background: #10b981;
    border-color: #10b981;
    color: #fff;
}
.cp-stage.active { color: #fff; }
.cp-stage.active .cp-stage-dot {
    border-color: #fbbf24;
    color: #fbbf24;
    background: rgba(251, 191, 36, 0.12);
    box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.6);
    animation: cpPulseGold 2s infinite;
}
.cp-stage-line {
    flex: 1;
    height: 2px;
    min-width: 20px;
    border-radius: 2px;
    background: rgba(255,255,255,0.12);
    position: relative;
    overflow: hidden;
}
.cp-stage-line.done {
    background: linear-gradient(90deg, #10b981, #fbbf24);
}
[dir="rtl"] .cp-stage-line.done {
    background: linear-gradient(-90deg, #10b981, #fbbf24);
}

/* ---- Body ---- */
.cp-body {
    padding: 1.75rem 2rem;
    background: var(--cp-surface);
}
.cp-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr);
    gap: 1.75rem;
}
.cp-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    min-width: 0;
}
.cp-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--text-secondary, var(--text-muted));
    margin: 0 0 0.75rem;
}
.cp-section-title svg { color: var(--cp-accent); }
.cp-anim {
    opacity: 0;
    transform: translateY(12px);
}
.cp-modal.show .cp-anim {
    animation: cpFadeUp 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
.cp-modal.show .cp-anim:nth-child(2) { animation-delay: 0.08s; }
.cp-modal.show .cp-anim:nth-child(3) { animation-delay: 0.16s; }
.cp-modal.show .cp-anim:nth-child(4) { animation-delay: 0.24s; }

/* Vehicle card */
.cp-vehicle {
    display: flex;
    gap: 1rem;
    align-items: center;
    padding: 0.85rem;
    border-radius: 18px;
    background: var(--cp-surface-2);
    border: 1px solid var(--cp-line);
}
.cp-vehicle-img {
    width: 110px;
    height: 78px;
    border-radius: 14px;
    overflow: hidden;
    flex-shrink: 0;
    background: #0b0f19;
    position: relative;
}
.cp-vehicle-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.cp-vehicle-img::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
}
.cp-vehicle-info { min-width: 0; flex: 1; }
.cp-vehicle-title {
    font-size: 1.02rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 0.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cp-vehicle-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.cp-ref {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', monospace;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    border-radius: 8px;
    background: rgba(59, 130, 246, 0.08);
    color: #3b82f6;
    border: 1px dashed rgba(59, 130, 246, 0.35);
    cursor: pointer;
    transition: all 0.2s;
    direction: ltr;
}
.cp-ref:hover { background: rgba(59, 130, 246, 0.15); }

/* Invoice */
.cp-invoice {
    border-radius: 18px;
    border: 1px solid var(--cp-line);
    background: var(--cp-surface);
    overflow: hidden;
    position: relative;
}
.cp-invoice-rows { padding: 1.1rem 1.25rem 0.9rem; }
.cp-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.45rem 0;
    font-size: 0.9rem;
}
.cp-row .k { color: var(--text-secondary, var(--text-muted)); font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; }
.cp-row .v { color: var(--text); font-weight: 800; font-variant-numeric: tabular-nums; }
.cp-row .v small { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); margin-inline-start: 0.2rem; }
.cp-row .tag {
    font-size: 0.65rem;
    font-weight: 800;
    padding: 0.1rem 0.45rem;
    border-radius: 6px;
    background: rgba(245, 158, 11, 0.12);
    color: var(--cp-gold);
}
.cp-total {
    position: relative;
    padding: 1.1rem 1.25rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.03));
    border-top: 1.5px dashed var(--cp-line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.cp-total::before,
.cp-total::after {
    content: '';
    position: absolute;
    top: -9px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--cp-surface);
    border: 1px solid var(--cp-line);
}
.cp-total::before { left: -9px; }
.cp-total::after { right: -9px; }
.cp-total-label {
    font-size: 0.78rem;
    font-weight: 800;
    color: var(--text-secondary, var(--text-muted));
    margin-bottom: 0.15rem;
}
.cp-total-amount {
    font-size: 1.85rem;
    font-weight: 900;
    line-height: 1.1;
    background: linear-gradient(135deg, #10b981, #0ea5e9);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    font-variant-numeric: tabular-nums;
    direction: ltr;
    display: inline-block;
}
.cp-total-amount .cur {
    font-size: 0.95rem;
    font-weight: 800;
    margin-inline-start: 0.25rem;
}
.cp-copy-amount {
    border: 1px solid rgba(16, 185, 129, 0.3);
    background: var(--cp-surface);
    color: var(--cp-accent);
    border-radius: 12px;
    padding: 0.5rem 0.8rem;
    font-size: 0.78rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}
.cp-copy-amount:hover {
    background: var(--cp-accent);
    color: #fff;
    border-color: var(--cp-accent);
}
.cp-note {
    display: flex;
    gap: 0.65rem;
    align-items: flex-start;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    font-size: 0.82rem;
    line-height: 1.6;
    font-weight: 600;
}
.cp-note svg { flex-shrink: 0; margin-top: 2px; }
.cp-note.info {
    background: rgba(59, 130, 246, 0.07);
    color: var(--text-secondary, var(--text));
    border: 1px solid rgba(59, 130, 246, 0.18);
}
.cp-note.info svg { color: #3b82f6; }
.cp-note.warn {
    background: rgba(245, 158, 11, 0.07);
    color: var(--text-secondary, var(--text));
    border: 1px solid rgba(245, 158, 11, 0.2);
}
.cp-note.warn svg { color: var(--cp-gold); }
.cp-note b { color: var(--text); }

/* Timeline */
.cp-timeline {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}
.cp-timeline::before {
    content: '';
    position: absolute;
    inset-inline-start: 17px;
    top: 18px;
    bottom: 18px;
    width: 2px;
    background: linear-gradient(to bottom, var(--cp-accent), rgba(16,185,129,0.1));
    border-radius: 2px;
}
.cp-tl-item {
    position: relative;
    display: flex;
    gap: 0.9rem;
    align-items: flex-start;
}
.cp-tl-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    background: var(--cp-surface);
    border: 1.5px solid rgba(16, 185, 129, 0.35);
    color: var(--cp-accent);
    position: relative;
    z-index: 1;
    transition: all 0.25s;
}
.cp-tl-item:hover .cp-tl-icon {
    background: var(--cp-accent);
    color: #fff;
    transform: scale(1.06);
}
.cp-tl-title {
    font-size: 0.9rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 0.1rem;
}
.cp-tl-desc {
    font-size: 0.8rem;
    color: var(--text-muted);
    line-height: 1.55;
    font-weight: 500;
}

/* Bank cards */
.cp-banks {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.cp-bank {
    position: relative;
    border-radius: 16px;
    padding: 1rem 1.1rem;
    background: var(--cp-surface-2);
    border: 1px solid var(--cp-line);
    transition: border-color 0.25s, box-shadow 0.25s, transform 0.25s;
    overflow: hidden;
}
.cp-bank::before {
    content: '';
    position: absolute;
    inset-inline-start: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #10b981, #0ea5e9);
    opacity: 0;
    transition: opacity 0.25s;
}
.cp-bank:hover {
    border-color: rgba(16, 185, 129, 0.4);
    box-shadow: 0 10px 28px -12px rgba(16, 185, 129, 0.35);
    transform: translateY(-2px);
}
.cp-bank:hover::before { opacity: 1; }
.cp-bank-head {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}
.cp-bank-logo {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: var(--cp-surface);
    border: 1px solid var(--cp-line);
    display: grid;
    place-items: center;
    overflow: hidden;
    flex-shrink: 0;
    font-weight: 900;
    font-size: 1rem;
    color: var(--cp-accent);
}
.cp-bank-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 4px;
}
.cp-bank-name {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--text);
    line-height: 1.3;
}
.cp-bank-benef {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
}
.cp-iban {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.6rem 0.6rem 0.6rem 0.9rem;
    border-radius: 12px;
    background: var(--cp-surface);
    border: 1px dashed var(--cp-line);
}
[dir="rtl"] .cp-iban { padding: 0.6rem 0.9rem 0.6rem 0.6rem; }
.cp-iban-lbl {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 0.1rem;
}
.cp-iban-val {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', monospace;
    font-size: 0.86rem;
    font-weight: 700;
    color: var(--text);
    letter-spacing: 0.02em;
    direction: ltr;
    text-align: start;
    word-break: break-all;
}
.cp-copy-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: none;
    border-radius: 10px;
    padding: 0.5rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 800;
    background: rgba(16, 185, 129, 0.1);
    color: var(--cp-accent);
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.2s;
}
.cp-copy-btn:hover {
    background: var(--cp-accent);
    color: #fff;
}
.cp-copy-btn.copied,
.cp-copy-amount.copied,
.cp-ref.copied {
    background: var(--cp-accent);
    color: #fff;
    border-color: var(--cp-accent);
}
.cp-copy-btn .ic-check,
.cp-copy-amount .ic-check,
.cp-ref .ic-check { display: none; }
.copied .ic-check { display: inline; animation: cpPop 0.3s ease; }
.copied .ic-copy { display: none; }
.cp-empty-banks {
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px dashed var(--cp-line);
    text-align: center;
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 600;
}

/* ---- Footer ---- */
.cp-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.1rem 2rem;
    border-top: 1px solid var(--cp-line);
    background: var(--cp-surface-2);
    flex-shrink: 0;
}
.cp-secure {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-muted);
}
.cp-secure svg { color: var(--cp-accent); }
.cp-actions { display: flex; gap: 0.65rem; flex-wrap: wrap; }
.cp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.35rem;
    border-radius: 14px;
    font-weight: 800;
    font-size: 0.88rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.25s ease;
    border: 1px solid transparent;
    white-space: nowrap;
}
.cp-btn-ghost {
    background: transparent;
    border-color: var(--cp-line);
    color: var(--text);
}
.cp-btn-ghost:hover {
    background: var(--bg-hover);
    color: var(--text);
}
.cp-btn-primary {
    position: relative;
    overflow: hidden;
    color: #fff;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 10px 24px -8px rgba(16, 185, 129, 0.65);
}
.cp-btn-primary::after {
    content: '';
    position: absolute;
    top: 0;
    left: -120%;
    width: 60%;
    height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,0.35), transparent);
    transform: skewX(-20deg);
    animation: cpShine 3.2s ease-in-out infinite;
}
.cp-btn-primary:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 14px 30px -8px rgba(16, 185, 129, 0.75);
}
.cp-btn-primary .cp-arrow { transition: transform 0.25s; }
.cp-btn-primary:hover .cp-arrow { transform: translateX(3px); }
[dir="rtl"] .cp-arrow { transform: scaleX(-1); }
[dir="rtl"] .cp-btn-primary:hover .cp-arrow { transform: scaleX(-1) translateX(3px); }

/* Light theme tune */
[data-theme="light"] .cp-modal .modal-content {
    box-shadow: 0 30px 80px -20px rgba(15, 23, 42, 0.35);
}

/* Keyframes */
@keyframes cpFadeUp { to { opacity: 1; transform: none; } }
@keyframes cpFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
@keyframes cpPulse {
    0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(52, 211, 153, 0); }
    100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
}
@keyframes cpPulseGold {
    0% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.55); }
    70% { box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); }
    100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); }
}
@keyframes cpShine { 0% { left: -120%; } 55%, 100% { left: 160%; } }
@keyframes cpPop { 0% { transform: scale(0.4); } 70% { transform: scale(1.2); } 100% { transform: scale(1); } }

@media (prefers-reduced-motion: reduce) {
    .cp-modal *, .cp-modal *::before, .cp-modal *::after { animation: none !important; transition: none !important; }
    .cp-anim { opacity: 1; transform: none; }
}

@media (max-width: 991px) {
    .cp-grid { grid-template-columns: 1fr; }
}
@media (max-width: 575px) {
    .cp-modal .modal-dialog { margin: 0.5rem; }
    .cp-hero { padding: 1.25rem 1.25rem 1.1rem; }
    .cp-hero h2 { font-size: 1.15rem; }
    .cp-trophy { width: 46px; height: 46px; border-radius: 14px; }
    .cp-stage-label { display: none; }
    .cp-body { padding: 1.25rem; }
    .cp-footer { padding: 1rem 1.25rem; flex-direction: column; align-items: stretch; }
    .cp-actions { flex-direction: column-reverse; }
    .cp-btn { width: 100%; }
    .cp-total-amount { font-size: 1.5rem; }
    .cp-vehicle-img { width: 88px; height: 64px; }
}

@media(max-width: 991px) {
    .bid-row-card {
        flex-direction: column;
        align-items: stretch;
    }
    .bid-img-wrap {
        width: 100%;
        height: 180px;
    }
    .bid-details {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .action-col {
        justify-content: flex-start;
        margin-top: 0.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="bids-header">
    <div class="bids-header-inner">
        <h1>{{ app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions' }}</h1>
        <p>{{ app()->getLocale() === 'ar' ? 'تهانينا! هنا يمكنك متابعة وإتمام إجراءات الشراء لجميع المركبات والمزادات التي فزت بها.' : 'Congratulations! Here you can monitor and finalize the purchase steps for all vehicles you have won.' }}</p>
    </div>
</div>

{{-- Dynamic Stats Widgets Row --}}
@php
    $totalCount = $auctions->count();
    $totalValue = 0;
    foreach($auctions as $auc) {
        $totalValue += is_array($auc) ? $auc['current_price'] : ($auc->winning_bid_amount ?: $auc->current_price);
    }
@endphp
<div class="stats-row">
    <div class="stat-widget">
        <div class="stat-icon-wrap won">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ $totalCount }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'المزادات الفائزة' : 'Won Auctions' }}</span>
        </div>
    </div>
    
    <div class="stat-widget">
        <div class="stat-icon-wrap value">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val">{{ number_format($totalValue) }} {{ __('SAR') }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'إجمالي القيمة الفائزة' : 'Total Winning Value' }}</span>
        </div>
    </div>

    <div class="stat-widget">
        <div class="stat-icon-wrap action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-val" style="color: #f59e0b;">{{ app()->getLocale() === 'ar' ? 'بانتظار السداد' : 'Pending Payment' }}</span>
            <span class="stat-lbl">{{ app()->getLocale() === 'ar' ? 'الخطوة التالية' : 'Next Step' }}</span>
        </div>
    </div>
</div>

{{-- ===== FILTER TABS ===== --}}
<div class="filters-bar" style="margin-top: 1rem;">
    <div class="auc-tabs">
        <a href="#" data-status="" class="auc-tab won-filter-tab {{ empty($paymentStatusFilter) ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
        </a>
        <a href="#" data-status="pending" class="auc-tab won-filter-tab {{ ($paymentStatusFilter ?? '') === 'pending' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'بانتظار الدفع' : 'Pending Payment' }}
        </a>
        <a href="#" data-status="paid" class="auc-tab won-filter-tab {{ ($paymentStatusFilter ?? '') === 'paid' ? 'active' : '' }}">
            {{ app()->getLocale() === 'ar' ? 'تم السداد ومكتملة' : 'Paid / Completed' }}
        </a>
    </div>
</div>

{{-- Main List Container --}}
<div id="won-auctions-container">
    @include('bidder.auctions.partials.won-auctions-list')
</div>

@endsection

@section('modals')
{{-- COMPLETE PURCHASE MODAL --}}
@php $isAr = app()->getLocale() === 'ar'; @endphp
<div class="modal fade cp-modal" id="completePurchaseModal" tabindex="-1" aria-labelledby="completePurchaseModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- Hero Header --}}
            <div class="cp-hero">
                <div class="cp-hero-top">
                    <div class="cp-trophy">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <div class="cp-hero-text">
                        <div class="cp-eyebrow"><span class="dot"></span>{{ $isAr ? 'تهانينا بالفوز' : 'Congratulations' }}</div>
                        <h2 id="completePurchaseModalTitle">{{ $isAr ? 'إتمام شراء المركبة' : 'Complete Vehicle Purchase' }}</h2>
                    </div>
                    <button type="button" class="cp-close" data-bs-dismiss="modal" aria-label="{{ $isAr ? 'إغلاق' : 'Close' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="cp-stepper" aria-hidden="true">
                    <div class="cp-stage done">
                        <span class="cp-stage-dot"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                        <span class="cp-stage-label">{{ $isAr ? 'الفوز بالمزاد' : 'Auction Won' }}</span>
                    </div>
                    <div class="cp-stage-line done"></div>
                    <div class="cp-stage active">
                        <span class="cp-stage-dot">2</span>
                        <span class="cp-stage-label">{{ $isAr ? 'سداد المبلغ' : 'Payment' }}</span>
                    </div>
                    <div class="cp-stage-line"></div>
                    <div class="cp-stage">
                        <span class="cp-stage-dot">3</span>
                        <span class="cp-stage-label">{{ $isAr ? 'استلام المركبة' : 'Delivery' }}</span>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-body cp-body">
                <div class="cp-grid">

                    {{-- Column A: Order summary --}}
                    <div class="cp-col">
                        <div class="cp-anim">
                            <h3 class="cp-section-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                {{ $isAr ? 'المركبة' : 'Vehicle' }}
                            </h3>
                            <div class="cp-vehicle">
                                <div class="cp-vehicle-img"><img id="cp-vehicle-img" src="" alt=""></div>
                                <div class="cp-vehicle-info">
                                    <div class="cp-vehicle-title" id="modal-vehicle-title"></div>
                                    <div class="cp-vehicle-meta" id="cp-vehicle-meta"></div>
                                    <button type="button" class="cp-ref" id="cp-ref" data-copy="" title="{{ $isAr ? 'نسخ رقم المرجع' : 'Copy reference' }}">
                                        <svg class="ic-copy" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        <svg class="ic-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span id="cp-ref-text"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="cp-anim">
                            <h3 class="cp-section-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/></svg>
                                {{ $isAr ? 'ملخص الفاتورة' : 'Invoice Summary' }}
                            </h3>
                            <div class="cp-invoice">
                                <div class="cp-invoice-rows">
                                    <div class="cp-row">
                                        <span class="k">{{ $isAr ? 'سعر الفوز بالمزاد' : 'Winning bid' }}</span>
                                        <span class="v"><span id="cp-bid"></span><small>{{ __('SAR') }}</small></span>
                                    </div>
                                    <div class="cp-row" id="cp-row-commission">
                                        <span class="k">{{ $isAr ? 'عمولة المنصة' : 'Platform commission' }}</span>
                                        <span class="v"><span id="cp-commission"></span><small>{{ __('SAR') }}</small></span>
                                    </div>
                                    <div class="cp-row" id="cp-row-vat">
                                        <span class="k">{{ $isAr ? 'ضريبة القيمة المضافة' : 'VAT' }}</span>
                                        <span class="v"><span id="cp-vat"></span><small>{{ __('SAR') }}</small></span>
                                    </div>
                                    <div class="cp-row" id="cp-row-deposit">
                                        <span class="k">{{ $isAr ? 'مبلغ التأمين المحجوز' : 'Held deposit' }} <span class="tag">{{ $isAr ? 'محجوز' : 'Held' }}</span></span>
                                        <span class="v"><span id="cp-deposit"></span><small>{{ __('SAR') }}</small></span>
                                    </div>
                                </div>
                                <div class="cp-total">
                                    <div>
                                        <div class="cp-total-label">{{ $isAr ? 'المبلغ المطلوب سداده' : 'Amount due' }}</div>
                                        <span class="cp-total-amount"><span id="modal-total-amount"></span><span class="cur">{{ __('SAR') }}</span></span>
                                    </div>
                                    <button type="button" class="cp-copy-amount" id="cp-copy-amount" data-copy="">
                                        <svg class="ic-copy" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        <svg class="ic-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span class="lbl">{{ $isAr ? 'نسخ المبلغ' : 'Copy' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="cp-note warn cp-anim">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <div>
                                {!! $isAr
                                    ? 'يرجى كتابة <b>رقم المرجع</b> في وصف الحوالة البنكية لتسريع مطابقة الدفعة وتأكيدها.'
                                    : 'Please include the <b>reference number</b> in your bank transfer description to speed up verification.' !!}
                            </div>
                        </div>
                    </div>

                    {{-- Column B: Steps + Banks --}}
                    <div class="cp-col">
                        <div class="cp-anim">
                            <h3 class="cp-section-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                {{ $isAr ? 'خطوات السداد والاستلام' : 'Payment & Delivery Steps' }}
                            </h3>
                            <div class="cp-timeline">
                                <div class="cp-tl-item">
                                    <div class="cp-tl-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>
                                    </div>
                                    <div>
                                        <div class="cp-tl-title">{{ $isAr ? 'حوّل المبلغ بنكياً' : 'Make a bank transfer' }}</div>
                                        <div class="cp-tl-desc">{{ $isAr ? 'قم بتحويل المبلغ المطلوب إلى أحد حساباتنا البنكية المعتمدة أدناه.' : 'Transfer the amount due to one of our approved bank accounts below.' }}</div>
                                    </div>
                                </div>
                                <div class="cp-tl-item">
                                    <div class="cp-tl-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    </div>
                                    <div>
                                        <div class="cp-tl-title">{{ $isAr ? 'ارفع إيصال التحويل' : 'Upload the receipt' }}</div>
                                        <div class="cp-tl-desc">{{ $isAr ? 'من محفظتك الإلكترونية، أنشئ طلب إيداع جديد وأرفق صورة الإيصال.' : 'From your wallet, create a new deposit request and attach the receipt.' }}</div>
                                    </div>
                                </div>
                                <div class="cp-tl-item">
                                    <div class="cp-tl-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                                    </div>
                                    <div>
                                        <div class="cp-tl-title">{{ $isAr ? 'استلم مركبتك' : 'Receive your vehicle' }}</div>
                                        <div class="cp-tl-desc">{{ $isAr ? 'يتواصل معك فريق خدمة العملاء فور تأكيد الدفعة لترتيب التسليم.' : 'Our team will contact you to arrange delivery once payment is verified.' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cp-anim">
                            <h3 class="cp-section-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                {{ $isAr ? 'حساباتنا البنكية المعتمدة' : 'Approved Bank Accounts' }}
                            </h3>
                            <div class="cp-banks">
                                @forelse($bankAccounts as $acc)
                                    <div class="cp-bank">
                                        <div class="cp-bank-head">
                                            <div class="cp-bank-logo">
                                                @if(!empty($acc->logo_path))
                                                    <img src="{{ asset('storage/' . $acc->logo_path) }}" alt="{{ $acc->bank_name }}">
                                                @else
                                                    {{ mb_substr($acc->bank_name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div style="min-width:0;">
                                                <div class="cp-bank-name">{{ $acc->bank_name }}</div>
                                                <div class="cp-bank-benef">{{ $isAr ? 'المستفيد:' : 'Beneficiary:' }} {{ $acc->beneficiary_name }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-iban">
                                            <div style="min-width:0;">
                                                <div class="cp-iban-lbl">IBAN</div>
                                                <div class="cp-iban-val">{{ trim(chunk_split(str_replace(' ', '', $acc->iban), 4, ' ')) }}</div>
                                            </div>
                                            <button type="button" class="cp-copy-btn iban-copy-btn" data-copy="{{ str_replace(' ', '', $acc->iban) }}">
                                                <svg class="ic-copy" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                                <svg class="ic-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                <span class="lbl">{{ $isAr ? 'نسخ' : 'Copy' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="cp-empty-banks">
                                        {{ $isAr ? 'لا توجد حسابات بنكية متاحة حالياً، يرجى التواصل مع الدعم.' : 'No bank accounts available right now. Please contact support.' }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="cp-footer">
                <span class="cp-secure">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    {{ $isAr ? 'عملية آمنة ومُراجَعة من فريق موتورزاد' : 'Secure & verified by the Motorazad team' }}
                </span>
                <div class="cp-actions" style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end;">
                    <button type="button" class="cp-btn cp-btn-ghost" data-bs-dismiss="modal">
                        {{ $isAr ? 'إغلاق' : 'Close' }}
                    </button>
                    <a href="{{ route('bidder.wallet.index') }}" class="cp-btn cp-btn-primary" id="cp-go-wallet" style="background: white; color: var(--text); border: 1px solid var(--border);">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                        {{ $isAr ? 'إرفاق إيصال تحويل' : 'Attach Receipt' }}
                    </a>
                    <a href="#" class="cp-btn cp-btn-primary" id="cp-pay-online" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white;">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        {{ $isAr ? 'الدفع إلكترونياً (مدى / فيزا)' : 'Pay Online' }}
                        <svg class="cp-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Initial active tab state sync from URL
    syncWonFormInputs(window.location.href);

    // Tab switching click handler via AJAX
    $(document).on('click', '.won-filter-tab', function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        
        // Visual feedback
        $('.won-filter-tab').removeClass('active');
        $(this).addClass('active');

        // Construct the AJAX URL
        const baseUrl = '{{ route("bidder.auctions.won") }}';
        const url = status ? (baseUrl + '?payment_status=' + status) : baseUrl;

        loadWonAuctions(url);
    });

    // Pagination links click handler via AJAX
    $(document).on('click', '#won-auctions-container .pagination-wrapper a, #won-auctions-container .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadWonAuctions(url);
        }
    });

    function loadWonAuctions(url) {
        $('#won-auctions-container').css('opacity', '0.5');

        BidderAjax.get(url, {}, {
            onSuccess: function(response) {
                $('#won-auctions-container').css('opacity', '1');
                if (response.success && response.html) {
                    $('#won-auctions-container').html(response.html);
                    window.history.pushState(null, null, url);
                    
                    // Sync active tab visually with current URL param
                    syncWonFormInputs(url);
                } else {
                    toastr.error('Failed to load won auctions.');
                }
            },
            onError: function() {
                $('#won-auctions-container').css('opacity', '1');
                toastr.error('Failed to load won auctions.');
            }
        });
    }

    function syncWonFormInputs(url) {
        try {
            const urlObj = new URL(url, window.location.origin);
            const statusParam = urlObj.searchParams.get('payment_status') || '';

            // Update tab selection highlight
            $('.won-filter-tab').removeClass('active');
            $(`.won-filter-tab[data-status="${statusParam}"]`).addClass('active');
        } catch(err) {
            console.error('syncWonFormInputs failed', err);
        }
    }

    // Handle back/forward navigation state restore
    window.addEventListener('popstate', function() {
        const currentUrl = window.location.href;
        loadWonAuctions(currentUrl);
    });

    // Complete Purchase Button Trigger
    const cpModalEl = document.getElementById('completePurchaseModal');
    const cpModal = bootstrap.Modal.getOrCreateInstance(cpModalEl);

    function cpSetRow(rowSel, valSel, value) {
        if (value) {
            $(valSel).text(value);
            $(rowSel).show();
        } else {
            $(rowSel).hide();
        }
    }

    $(document).on('click', '.complete-purchase-btn', function() {
        const d = $(this).data();

        $('#modal-vehicle-title').text(d.title || '');
        $('#cp-vehicle-meta').text(d.meta || '');
        $('#cp-vehicle-img').attr({ src: d.image || '', alt: d.title || '' });
        $('#cp-ref-text').text(d.ref || '');
        $('#cp-ref').attr('data-copy', d.ref || '');

        $('#cp-bid').text(d.bid || d.amount || '');
        cpSetRow('#cp-row-commission', '#cp-commission', d.commission);
        cpSetRow('#cp-row-vat', '#cp-vat', d.vat);
        cpSetRow('#cp-row-deposit', '#cp-deposit', d.deposit);

        $('#modal-total-amount').text(d.amount || '');
        $('#cp-copy-amount').attr('data-copy', d.amountRaw || '');

        const baseUrl = "{{ route('bidder.wallet.index') }}";
        const amountRaw = d.amountRaw || '';
        const ref = d.ref || '';
        $('#cp-go-wallet').attr('href', baseUrl + '?auto_deposit=1&amount=' + encodeURIComponent(amountRaw) + '&ref=' + encodeURIComponent(ref));

        const checkoutRouteTemplate = "{{ route('bidder.payments.checkout', ['order' => 'ORDER_ID_PLACEHOLDER']) }}";
        
        if (d.orderId) {
            $('#cp-pay-online').attr('href', checkoutRouteTemplate.replace('ORDER_ID_PLACEHOLDER', d.orderId)).show();
        } else if (d.id) {
            // Fallback for mock data testing
            $('#cp-pay-online').attr('href', checkoutRouteTemplate.replace('ORDER_ID_PLACEHOLDER', 'mock_' + d.id)).show();
        } else {
            // Hide the button if there is no actual order yet (fallback case)
            $('#cp-pay-online').hide();
        }

        cpModal.show();
    });

    // Generic copy handler (IBAN, amount, reference)
    $(document).on('click', '#completePurchaseModal [data-copy]', function() {
        const btn = $(this);
        const text = String(btn.attr('data-copy') || '').trim();
        if (!text) return;

        const onDone = function() {
            const lbl = btn.find('.lbl');
            const original = lbl.length ? lbl.text() : null;
            btn.addClass('copied');
            if (lbl.length) lbl.text("{{ app()->getLocale() === 'ar' ? 'تم النسخ' : 'Copied' }}");
            toastr.success("{{ app()->getLocale() === 'ar' ? 'تم النسخ إلى الحافظة' : 'Copied to clipboard' }}");
            clearTimeout(btn.data('cpTimer'));
            btn.data('cpTimer', setTimeout(function() {
                btn.removeClass('copied');
                if (lbl.length) lbl.text(original);
            }, 1800));
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(onDone).catch(function() { cpFallbackCopy(text) ? onDone() : toastr.error('Could not copy text.'); });
        } else {
            cpFallbackCopy(text) ? onDone() : toastr.error('Could not copy text.');
        }
    });

    // Fallback for non-HTTPS environments (e.g. local XAMPP over http)
    function cpFallbackCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        cpModalEl.appendChild(ta);
        ta.select();
        let ok = false;
        try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
        ta.remove();
        return ok;
    }
});
</script>
@endsection
