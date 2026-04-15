@props([
    /** Admin edit URL the pill navigates to. Either this or :page is required. */
    'href' => null,
    /** Page slug for page-content editor deep-link (e.g. "home" → admin.page-content.edit). */
    'page' => null,
    /** Short human label shown in tooltip and as screen-reader text. */
    'label' => 'Edit',
    /** Corner of the parent container the pill anchors to.
     *  One of: top-right (default) | top-left | bottom-right | bottom-left. */
    'position' => 'top-right',
])

{{-- Renders only for logged-in admins. Anonymous visitors receive zero
     bytes for this component — the server never sends the markup. --}}
@if ($isAdmin ?? false)
    @php
        $positionClass = match ($position) {
            'top-left' => 'admin-edit-pill--top-left',
            'bottom-right' => 'admin-edit-pill--bottom-right',
            'bottom-left' => 'admin-edit-pill--bottom-left',
            default => 'admin-edit-pill--top-right',
        };
        // Resolve href — explicit `href` wins; else build from `page` slug.
        $finalHref = $href ?? ($page ? route('admin.page-content.edit', $page) : '#');
    @endphp
    <a
        href="{{ $finalHref }}"
        class="admin-edit-pill {{ $positionClass }}"
        title="{{ $label }} — opens admin editor"
        aria-label="{{ $label }}"
        data-page-transition="false"
        onclick="event.stopPropagation();"
    >
        <svg class="admin-edit-pill__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        <span class="admin-edit-pill__sr-only">{{ $label }}</span>
    </a>
@endif
