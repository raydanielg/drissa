@php
$base = 'h-9 w-full min-w-0 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm text-gray-900 transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-red-300 aria-invalid:ring-red-100';
// pull layout classes (col-span, widths, grow) onto the wrapper so grids keep working
$layoutCls = '';
foreach (preg_split('/\s+/', (string) $attributes->get('class', '')) as $c) {
    if ($c !== '' && preg_match('/^(col-|row-|min-w|max-w|w-|flex-1\b|grow|shrink|self-|place-)/', $c)) { $layoutCls .= $c . ' '; }
}
$btnCls = trim($base . ' ' . $attributes->get('class', ''));
@endphp
<div class="relative {{ trim($layoutCls) }}" data-select-wrap>
    <select data-slot="select" {{ $attributes->merge(['class' => $base . ' sr-only']) }}>
        {{ $slot }}
    </select>
    <button type="button" data-select-trigger class="{{ $btnCls }} flex items-center justify-between gap-2" {{ $attributes->has('disabled') ? 'disabled' : '' }}>
        <span data-select-label class="flex-1 truncate text-left"></span>
        <i class="hgi-stroke hgi-arrow-down-01 shrink-0 text-sm opacity-60"></i>
    </button>
    <div data-select-menu class="absolute left-0 right-0 top-full z-50 mt-1 hidden max-h-60 overflow-auto rounded-md border border-gray-200 bg-white p-1 shadow-md"></div>
</div>

@once
<script>
(function () {
    function wrap(el) { return el.closest('[data-select-wrap]'); }
    function getSelect(w) { return w.querySelector('select'); }
    function getMenu(w) { return w.querySelector('[data-select-menu]'); }
    function getLabel(w) { return w.querySelector('[data-select-label]'); }

    function syncLabel(w) {
        var s = getSelect(w), l = getLabel(w);
        if (!s || !l) return;
        var o = (s.selectedOptions && s.selectedOptions[0]) || s.options[s.selectedIndex];
        l.textContent = o ? o.textContent : '';
        l.classList.toggle('text-gray-400', !o || o.value === '');
    }

    function buildMenu(w) {
        var s = getSelect(w), m = getMenu(w);
        m.innerHTML = '';
        Array.prototype.forEach.call(s.options, function (o) {
            var it = document.createElement('div');
            it.setAttribute('data-select-item', '');
            it.dataset.value = o.value;
            var active = o.value === s.value;
            it.className = 'flex cursor-pointer items-center justify-between gap-2 rounded px-3 py-1.5 text-xs ' +
                (active ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-600 hover:bg-gray-100');
            var sp = document.createElement('span');
            sp.className = 'truncate';
            sp.textContent = o.textContent;
            it.appendChild(sp);
            if (active) {
                var i = document.createElement('i');
                i.className = 'hgi-stroke hgi-tick-02 shrink-0 text-sm text-slate-700';
                it.appendChild(i);
            }
            m.appendChild(it);
        });
    }

    function closeAll(except) {
        document.querySelectorAll('[data-select-menu]:not(.hidden)').forEach(function (m) {
            if (m !== except) m.classList.add('hidden');
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-select-trigger]');
        if (btn && !btn.disabled) {
            var w = wrap(btn), m = getMenu(w), wasHidden = m.classList.contains('hidden');
            closeAll();
            buildMenu(w);
            if (wasHidden) m.classList.remove('hidden');
            return;
        }
        var item = e.target.closest('[data-select-item]');
        if (item) {
            var w2 = wrap(item), s2 = getSelect(w2);
            s2.value = item.dataset.value;
            syncLabel(w2);
            getMenu(w2).classList.add('hidden');
            s2.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }
        closeAll();
    });

    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeAll(); });
    document.addEventListener('change', function (e) {
        if (e.target.matches && e.target.matches('select')) {
            var w = wrap(e.target);
            if (w) syncLabel(w);
        }
    });

    function init(w) { if (!w.dataset.selInit) { w.dataset.selInit = '1'; syncLabel(w); } }
    function scan(root) {
        if (root.querySelectorAll) root.querySelectorAll('[data-select-wrap]').forEach(init);
    }
    document.addEventListener('DOMContentLoaded', function () {
        scan(document);
        new MutationObserver(function (ms) {
            ms.forEach(function (m) {
                m.addedNodes.forEach(function (n) {
                    if (n.nodeType !== 1) return;
                    if (n.matches && n.matches('[data-select-wrap]')) init(n);
                    scan(n);
                });
            });
        }).observe(document.body, { childList: true, subtree: true });
    });
})();
</script>
@endonce
