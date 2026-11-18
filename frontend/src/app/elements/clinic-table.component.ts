import {
  AfterViewInit, ChangeDetectionStrategy, Component, ElementRef,
  HostListener, Input, OnDestroy, computed, inject, signal,
} from '@angular/core';

/**
 * <clinic-table> — progressive enhancer for an EXISTING server-rendered <table>.
 *
 * Wrap any Blade table and it gains client-side search, column sorting and
 * pagination — zero backend changes needed:
 *
 *   <clinic-table page-size="10" placeholder="Search reports...">
 *     <div class="overflow-x-auto"><table class="...">…</table></div>
 *   </clinic-table>
 */
@Component({
  selector: 'clinic-table',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    <div class="ct-bar">
      <input class="ct-search" type="search" [attr.placeholder]="placeholder" (input)="onSearch($event)" aria-label="Search table">
      <span class="ct-count">{{ shown() }} / {{ total() }}</span>
    </div>
    <ng-content></ng-content>
    @if (shown() === 0 && total() > 0) {
      <p class="ct-empty">No matching rows</p>
    }
    @if (pages() > 1) {
      <div class="ct-pager">
        <button type="button" (click)="go(page() - 1)" [disabled]="page() <= 1">&lsaquo; Prev</button>
        <span>{{ page() }} / {{ pages() }}</span>
        <button type="button" (click)="go(page() + 1)" [disabled]="page() >= pages()">Next &rsaquo;</button>
      </div>
    }
  `,
  styleUrl: './clinic-table.component.scss',
})
export class ClinicTable implements AfterViewInit, OnDestroy {
  @Input({ alias: 'page-size' }) pageSize = 10;
  @Input() placeholder = 'Search…';

  private readonly host = inject(ElementRef<HTMLElement>);
  private table?: HTMLTableElement;
  private rows: HTMLTableRowElement[] = [];
  private colType: ('num' | 'str')[] = [];
  private sortIdx = -1;
  private sortDir: 'asc' | 'desc' = 'asc';
  private observer?: MutationObserver;
  private searchTimer?: number;

  readonly query = signal('');
  readonly page = signal(1);
  readonly total = signal(0);
  readonly shown = signal(0);
  readonly pages = computed(() => Math.max(1, Math.ceil(this.shown() / this.pageSize)));

  ngAfterViewInit(): void {
    this.table = this.host.nativeElement.querySelector('table') ?? undefined;
    if (!this.table) return;
    this.collect();
    this.makeSortable();
    // Re-collect if the server/framework swaps tbody contents.
    this.observer = new MutationObserver(() => this.collect());
    this.observer.observe(this.table, { childList: true, subtree: true });
  }

  ngOnDestroy(): void {
    this.observer?.disconnect();
    clearTimeout(this.searchTimer);
  }

  onSearch(event: Event): void {
    const value = (event.target as HTMLInputElement).value;
    clearTimeout(this.searchTimer);
    this.searchTimer = window.setTimeout(() => {
      this.query.set(value.trim().toLowerCase());
      this.page.set(1);
      this.apply();
    }, 150);
  }

  go(target: number): void {
    this.page.set(Math.min(Math.max(1, target), this.pages()));
    this.apply();
  }

  @HostListener('click', ['$event'])
  onHeaderClick(event: Event): void {
    const th = (event.target as HTMLElement).closest('th');
    if (!th || !this.table || !th.parentElement) return;
    const idx = Array.prototype.indexOf.call(th.parentElement.children, th);
    if (idx < 0) return;
    this.sortDir = this.sortIdx === idx && this.sortDir === 'asc' ? 'desc' : 'asc';
    this.sortIdx = idx;
    this.apply();
  }

  private collect(): void {
    const tbody = this.table?.tBodies[0];
    if (!tbody) return;
    this.rows = Array.from(tbody.rows);
    this.total.set(this.rows.length);
    this.inferTypes();
    this.apply();
  }

  private inferTypes(): void {
    const headCols = this.table?.tHead?.rows[0]?.cells.length ?? 0;
    this.colType = Array.from({ length: headCols }, (_, i) => {
      const sample = this.rows.slice(0, 5).map((r) => r.cells[i]?.textContent ?? '');
      const numeric = sample.filter((s) => s.trim()).every((s) => !isNaN(parseFloat(s.replace(/[^\d.-]/g, ''))));
      return numeric ? 'num' : 'str';
    });
  }

  private makeSortable(): void {
    const headCells = this.table?.tHead?.rows[0]?.cells;
    if (!headCells) return;
    Array.from(headCells).forEach((th) => {
      const el = th as HTMLElement;
      if (/action/i.test(el.textContent ?? '')) return;
      el.style.cursor = 'pointer';
      el.title = 'Click to sort';
    });
  }

  private apply(): void {
    const q = this.query();
    let working = q ? this.rows.filter((r) => (r.textContent ?? '').toLowerCase().includes(q)) : [...this.rows];

    if (this.sortIdx >= 0) {
      const idx = this.sortIdx;
      const numeric = this.colType[idx] === 'num';
      const cell = (r: HTMLTableRowElement) => r.cells[idx]?.textContent?.trim() ?? '';
      const key = numeric
        ? (r: HTMLTableRowElement) => parseFloat(cell(r).replace(/[^\d.-]/g, '')) || 0
        : (r: HTMLTableRowElement) => cell(r).toLowerCase();
      working.sort((a, b) => {
        const ka = key(a), kb = key(b);
        const cmp = ka < kb ? -1 : ka > kb ? 1 : 0;
        return this.sortDir === 'asc' ? cmp : -cmp;
      });
    }

    const pageCount = Math.max(1, Math.ceil(working.length / this.pageSize));
    const current = Math.min(this.page(), pageCount);
    const start = (current - 1) * this.pageSize;
    const visibleSet = new Set(working.slice(start, start + this.pageSize));

    const tbody = this.table?.tBodies[0];
    if (tbody) {
      for (const row of working) tbody.appendChild(row); // re-append in sorted order
      for (const row of this.rows) row.style.display = visibleSet.has(row) ? '' : 'none';
    }
    this.shown.set(working.length);
    this.page.set(current);
  }
}
