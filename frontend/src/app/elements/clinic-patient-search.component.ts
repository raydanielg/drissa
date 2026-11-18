import {
  ChangeDetectionStrategy, Component, ElementRef, HostListener, Input,
  OnDestroy, ViewChild, inject, signal,
} from '@angular/core';
import { HttpClient } from '@angular/common/http';

interface PatientHit {
  id: number;
  text: string;
  phone?: string;
  gender?: string;
  age?: number | string;
}

/**
 * <clinic-patient-search> — global patient quick-search palette.
 *
 * Opens on Ctrl+K / Cmd+K or via `document.dispatchEvent(new CustomEvent('clinic-search:open'))`.
 * Debounced GET to `endpoint`, keyboard navigation, Enter opens the patient page.
 */
@Component({
  selector: 'clinic-patient-search',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (open()) {
      <div class="cps-overlay" (click)="close()">
        <div class="cps-panel" role="dialog" aria-modal="true" aria-label="Patient search" (click)="$event.stopPropagation()">
          <div class="cps-inputwrap">
            <span class="cps-icon" aria-hidden="true">&#9906;</span>
            <input #box class="cps-input" type="search" placeholder="Search patient — name, phone or MRN…"
                   [value]="query()" (input)="onInput($event)" (keydown)="onKeys($event)" aria-label="Search patients">
            <kbd class="cps-kbd">ESC</kbd>
          </div>
          @if (loading()) { <p class="cps-status">Searching…</p> }
          @else if (results().length) {
            <ul class="cps-list" role="listbox">
              @for (p of results(); track p.id; let i = $index) {
                <li role="option" [attr.aria-selected]="active() === i" class="cps-item" [class.cps-item--active]="active() === i"
                    (click)="go(p)" (mousemove)="active.set(i)">
                  <span class="cps-avatar">{{ initials(p.text) }}</span>
                  <span class="cps-main">
                    <span class="cps-name">{{ p.text }}</span>
                    <span class="cps-meta">{{ p.gender || '' }}{{ p.gender && p.age ? ' · ' : '' }}{{ p.age != null ? p.age + ' yrs' : '' }}</span>
                  </span>
                  <span class="cps-phone">{{ p.phone }}</span>
                </li>
              }
            </ul>
          }
          @else if (query().length >= 2) { <p class="cps-status">No patients found</p> }
          @else { <p class="cps-hint">Type at least 2 characters — Ctrl+K anywhere to open</p> }
        </div>
      </div>
    }
  `,
  styleUrl: './clinic-patient-search.component.scss',
})
export class ClinicPatientSearch implements OnDestroy {
  @Input() endpoint = '/patients/search';
  @Input({ alias: 'base-url' }) baseUrl = '/patients/';

  @ViewChild('box') private box?: ElementRef<HTMLInputElement>;

  private readonly http = inject(HttpClient);
  private timer?: number;

  readonly open = signal(false);
  readonly query = signal('');
  readonly results = signal<PatientHit[]>([]);
  readonly loading = signal(false);
  readonly active = signal(0);

  @HostListener('document:clinic-search:open')
  show(): void {
    this.open.set(true);
    setTimeout(() => this.box?.nativeElement.focus());
  }

  @HostListener('document:keydown', ['$event'])
  onGlobalKeys(event: KeyboardEvent): void {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault();
      this.show();
    } else if (event.key === 'Escape' && this.open()) {
      this.close();
    }
  }

  onInput(event: Event): void {
    const value = (event.target as HTMLInputElement).value;
    this.query.set(value);
    this.active.set(0);
    clearTimeout(this.timer);
    if (value.trim().length < 2) {
      this.results.set([]);
      return;
    }
    this.timer = window.setTimeout(() => this.search(value.trim()), 250);
  }

  onKeys(event: KeyboardEvent): void {
    const list = this.results();
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      this.active.set(Math.min(this.active() + 1, list.length - 1));
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      this.active.set(Math.max(this.active() - 1, 0));
    } else if (event.key === 'Enter' && list[this.active()]) {
      event.preventDefault();
      this.go(list[this.active()]);
    }
  }

  go(patient: PatientHit): void {
    this.close();
    window.location.href = this.baseUrl + patient.id;
  }

  close(): void {
    this.open.set(false);
    this.query.set('');
    this.results.set([]);
  }

  initials(text: string): string {
    return text.split(/\s+/).slice(0, 2).map((w) => w[0] ?? '').join('').toUpperCase() || 'P';
  }

  private search(q: string): void {
    this.loading.set(true);
    this.http.get<PatientHit[]>(this.endpoint, { params: { q } }).subscribe({
      next: (list) => { this.results.set(Array.isArray(list) ? list : []); this.loading.set(false); },
      error: () => { this.results.set([]); this.loading.set(false); },
    });
  }

  ngOnDestroy(): void {
    clearTimeout(this.timer);
  }
}
