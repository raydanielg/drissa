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
ESC / backdrop click resolve `false`. All `data-confirm` forms already use it
(SweetAlert fallback when the bundle is absent).

### `<clinic-table>` — table enhancer
Wraps any **existing** server-rendered `<table>`; adds search, click-to-sort
column headers (numeric-aware) and client-side pagination — no backend changes:
```blade
<clinic-table page-size="10" placeholder="Search reports…">
  <div class="overflow-x-auto"><table>…</table></div>
</clinic-table>
```
Already applied to: manager reports list, admin announcements list.

### `<clinic-patient-search>` — global palette
Ctrl+K / Cmd+K anywhere, or the magnifier in the topbar, or
`document.dispatchEvent(new CustomEvent('clinic-search:open'))`.
Debounced (250 ms) search against `GET /patients/search`, arrow-key navigation,
Enter opens `/patients/{id}`. Works for every authenticated role.

### `<clinic-alert>`
```blade
<clinic-alert type="warning" title="Heads up" dismissible>Optional detail</clinic-alert>
```
Types: `info | success | warning | critical` (brand colors).

### `<clinic-notifications>` — topbar notification center
Replaces the static bell (rendered when `public/ng/main.js` exists, old markup
otherwise). Polls `GET /api/notifications/feed` every 60 s — the feed merges
per-user `user_notifications` rows (server read-state) with active
announcements (browser read-state via `localStorage`). Unread badge, mark-one /
mark-all read, item click follows `url`.

Push a notification from PHP:
```php
UserNotification::sendTo([
    'title' => 'Lab report ready',
    'type'  => 'success',
    'url'   => '/lab/results/12',
]);                  // all staff
UserNotification::sendTo($data, [$doctorId]); // specific users
```
Publishing an announcement fans out to all staff automatically.

Wired events (server-side pushes):
- `assignDoctor` / paid visit → doctor gets "patient in your queue" / "payment received"
- Lab results completed (both submit paths) → ordering doctor gets "Lab results ready"

### `<clinic-modal title="…" size="md" open>`
Slotted dialog — body via default slot, actions via `slot="footer"`.
Toggle: `el.open = true`, `el.close()`, `el.toggle()`; closes on ESC / backdrop /
✕ and emits `(closed)`. Sizes: `sm | md | lg | xl`.

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
- [x] Phase 4: banner + confirm + alert + **notification center** (`user_notifications` backend)
- [x] Phase 5 (part): `clinic-table` enhancer, `clinic-patient-search` palette
- [x] Progressive: clinic-table on reports/announcements/patients/invoices/users/appointments
- [ ] Phase 3/5: `<clinic-button>` loading variants, form field elements
- [ ] Phase 5: server-side `clinic-data-table` (JSON-driven) where datasets are large
- [ ] Phase 6–7: wrap remaining high-traffic tables, migrate modules progressively
- [ ] Phase 8: bundle splitting per element group if the library grows
