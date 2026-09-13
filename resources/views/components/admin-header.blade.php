@props(['title', 'breadcrumb' => '', 'icon' => null])

<div class="premium-header-ultra mb-4">
    <div class="orb-1"></div>
    <div class="orb-2"></div>
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative" style="z-index: 1;">
        <div class="d-flex align-items-center gap-4 mb-3 mb-md-0">
            <div class="ultra-icon-box">
                @if($icon)
                    {!! $icon !!}
                @else
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                @endif
            </div>
            <div>
                <h1 class="ultra-title">{{ $title }}</h1>
                @if($breadcrumb)
                    <div class="ultra-breadcrumb">
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                        <span class="divider">/</span>
                        <span class="active">{{ $breadcrumb }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        @if($slot->isNotEmpty())
            <div class="d-flex align-items-center gap-2">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
