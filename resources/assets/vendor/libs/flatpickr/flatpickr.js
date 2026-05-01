import flatpickr from 'flatpickr/dist/flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

try {
  window.flatpickr = flatpickr;
  // Register Indonesian locale globally so `locale: "id"` never throws.
  if (Indonesian) {
    flatpickr.localize(Indonesian);
    flatpickr.l10ns.id = Indonesian;
  }
} catch (e) {}

export { flatpickr };
