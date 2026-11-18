import { ChangeDetectionStrategy, Component, HostListener, signal } from '@angular/core';

export interface ClinicConfirmRequest {
  title?: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  danger?: boolean;
  resolve: (value: boolean) => void;
}

/**
 * <clinic-confirm> — branded confirmation dialog element.
 *
 * Usage from any page script (Blade or vanilla JS):
 *   const ok = await window.clinicConfirm({ title: 'Delete report', message: 'This cannot be undone', danger: true });
 *
 * The global `window.clinicConfirm` bridge is registered in main.ts and resolves a Promise<boolean>.
 */
@Component({
  selector: 'clinic-confirm',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (req(); as r) {
      <div class="cc-overlay" (click)="answer(false)">
        <div class="cc-dialog" role="alertdialog" aria-modal="true" aria-labelledby="cc-title" (click)="$event.stopPropagation()">
          <div class="cc-icon" [class.cc-icon--danger]="r.danger">{{ r.danger ? '!' : '?' }}</div>
          <h3 class="cc-title" id="cc-title">{{ r.title || 'Please confirm' }}</h3>
          <p class="cc-msg">{{ r.message }}</p>
          <div class="cc-actions">
            <button type="button" class="cc-btn cc-cancel" (click)="answer(false)">{{ r.cancelText || 'Cancel' }}</button>
            <button type="button" class="cc-btn" [class.cc-danger]="r.danger" [class.cc-primary]="!r.danger" autofocus (click)="answer(true)">
              {{ r.confirmText || (r.danger ? 'Delete' : 'Confirm') }}
            </button>
          </div>
        </div>
      </div>
    }
  `,
  styleUrl: './clinic-confirm.component.scss',
})
export class ClinicConfirm {
  readonly req = signal<ClinicConfirmRequest | null>(null);

  @HostListener('document:clinic-confirm', ['$event'])
  onRequest(event: Event): void {
    this.req.set((event as CustomEvent<ClinicConfirmRequest>).detail);
  }

  @HostListener('document:keydown.escape')
  onEscape(): void {
    if (this.req()) this.answer(false);
  }

  answer(value: boolean): void {
    const current = this.req();
    this.req.set(null);
    current?.resolve(value);
  }
}
