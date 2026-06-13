@props([
    'from',
    'to',
    'type' => null
])

@php
    $locale = app()->getLocale();
    $fromLang = $locale === 'ar' ? 'ar' : 'en';
    $toLang = $locale === 'ar' ? 'en' : 'ar';
    $fromSelector = $locale === 'ar' ? $from : $to;
    $toSelector = $locale === 'ar' ? $to : $from;
    $btnLabel = $locale === 'ar' ? __('Translate to English') : __('Translate to Arabic');
@endphp

<button type="button" class="btn btn-sm btn-link p-0 text-primary translate-btn" 
    data-from="{{ $fromSelector }}" 
    data-to="{{ $toSelector }}" 
    data-from-lang="{{ $fromLang }}" 
    data-to-lang="{{ $toLang }}"
    @if($type) data-type="{{ $type }}" @endif
    style="text-decoration: none; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
    {{ $btnLabel }}
</button>
