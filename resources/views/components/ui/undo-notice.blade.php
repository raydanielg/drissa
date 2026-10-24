{{-- Undo notice snackbar: window.UndoNotice.show({ message, duration, onExpire }) --}}
<div id="undoNoticeContainer" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-[9998] flex flex-col gap-2 items-center pointer-events-none" aria-live="polite"></div>

@once
@push('styles')
<style>
.un { pointer-events: auto; display: flex; min-height: 36px; min-width: 280px; max-width: 90vw; align-items: center; justify-content: space-between; gap: 12px; border-radius: 10px; background: #fff; border: 1px solid #e5e7eb; box-shadow: 0 8px 24px rgba(15,23,42,.12); padding: 4px 4px 4px 12px; animation: unIn .25s cubic-bezier(.23,1,.32,1) both; }
.un.out { animation: unOut .2s cubic-bezier(.55,0,.55,.2) both; }
.un-msg { display: flex; align-items: center; gap: 8px; min-width: 0; font-size: 12px; font-weight: 500; color: #1f2937; }
.un-msg p { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.un-btn { position: relative; isolation: isolate; display: flex; height: 28px; min-width: 80px; cursor: pointer; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; padding: 0 10px; font-size: 12px; font-weight: 600; color: #1f2937; outline: none; transition: transform .15s cubic-bezier(.23,1,.32,1); }
.un-btn:active { transform: scale(.97); }
.un-btn:focus-visible { box-shadow: 0 0 0 2px #94a3b8; }
.un-fill { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; gap: 6px; background: #0f172a; color: #fff; transition-property: clip-path; transition-timing-function: linear; }
@keyframes unIn { from { opacity: 0; transform: translateY(8px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes unOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(6px); } }
@media (prefers-reduced-motion: reduce) { .un, .un.out, .un-fill { animation: none !important; transition-duration: 0ms !important; } }
</style>
@endpush

@push('scripts')
<script>
(function () {
    if (window.UndoNotice) return;
    window.UndoNotice = {
        show: function (opts) {
            var container = document.getElementById('undoNoticeContainer');
            if (!container) { if (opts.onExpire) opts.onExpire(); return; }
            var duration = opts.duration || 5000;
            var message = opts.message || 'Done';
            var input = opts.input || 'pointer';

            var el = document.createElement('div');
            el.className = 'un';
            el.setAttribute('role', 'status');
            el.innerHTML =
                '<div class="un-msg"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600 shrink-0"></i><p></p></div>' +
                '<button type="button" class="un-btn">' +
                    '<span class="flex items-center gap-1.5"><i class="hgi-stroke hgi-undo text-sm"></i>Undo</span>' +
                    '<span class="un-fill" style="clip-path: inset(0 0 0 0);"><i class="hgi-stroke hgi-undo text-sm"></i>Undo</span>' +
                '</button>';
            el.querySelector('.un-msg p').textContent = message;
            container.appendChild(el);

            var fill = el.querySelector('.un-fill');
            var done = false;
            var timer = null;

            function remove() {
                el.classList.add('out');
                setTimeout(function () { el.remove(); }, 220);
            }
            function expire() {
                if (done) return;
                done = true;
                clearTimeout(timer);
                remove();
                if (opts.onExpire) opts.onExpire();
            }
            function undo(e) {
                if (done) return;
                done = true;
                clearTimeout(timer);
                remove();
                if (opts.onUndo) opts.onUndo(e && e.detail === 0 ? 'keyboard' : 'pointer');
            }

            // start countdown drain on next frame
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    fill.style.transitionDuration = duration + 'ms';
                    fill.style.clipPath = 'inset(0 100% 0 0)';
                });
            });
            timer = setTimeout(expire, duration);
            el.querySelector('.un-btn').addEventListener('click', undo);
        }
    };
})();
</script>
@endpush
@endonce
