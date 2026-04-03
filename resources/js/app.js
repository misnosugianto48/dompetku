import './bootstrap';

import { formatNumber, getRaw } from './lib/numberFormat';

window.DompetkuNumberFormat = {
    formatNumber,
    getRaw,
};

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
