import { ChangeDetectionStrategy, Component, Input, signal } from '@angular/core';

/**
 * <clinic-alert type="success|info|warning|critical" title="Saved" dismissible>
 *   Optional rich message goes here.
 * </clinic-alert>
 */
@Component({
  selector: 'clinic-alert',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (!closed()) {
      <div class="ca ca--{{ type }}" role="alert">
        <span class="ca-icon" aria-hidden="true">{{ icon() }}</span>
        <div class="ca-body">
          @if (title) { <p class="ca-title">{{ title }}</p> }
          <div class="ca-text"><ng-content></ng-content></div>
        </div>
        @if (dismissible) {
          <button type="button" class="ca-close" (click)="closed.set(true)" aria-label="Dismiss">&times;</button>
        }
      </div>
    }
  `,
  styleUrl: './clinic-alert.component.scss',
})
export class ClinicAlert {
  @Input() type: 'info' | 'success' | 'warning' | 'critical' | string = 'info';
  @Input() title = '';
  @Input() dismissible = false;

  readonly closed = signal(false);

  icon(): string {
    return { info: 'i', success: '✓', warning: '!', critical: '✕', error: '✕' }[this.type] ?? 'i';
  }
}
