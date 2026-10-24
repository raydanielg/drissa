@props([
    'label' => 'Hold to confirm',
    'confirmedLabel' => 'Confirmed',
    'duration' => 1600,
    'reset' => 1800,
    'form' => null,     // optional form selector; defaults to closest('form')
    'icon' => null,     // hugeicons name, e.g. 'hgi-cancel-circle'
    'danger' => true,
    'class' => '',
    'undo' => null,     // message for UndoNotice; delays form submit until countdown expires
    'undoDuration' => 5000,
])

<button type="button"
    data-hold-confirm
    data-duration="{{ $duration }}"
    data-reset="{{ $reset }}"
    @if($form) data-form="{{ $form }}" @endif
    @if($undo) data-undo="{{ $undo }}" data-undo-duration="{{ $undoDuration }}" @endif
    class="htc relative isolate inline-flex h-9 min-w-40 touch-none select-none items-center justify-center overflow-hidden rounded-md border px-3 text-xs font-semibold outline-none transition-[transform] duration-150 focus-visible:ring-2 focus-visible:ring-offset-1 disabled:cursor-not-allowed disabled:opacity-50 {{ $danger ? 'border-red-300 bg-red-50 text-red-700 focus-visible:ring-red-400' : 'border-gray-200 bg-gray-50 text-gray-700 focus-visible:ring-gray-400' }} {{ $class }}"
    {{ $attributes }}>
    <span class="htc-label relative z-10 flex items-center justify-center gap-1.5">
        @if($icon)<i class="hgi-stroke {{ $icon }} text-sm"></i>@endif
        {{ $slot->isEmpty() ? $label : $slot }}
    </span>
    <span aria-hidden="true" class="htc-fill absolute inset-0 z-0 flex items-center justify-center gap-1.5 px-3 text-xs font-semibold {{ $danger ? 'bg-red-600 text-white' : 'bg-slate-800 text-white' }}" style="clip-path: inset(0 100% 0 0);">
        @if($icon)<i class="hgi-stroke {{ $icon }} text-sm"></i>@endif
        {{ $slot->isEmpty() ? $label : $slot }}
    </span>
    <span class="htc-done absolute inset-0 z-10 hidden items-center justify-center gap-1.5 text-white {{ $danger ? 'bg-emerald-600' : 'bg-emerald-600' }}">
        <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
        {{ $confirmedLabel }}
    </span>
</button>

@once
@push('styles')
<style>
.htc { cursor: pointer; }
.htc:active[data-input="pointer"] { transform: scale(.98); }
.htc-fill { transition-property: clip-path; transition-timing-function: cubic-bezier(0.23,1,0.32,1); }
.htc[data-input="keyboard"] .htc-fill { transition-duration: 0ms !important; }
@media (prefers-reduced-motion: reduce) { .htc-fill { transition-duration: 0ms !important; } }
</style>
@endpush

@push('scripts')
<script>
(function () {
    if (window.__htcInit) return;
    window.__htcInit = true;
    var BOUNDARY = 8;

    function getFill(btn) { return btn.querySelector('.htc-fill'); }
    function getDone(btn) { return btn.querySelector('.htc-done'); }

    function startHold(btn, input) {
        if (btn.disabled || btn._htcState === 'confirmed' || btn._htcState === 'holding') return;
        if (btn._htcReset) { clearTimeout(btn._htcReset); btn._htcReset = null; }
        btn._htcState = 'holding';
        btn._htcInput = input;
        btn.dataset.input = input;
        var fill = getFill(btn);
        var duration = parseInt(btn.dataset.duration || '1600', 10);
        if (fill) {
            fill.style.transitionDuration = (input === 'keyboard' ? 0 : duration) + 'ms';
            fill.style.transitionTimingFunction = 'linear';
            // force reflow so the new clip-path animates
            void fill.offsetWidth;
            fill.style.clipPath = 'inset(0 0 0 0)';
        }
        btn._htcTimer = setTimeout(function () { completeHold(btn); }, duration);
    }

    function cancelHold(btn) {
        if (btn._htcState !== 'holding') return;
        btn._htcState = 'idle';
        clearTimeout(btn._htcTimer);
        btn._htcTimer = null;
        btn._htcPointer = null;
        var fill = getFill(btn);
        if (fill) {
            fill.style.transitionDuration = '180ms';
            fill.style.transitionTimingFunction = 'cubic-bezier(0.23,1,0.32,1)';
            fill.style.clipPath = 'inset(0 100% 0 0)';
        }
    }

    function completeHold(btn) {
        if (btn._htcState !== 'holding') return;
        btn._htcState = 'confirmed';
        clearTimeout(btn._htcTimer);
        btn._htcTimer = null;
        btn._htcPointer = null;
        var fill = getFill(btn);
        if (fill) fill.style.clipPath = 'inset(0 0 0 0)';
        var done = getDone(btn);
        if (done) { done.classList.remove('hidden'); done.classList.add('flex'); }
        btn.setAttribute('aria-busy', 'false');

        // trigger action: named form or closest form
        var form = null;
        if (btn.dataset.form) form = document.querySelector(btn.dataset.form);
        if (!form) form = btn.closest('form');
        if (form) {
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        }
        btn.dispatchEvent(new CustomEvent('hold-confirmed', { bubbles: true }));

        var reset = parseInt(btn.dataset.reset || '1800', 10);
        if (reset > 0) {
            btn._htcReset = setTimeout(function () {
                btn._htcState = 'idle';
                if (done) { done.classList.add('hidden'); done.classList.remove('flex'); }
                var f2 = getFill(btn);
                if (f2) {
                    f2.style.transitionDuration = '180ms';
                    f2.style.clipPath = 'inset(0 100% 0 0)';
                }
            }, reset);
        }
    }

    // Pointer handling
    document.addEventListener('pointerdown', function (e) {
        var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
        if (!btn || !e.isPrimary || e.button !== 0) return;
        btn._htcPointer = e.pointerId;
        try { btn.setPointerCapture(e.pointerId); } catch (_) {}
        startHold(btn, 'pointer');
    });
    document.addEventListener('pointermove', function (e) {
        var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
        if (!btn || btn._htcState !== 'holding' || btn._htcPointer !== e.pointerId) return;
        var r = btn.getBoundingClientRect();
        if (e.clientX < r.left - BOUNDARY || e.clientX > r.right + BOUNDARY ||
            e.clientY < r.top - BOUNDARY || e.clientY > r.bottom + BOUNDARY) {
            cancelHold(btn);
        }
    });
    ['pointerup', 'pointercancel'].forEach(function (ev) {
        document.addEventListener(ev, function (e) {
            var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
            if (!btn || btn._htcPointer !== e.pointerId) return;
            cancelHold(btn);
        });
    });
    document.addEventListener('lostpointercapture', function (e) {
        var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
        if (btn) cancelHold(btn);
    });

    // Keyboard handling (Enter / Space)
    document.addEventListener('keydown', function (e) {
        var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
        if (!btn || e.repeat || (e.key !== 'Enter' && e.key !== ' ')) return;
        e.preventDefault();
        startHold(btn, 'keyboard');
    });
    document.addEventListener('keyup', function (e) {
        var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
        if (!btn || (e.key !== 'Enter' && e.key !== ' ')) return;
        e.preventDefault();
        cancelHold(btn);
    });
    document.addEventListener('focusout', function (e) {
        var btn = e.target.closest && e.target.closest('[data-hold-confirm]');
        if (btn) cancelHold(btn);
    });
})();
</script>
@endpush
@endonce
