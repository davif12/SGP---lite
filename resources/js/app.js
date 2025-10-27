import './bootstrap';
import 'bootstrap';
import Chart from 'chart.js/auto';
import './kanban';

import Alpine from 'alpinejs';

// Make Chart.js available globally
window.Chart = Chart;
window.Alpine = Alpine;

Alpine.start();
