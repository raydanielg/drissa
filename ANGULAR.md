# Clinic UI — Angular Elements Integration

Angular-powered UI components embedded into the existing Laravel/Blade application
as standard custom elements (web components). Blade stays the app shell; PHP stays
the authority for auth, RBAC, and business logic.

## Architecture (hybrid — approach C)

```
frontend/                     Angular 22 workspace (zoneless, no router/Material)
  src/app/design/tokens.scss  Brand tokens (mirror of :root CSS vars)
  src/app/elements/           One component per custom element
    clinic-banner.*           <clinic-banner>  — announcement banners
    clinic-confirm.*          <clinic-confirm> — branded confirm dialog
  src/main.ts                 createApplication + customElements.define + window.clinicConfirm
public/ng/                    Build output (gitignored — deploy manually)
```

- **Bundle**: single ES-module `main.js` (~46 kB gzipped), loaded with
  `<script type="module">` in `layouts/dashboard.blade.php`.
- **Graceful degradation**: if `public/ng/main.js` is absent the tags stay inert
  and `window.clinicConfirm` falls back to native `confirm()`.
- **Shadow DOM**: element styles are self-contained; they cannot leak into Blade
  pages and page CSS cannot leak in.

## Elements

### `<clinic-banner src="/api/announcements/active">`
Fetches active announcements and renders them at the top of the content area.
- Types: `info` (blue), `success` (green), `warning` (amber), `critical` (red).
- Optional `action_label` + `action_url` → action button.
- Dismissals persist in `localStorage` per announcement id.
- Admin CRUD: sidebar → **Announcements** (admin only). Start/end scheduling,
  priority ordering, `dismissible`, `is_active`.

### `<clinic-confirm>` + `window.clinicConfirm()`
```js
const ok = await window.clinicConfirm({
  title: 'Delete report',
  message: 'This cannot be undone.',
  confirmText: 'Delete',
  danger: true,
});
if (ok) { /* proceed */ }
```
ESC / backdrop click resolve `false`.

## Build & deploy

```bash
cd frontend
npm install          # first time
npm run build        # outputs to ../public/ng/  (main.js, styles.css)
```

`public/ng/` is **gitignored** (no build artifacts in the repo). Deploy:

```bash
# on your machine
cd frontend && npm run build

# upload public/ng/ to the server once via cPanel File Manager / FTP
# PHP changes still deploy normally:
git pull && php artisan view:clear
```

If the cPanel account has "Setup Node.js App" (CloudLinux), the build can be run
server-side instead of uploading.

## PHP integration notes

- Session auth: elements call same-origin endpoints, cookies flow automatically.
- CSRF: for any future mutating calls, send `X-CSRF-TOKEN` read from
  `meta[name="csrf-token"]`.
- API: `GET /api/announcements/active` sits inside the `auth` middleware group
  (`routes/web.php`); authorization remains server-side.

## Adding a new element

1. `src/app/elements/clinic-foo.component.ts` (+ `.scss`, `@use '../design/tokens'`).
2. Register the tag in `src/main.ts` (`elements` array).
3. `npm run build` → upload `public/ng/`.
4. Use `<clinic-foo>` anywhere in Blade.

## Roadmap (phases per master prompt)

- [x] Phase 1–2: workspace, tokens, elements build pipeline
- [x] Phase 4 (part): banner + confirm dialog elements
- [ ] Phase 3–5: buttons/forms/data-table elements, migrate modules progressively
- [ ] Phase 8: bundle splitting per element group if the library grows
