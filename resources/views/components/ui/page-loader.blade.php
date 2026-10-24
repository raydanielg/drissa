<div id="pageLoader" role="status" aria-label="{{ config('app.name', 'Uzazi Clinic') }} loading"
     style="position:fixed;inset:0;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#fff;transition:opacity .3s ease;">
    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Uzazi Clinic') }}"
         style="width:64px;height:64px;border-radius:14px;object-fit:cover;box-shadow:0 4px 16px rgba(15,23,42,.12);animation:pageLoaderPop .5s cubic-bezier(.16,1,.3,1) both;">
    <div style="margin-top:20px;height:3px;width:120px;border-radius:9999px;background:#f1f5f9;overflow:hidden;">
        <div style="height:100%;width:38%;border-radius:9999px;background:#1e293b;animation:pageLoaderBar 1.1s cubic-bezier(.45,0,.55,1) infinite;"></div>
    </div>
</div>
<style>
@keyframes pageLoaderBar { 0% { transform:translateX(-115%); } 100% { transform:translateX(310%); } }
@keyframes pageLoaderPop { from { opacity:0; transform:scale(.92); } to { opacity:1; transform:scale(1); } }
@media (prefers-reduced-motion: reduce) {
    #pageLoader { transition:none !important; }
    #pageLoader * { animation:none !important; }
}
</style>
<script>
(function () {
    var l = document.getElementById('pageLoader');
    if (!l) return;
    var done = false;
    function hide() {
        if (done) return;
        done = true;
        l.style.opacity = '0';
        l.style.pointerEvents = 'none';
        setTimeout(function () { if (l.parentNode) l.remove(); }, 350);
    }
    window.addEventListener('load', hide);
    setTimeout(hide, 5000);
})();
</script>
