import {
  ChangeDetectionStrategy, Component, ElementRef, HostListener, Input,
  OnDestroy, OnInit, computed, inject, signal,
} from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

interface FeedItem {
  id: string;
  kind: 'user' | 'announcement';
  title: string;
  body?: string | null;
  type: string;
  url?: string | null;
  read: boolean;
  at?: string | null;
}

const READ_KEY = 'clinic-notif:read-announcements';

/**
 * <clinic-notifications> — topbar bell with unread badge and dropdown feed.
 * Merges per-user notifications (server read-state) and active announcements
 * (browser read-state). Polls every `interval` seconds.
 */
@Component({
  selector: 'clinic-notifications',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    <div class="cn-wrap">
      <button type="button" class="cn-bell" (click)="toggle()" aria-label="Notifications" [attr.aria-expanded]="open()">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/>
        </svg>
        @if (unreadCount() > 0) {
          <span class="cn-badge">{{ unreadCount() > 9 ? '9+' : unreadCount() }}</span>
        }
      </button>
      @if (open()) {
        <div class="cn-drop" role="menu" aria-label="Notifications">
          <div class="cn-head">
            <span>Notifications</span>
            @if (unreadCount() > 0) {
              <button type="button" class="cn-readall" (click)="markAll()">Mark all read</button>
            }
          </div>
          <div class="cn-list">
            @for (n of items(); track n.id) {
              <button type="button" class="cn-item" [class.cn-item--unread]="!isRead(n)" (click)="openItem(n)">
                <span class="cn-dot cn-dot--{{ n.type }}"></span>
                <span class="cn-text">
                  <span class="cn-title">{{ n.title }}</span>
                  @if (n.body) { <span class="cn-body">{{ n.body }}</span> }
                  <span class="cn-time">{{ ago(n.at) }}</span>
                </span>
              </button>
            } @empty {
              <p class="cn-empty">No notifications</p>
            }
          </div>
        </div>
      }
    </div>
  `,
  styleUrl: './clinic-notifications.component.scss',
})
export class ClinicNotifications implements OnInit, OnDestroy {
  @Input() src = '/api/notifications/feed';
  @Input() interval = 60;

  private readonly http = inject(HttpClient);
  private readonly host = inject(ElementRef<HTMLElement>);
  private timer?: number;

  readonly open = signal(false);
  readonly items = signal<FeedItem[]>([]);
  readonly unread = signal(0);
  readonly readAnnouncements = signal<Set<string>>(ClinicNotifications.loadRead());

  readonly unreadCount = computed(() =>
    this.unread() + this.items().filter((i) => i.kind === 'announcement' && !this.readAnnouncements().has(i.id)).length
  );

  ngOnInit(): void {
    this.refresh();
    this.timer = window.setInterval(() => this.refresh(), this.interval * 1000);
  }

  ngOnDestroy(): void {
    clearInterval(this.timer);
  }

  @HostListener('document:click', ['$event'])
  onOutside(event: Event): void {
    if (this.open() && !this.host.nativeElement.contains(event.target as Node)) {
      this.open.set(false);
    }
  }

  toggle(): void {
    this.open.update((v) => !v);
    if (this.open()) this.refresh();
  }

  isRead(item: FeedItem): boolean {
    return item.kind === 'user' ? item.read : this.readAnnouncements().has(item.id);
  }

  openItem(item: FeedItem): void {
    if (item.kind === 'user' && !item.read) {
      const id = item.id.slice(2);
      this.http.post(`/api/notifications/${id}/read`, {}, { headers: this.csrf() }).subscribe(() => this.refresh());
    } else if (item.kind === 'announcement') {
      const next = new Set(this.readAnnouncements());
      next.add(item.id);
      this.readAnnouncements.set(next);
      try { localStorage.setItem(READ_KEY, JSON.stringify([...next])); } catch { /* private mode */ }
    }
    if (item.url) window.location.href = item.url;
  }

  markAll(): void {
    this.http.post('/api/notifications/read-all', {}, { headers: this.csrf() }).subscribe(() => this.refresh());
    const next = new Set(this.readAnnouncements());
    for (const i of this.items()) if (i.kind === 'announcement') next.add(i.id);
    this.readAnnouncements.set(next);
    try { localStorage.setItem(READ_KEY, JSON.stringify([...next])); } catch { /* private mode */ }
  }

  ago(iso?: string | null): string {
    if (!iso) return '';
    const mins = Math.max(0, Math.floor((Date.now() - new Date(iso).getTime()) / 60000));
    if (mins < 1) return 'just now';
    if (mins < 60) return mins + 'm ago';
    const hrs = Math.floor(mins / 60);
    if (hrs < 24) return hrs + 'h ago';
    return Math.floor(hrs / 24) + 'd ago';
  }

  private refresh(): void {
    this.http.get<{ items: FeedItem[]; unread: number }>(this.src).subscribe({
      next: (res) => { this.items.set(res.items ?? []); this.unread.set(res.unread ?? 0); },
      error: () => {},
    });
  }

  private csrf(): HttpHeaders {
    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
    return new HttpHeaders({ 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' });
  }

  private static loadRead(): Set<string> {
    try {
      return new Set<string>(JSON.parse(localStorage.getItem(READ_KEY) ?? '[]'));
    } catch {
      return new Set<string>();
    }
  }
}
