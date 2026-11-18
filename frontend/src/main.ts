import { createApplication } from '@angular/platform-browser';
import { createCustomElement } from '@angular/elements';
import { provideHttpClient, withFetch } from '@angular/common/http';
import { provideBrowserGlobalErrorListeners, provideZonelessChangeDetection } from '@angular/core';
import { ClinicAlert } from './app/elements/clinic-alert.component';
import { ClinicBanner } from './app/elements/clinic-banner.component';
import { ClinicConfirm, ClinicConfirmRequest } from './app/elements/clinic-confirm.component';
import { ClinicPatientSearch } from './app/elements/clinic-patient-search.component';
import { ClinicTable } from './app/elements/clinic-table.component';

declare global {
  interface Window {
    /**
     * Global promise-based confirm dialog. Registered BEFORE elements boot so
     * Blade/legacy scripts can call it the moment this module executes.
     *   const ok = await window.clinicConfirm({ title, message, danger });
     */
    clinicConfirm: (opts: Omit<ClinicConfirmRequest, 'resolve'>) => Promise<boolean>;
  }
}

window.clinicConfirm = (opts) =>
  new Promise<boolean>((resolve) => {
    document.dispatchEvent(
      new CustomEvent<ClinicConfirmRequest>('clinic-confirm', { detail: { ...opts, resolve } })
    );
  });

createApplication({
  providers: [
    provideZonelessChangeDetection(),
    provideBrowserGlobalErrorListeners(),
    provideHttpClient(withFetch()),
  ],
})
  .then((appRef) => {
    const injector = appRef.injector;
    const elements: [string, unknown][] = [
      ['clinic-alert', ClinicAlert],
      ['clinic-banner', ClinicBanner],
      ['clinic-confirm', ClinicConfirm],
      ['clinic-patient-search', ClinicPatientSearch],
      ['clinic-table', ClinicTable],
    ];
    for (const [tag, component] of elements) {
      if (!customElements.get(tag)) {
        customElements.define(tag, createCustomElement(component as never, { injector }));
      }
    }
  })
  .catch((err) => console.error('[clinic-ui] bootstrap failed', err));
