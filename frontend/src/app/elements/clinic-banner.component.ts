import { ChangeDetectionStrategy, Component, Input, OnInit, computed, inject, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';

export interface ClinicAnnouncement {
  id: number;
  title: string;
  body?: string | null;
  type: 'info' | 'success' | 'warning' | 'critical' | string;
  action_label?: string | null;
  action_url?: string | null;
  dismissible: boolean;
}

const DISMISS_KEY = 'clinic-banner:dismissed';

@Component({
  selector: 'clinic-banner',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (visible().length) {
      <div class="cb-stack" role="region" aria-label="Announcements">
        @for (a of visible(); track a.id) {
          <div class="cb cb--{{ a.type }}" role="status">
            <span class="cb-icon" aria-hidden="true">{{ icon(a.type) }}</span>
            <div class="cb-body">
              <p class="cb-title">{{ a.title }}</p>
              @if (a.body) {
                <p class="cb-text">{{ a.body }}</p>
              }
            </div>
            @if (a.action_label && a.action_url) {
              <a class="cb-action" [href]="a.action_url">{{ a.action_label }}</a>
            }
            @if (a.dismissible) {
              <button type="button" class="cb-close" (click)="dismiss(a.id)" aria-label="Dismiss announcement">&times;</button>
            }
          </div>
        }
      </div>
    }
  `,
  styleUrl: './clinic-banner.component.scss',
})
export class ClinicBanner implements OnInit {
  /** Endpoint returning the active announcements JSON list. */
  @Input() src = '/api/announcements/active';

  private readonly http = inject(HttpClient);
  private readonly items = signal<ClinicAnnouncement[]>([]);
  private readonly dismissed = signal<Set<number>>(ClinicBanner.loadDismissed());

  readonly visible = computed(() => this.items().filter((a) => !this.dismissed().has(a.id)));

  ngOnInit(): void {
    this.http.get<ClinicAnnouncement[]>(this.src).subscribe({
      next: (list) => this.items.set(Array.isArray(list) ? list : []),
      error: () => this.items.set([]),
    });
  }

  icon(type: string): string {
    return { info: 'i', success: '✓', warning: '!', critical: '✕' }[type] ?? 'i';
  }

  dismiss(id: number): void {
    const next = new Set(this.dismissed());
    next.add(id);
    this.dismissed.set(next);
    try {
      localStorage.setItem(DISMISS_KEY, JSON.stringify([...next]));
    } catch { /* private mode */ }
  }

  private static loadDismissed(): Set<number> {
    try {
      return new Set<number>(JSON.parse(localStorage.getItem(DISMISS_KEY) ?? '[]'));
    } catch {
      return new Set<number>();
    }
  }
}
