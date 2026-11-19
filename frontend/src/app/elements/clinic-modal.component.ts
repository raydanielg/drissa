import { ChangeDetectionStrategy, Component, EventEmitter, HostListener, Input, Output, booleanAttribute, signal } from '@angular/core';

/**
 * <clinic-modal title="Edit patient" size="md" open>
 *   …body content…
 *   <div slot="footer"><button>Save</button></div>
 * </clinic-modal>
 *
 * Toggle from JS:  el.open = true / el.close() / el.toggle()
 * Emits (closed) when dismissed via ESC, backdrop or the ✕ button.
 */
@Component({
  selector: 'clinic-modal',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (open) {
      <div class="cm-overlay" (click)="dismiss()">
        <div class="cm-dialog cm--{{ size }}" role="dialog" aria-modal="true" [attr.aria-label]="title || 'Dialog'" (click)="$event.stopPropagation()">
          <div class="cm-head">
            <h3 class="cm-title">{{ title }}</h3>
            <button type="button" class="cm-x" (click)="dismiss()" aria-label="Close">&times;</button>
          </div>
          <div class="cm-body"><ng-content></ng-content></div>
          <div class="cm-foot"><ng-content select="[slot=footer]"></ng-content></div>
        </div>
      </div>
    }
  `,
  styleUrl: './clinic-modal.component.scss',
})
export class ClinicModal {
  @Input() title = '';
  @Input() size: 'sm' | 'md' | 'lg' | 'xl' | string = 'md';
  @Input({ transform: booleanAttribute })
  set open(value: boolean) { this.isOpen.set(value); }
  get open() { return this.isOpen(); }
  @Output() closed = new EventEmitter<void>();

  private readonly isOpen = signal(false);

  @HostListener('document:keydown.escape')
  onEscape(): void {
    if (this.isOpen()) this.dismiss();
  }

  show(): void { this.isOpen.set(true); }
  close(): void { this.dismiss(); }
  toggle(): void { this.isOpen.update((v) => !v); }

  dismiss(): void {
    if (!this.isOpen()) return;
    this.isOpen.set(false);
    this.closed.emit();
  }
}
