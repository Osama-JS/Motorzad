{{-- ===== CREATE BACKUP MODAL (Premium Split Layout) ===== --}}
<style>
    #createBackupModal .modal-dialog { max-width: 920px; }
    #createBackupModal .modal-content {
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        background: var(--bg-card);
        box-shadow: 0 40px 100px -25px rgba(0,0,0,.45);
    }
    .bkm { display: grid; grid-template-columns: 300px 1fr; min-height: 520px; }
    @media (max-width: 860px) { .bkm { grid-template-columns: 1fr; } .bkm-aside { display: none !important; } }

    /* ── Aside ── */
    .bkm-aside {
        position: relative; overflow: hidden;
        padding: 1.75rem 1.5rem;
        color: #e2e8f0;
        background:
            radial-gradient(circle at 110% -10%, rgba(229,62,62,.55), transparent 50%),
            radial-gradient(circle at -20% 110%, rgba(59,130,246,.40), transparent 55%),
            linear-gradient(160deg, #0f172a 0%, #111827 60%, #1e1b4b 100%);
        display: flex; flex-direction: column;
    }
    .bkm-aside::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.07) 1px, transparent 1px);
        background-size: 16px 16px; pointer-events: none;
    }
    .bkm-aside > * { position: relative; }
    .bkm-logo {
        width: 54px; height: 54px; border-radius: 16px;
        background: linear-gradient(135deg, #ef4444, #991b1b);
        display: flex; align-items: center; justify-content: center; color: #fff;
        box-shadow: 0 12px 30px -8px rgba(239,68,68,.7), inset 0 1px 0 rgba(255,255,255,.25);
        margin-bottom: 1.1rem;
    }
    .bkm-aside h5 { color: #fff; font-weight: 800; font-size: 1.15rem; margin: 0 0 .35rem; }
    .bkm-aside .lead-txt { font-size: .78rem; color: #94a3b8; line-height: 1.6; margin: 0 0 1.5rem; }

    .bkm-steps { list-style: none; padding: 0; margin: 0 0 1.5rem; }
    .bkm-steps li { display: flex; gap: .75rem; position: relative; padding-bottom: 1.1rem; }
    .bkm-steps li:not(:last-child)::before {
        content: ''; position: absolute; top: 28px; bottom: 2px; inset-inline-start: 13px;
        width: 2px; background: rgba(255,255,255,.12);
    }
    .bkm-steps .n {
        width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800;
        background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18); color: #cbd5e1;
        transition: all .3s ease;
    }
    .bkm-steps li.active .n { background: #ef4444; border-color: #ef4444; color: #fff; box-shadow: 0 0 0 4px rgba(239,68,68,.25); }
    .bkm-steps li.done .n   { background: #10b981; border-color: #10b981; color: #fff; }
    .bkm-steps b { display: block; font-size: .8rem; color: #f1f5f9; }
    .bkm-steps small { font-size: .7rem; color: #94a3b8; }

    .bkm-meta { margin-top: auto; display: grid; gap: .6rem; }
    .bkm-meta div {
        display: flex; justify-content: space-between; align-items: center;
        padding: .6rem .8rem; border-radius: 10px;
        background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08);
        font-size: .74rem; color: #94a3b8;
    }
    .bkm-meta strong { color: #fff; font-size: .8rem; }

    /* ── Main ── */
    .bkm-main { display: flex; flex-direction: column; min-width: 0; }
    .bkm-head {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 1.4rem 1.6rem 1rem; border-bottom: 1px solid var(--border);
    }
    .bkm-head h6 { margin: 0; font-weight: 800; font-size: 1rem; color: var(--text); }
    .bkm-head p  { margin: .2rem 0 0; font-size: .76rem; color: var(--text-secondary); }
    .bkm-close {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        border: 1px solid var(--border); background: var(--bg-hover); color: var(--text-muted);
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s;
    }
    .bkm-close:hover:not(:disabled) { color: var(--danger); border-color: var(--danger); transform: rotate(90deg); }
    .bkm-close:disabled { opacity: .4; cursor: not-allowed; }

    .bkm-body { padding: 1.25rem 1.6rem; flex: 1; }

    /* Option rows (override grid cards) */
    #createBackupModal .bkm-options { display: grid; grid-template-columns: 1fr; gap: .7rem; }
    #createBackupModal .bkm-options .backup-option-card {
        display: flex; align-items: center; gap: 1rem;
        text-align: start; padding: .95rem 1.1rem; padding-inline-end: 3rem;
        border: 1.5px solid var(--border); border-radius: 14px;
        background: var(--bg-card); transform: none;
    }
    #createBackupModal .bkm-options .backup-option-card::after {
        inset: 0 auto 0 auto; inset-inline-start: 0; width: 4px; height: auto; border-radius: 0;
    }
    #createBackupModal .bkm-options .backup-option-card:hover { border-color: var(--text-muted); transform: translateX(-2px); }
    #createBackupModal .bkm-options .backup-option-card.type-db.selected    { border-color: var(--brand-blue); background: linear-gradient(90deg, rgba(59,130,246,.07), transparent 70%); }
    #createBackupModal .bkm-options .backup-option-card.type-files.selected { border-color: var(--brand-gold); background: linear-gradient(90deg, rgba(245,158,11,.07), transparent 70%); }
    #createBackupModal .bkm-options .backup-option-card.type-all.selected   { border-color: var(--success);    background: linear-gradient(90deg, rgba(16,185,129,.07), transparent 70%); }
    [dir="rtl"] #createBackupModal .bkm-options .backup-option-card.selected { background-position: right; }
    #createBackupModal .bkm-options .backup-type-icon { margin: 0; width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0; }
    #createBackupModal .bkm-options .check-circle { top: 50%; transform: translateY(-50%); inset-inline-end: 1rem; width: 22px; height: 22px; }
    #createBackupModal .bkm-options .check-circle svg { width: 11px; height: 11px; }
    .bkm-opt-txt { flex: 1; min-width: 0; }
    .bkm-opt-txt .t { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
    #createBackupModal .bkm-opt-txt h6 { margin: 0; font-size: .88rem; }
    #createBackupModal .bkm-opt-txt p  { margin: .2rem 0 0; font-size: .74rem; }
    #createBackupModal .bkm-opt-txt .bk-tag { margin-top: 0; }
    .bkm-includes { display: flex; gap: .4rem; margin-top: .45rem; flex-wrap: wrap; }
    .bkm-includes span {
        font-size: .64rem; font-weight: 700; padding: .12rem .45rem; border-radius: 5px;
        background: var(--bg-hover); color: var(--text-muted); border: 1px solid var(--border);
    }

    /* Notice & progress */
    #createBackupModal #backupNotice {
        margin-top: 1rem; border-radius: 12px !important;
        border: 1px solid rgba(245,158,11,.25) !important;
        background: rgba(245,158,11,.06) !important;
    }
    #createBackupModal #backupProgressContainer { margin-top: 1rem; border-radius: 14px !important; padding: 1.1rem !important; }
    .bkm-ring { display: flex; align-items: center; gap: 1rem; margin-bottom: .9rem; }
    .bkm-ring .spin {
        width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
        border: 3px solid rgba(59,130,246,.15); border-top-color: var(--brand-blue);
        animation: btn-spin 1s linear infinite;
    }
    .bkm-ring.done .spin { animation: none; border-color: var(--success); }
    .bkm-ring.fail .spin { animation: none; border-color: var(--danger); }

    /* Footer */
    .bkm-foot {
        display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;
        padding: 1rem 1.6rem; border-top: 1px solid var(--border); background: var(--bg-hover);
    }
    .bkm-summary { font-size: .76rem; color: var(--text-secondary); display: flex; align-items: center; gap: .45rem; }
    .bkm-summary strong { color: var(--text); }
    .bkm-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--brand-blue); box-shadow: 0 0 0 3px rgba(59,130,246,.2); }
    .bkm-foot .btn-outline-secondary { border-radius: 10px; padding: .55rem 1.2rem; font-weight: 600; }

    /* Base Option Card Styles (Moved from index) */
    .backup-option-card {
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }
    .backup-option-card input[type="radio"] { display: none; }
    
    .backup-option-card .check-circle {
        position: absolute;
        border-radius: 50%;
        border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s ease;
    }
    .backup-option-card .check-circle svg { color: #fff; opacity: 0; transition: all 0.2s; }
    .backup-option-card.selected .check-circle svg { opacity: 1; }
    .backup-option-card.type-db.selected .check-circle { background: var(--brand-blue); border-color: var(--brand-blue); }
    .backup-option-card.type-files.selected .check-circle { background: var(--brand-gold); border-color: var(--brand-gold); }
    .backup-option-card.type-all.selected .check-circle { background: var(--success); border-color: var(--success); }

    .backup-type-icon {
        display: flex; align-items: center; justify-content: center;
    }
    .backup-type-icon.db    { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .backup-type-icon.files { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .backup-type-icon.all   { background: rgba(16,185,129,0.15); color: #10b981; }
</style>

<div class="modal fade" id="createBackupModal" tabindex="-1" aria-labelledby="createBackupModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="backupForm" action="{{ route('admin.backups.store') }}" method="POST">
                @csrf
                <div class="bkm">
                    {{-- Aside --}}
                    <aside class="bkm-aside">
                        <div class="bkm-logo">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                        </div>
                        <h5 id="createBackupModalLabel">نسخة احتياطية جديدة</h5>
                        <p class="lead-txt">احمِ بيانات المنصة بنسخة مضغوطة وآمنة يمكنك تنزيلها أو استعادتها في أي وقت.</p>

                        <ul class="bkm-steps" id="bkmSteps">
                            <li class="active"><span class="n">1</span><div><b>اختيار النوع</b><small>حدد البيانات المراد أرشفتها</small></div></li>
                            <li><span class="n">2</span><div><b>المعالجة والضغط</b><small>تصدير وتجميع الملفات</small></div></li>
                            <li><span class="n">3</span><div><b>جاهزة للتنزيل</b><small>تظهر في سجل النسخ</small></div></li>
                        </ul>

                        <div class="bkm-meta">
                            <div><span>المساحة المتاحة</span><strong>{{ $stats['free_disk_space'] }}</strong></div>
                            <div><span>آخر نسخة</span><strong>{{ $stats['last_backup'] }}</strong></div>
                        </div>
                    </aside>

                    {{-- Main --}}
                    <div class="bkm-main">
                        <div class="bkm-head">
                            <div>
                                <h6>ما الذي تريد نسخه؟</h6>
                                <p>اختر نوع النسخة المناسب لاحتياجك</p>
                            </div>
                            <button type="button" class="bkm-close" id="closeBackupModalBtn" data-bs-dismiss="modal" aria-label="إغلاق">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>

                        <div class="bkm-body">
                            <div class="backup-type-grid bkm-options">
                                <div class="backup-option-card type-db selected" data-value="db" data-label="قاعدة البيانات فقط" role="button" tabindex="0">
                                    <input type="radio" name="option" value="db" id="opt_db" checked>
                                    <div class="backup-type-icon db"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg></div>
                                    <div class="bkm-opt-txt">
                                        <div class="t"><h6>قاعدة البيانات فقط</h6><span class="bk-tag">الأسرع</span></div>
                                        <p>تصدير جميع جداول MySQL في ملف SQL مضغوط.</p>
                                        <div class="bkm-includes"><span>الجداول</span><span>المستخدمين</span><span>المزادات</span></div>
                                    </div>
                                    <div class="check-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                                </div>

                                <div class="backup-option-card type-files" data-value="files" data-label="الملفات المرفوعة فقط" role="button" tabindex="0">
                                    <input type="radio" name="option" value="files" id="opt_files">
                                    <div class="backup-type-icon files"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
                                    <div class="bkm-opt-txt">
                                        <div class="t"><h6>الملفات المرفوعة فقط</h6></div>
                                        <p>أرشفة الصور والوسائط والمستندات المرفوعة.</p>
                                        <div class="bkm-includes"><span>storage</span><span>uploads</span><span>الصور</span></div>
                                    </div>
                                    <div class="check-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                                </div>

                                <div class="backup-option-card type-all" data-value="all" data-label="النسخة الكاملة" role="button" tabindex="0">
                                    <input type="radio" name="option" value="all" id="opt_all">
                                    <div class="backup-type-icon all"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></div>
                                    <div class="bkm-opt-txt">
                                        <div class="t"><h6>النسخة الكاملة</h6><span class="bk-tag recommended">موصى به</span></div>
                                        <p>قاعدة البيانات والملفات معاً في حزمة واحدة متكاملة.</p>
                                        <div class="bkm-includes"><span>قاعدة البيانات</span><span>الملفات</span><span>ZIP</span></div>
                                    </div>
                                    <div class="check-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                                </div>
                            </div>

                            <div id="backupNotice" class="d-flex align-items-start gap-2 p-3" style="font-size:.78rem;color:var(--text-secondary);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;color:var(--brand-gold);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                <span>قد تستغرق العملية من <strong style="color:var(--text);">ثوانٍ إلى عدة دقائق</strong> حسب حجم البيانات. لا تغلق الصفحة أثناء التشغيل.</span>
                            </div>

                            <div id="backupProgressContainer" style="display:none;background:var(--bg-hover);border:1px solid var(--border);">
                                <div class="bkm-ring" id="bkmRing">
                                    <div class="spin"></div>
                                    <div class="flex-grow-1" style="min-width:0;">
                                        <div class="d-flex justify-content-between align-items-center" style="font-weight:700;">
                                            <span id="backupProgressText" style="color:var(--text);font-size:.82rem;">جاري الاتصال وتحضير البيئة...</span>
                                            <span id="backupProgressPercent" style="color:var(--brand-blue);font-size:1.1rem;font-weight:800;">0%</span>
                                        </div>
                                        <small class="text-muted" style="font-size:.7rem;">يرجى إبقاء هذه النافذة مفتوحة</small>
                                    </div>
                                </div>
                                <div style="width:100%;height:8px;background:rgba(0,0,0,.07);border-radius:100px;overflow:hidden;">
                                    <div id="backupProgressBar" style="width:0%;height:100%;border-radius:100px;background:linear-gradient(90deg, var(--brand-blue), #60a5fa);transition:width .4s ease, background .4s ease;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="bkm-foot">
                            <div class="bkm-summary"><span class="bkm-dot" id="bkmDot"></span>النوع المحدد: <strong id="bkmSelected">قاعدة البيانات فقط</strong></div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="cancelBackupBtn" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" id="startBackupBtn" class="btn-ultra">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                                    <span>بدء النسخ الاحتياطي</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = { db: 'var(--brand-blue)', files: 'var(--brand-gold)', all: 'var(--success)' };
    const dot = document.getElementById('bkmDot');
    const label = document.getElementById('bkmSelected');
    document.querySelectorAll('#createBackupModal .backup-option-card').forEach(card => {
        card.addEventListener('click', () => {
            label.textContent = card.dataset.label;
            dot.style.background = colors[card.dataset.value];
        });
    });

    // Sync stepper + ring with the progress UI
    const steps = document.querySelectorAll('#bkmSteps li');
    const ring = document.getElementById('bkmRing');
    const setStep = (i, final) => steps.forEach((li, idx) => {
        li.classList.toggle('done', idx < i || (final && idx === i));
        li.classList.toggle('active', idx === i && !final);
    });
    const percentEl = document.getElementById('backupProgressPercent');
    const textEl = document.getElementById('backupProgressText');
    new MutationObserver(() => {
        const color = percentEl.style.color || '';
        const pct = parseInt(percentEl.textContent) || 0;
        ring.classList.toggle('done', color.includes('success'));
        ring.classList.toggle('fail', color.includes('danger'));
        if (color.includes('success')) setStep(2, true);
        else if (color.includes('danger')) setStep(1, false);
        else setStep(pct > 0 || document.getElementById('backupProgressContainer').style.display === 'block' ? 1 : 0, false);
    }).observe(percentEl, { childList: true, characterData: true, subtree: true, attributes: true });
    new MutationObserver(() => percentEl.dispatchEvent(new Event('x'))).observe(textEl, { childList: true });
});
</script>
