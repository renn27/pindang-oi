import './bootstrap';
import Alpine from 'alpinejs';
import SweetAlertHelper from './helpers/sweetalert';
import { initPushNotifications } from './push-notifications';

// Attach to window object
window.Swal = SweetAlertHelper;

window.Alpine = Alpine;
window.loadFlatpickr = (() => {
    let loader = null;

    return async () => {
        if (window.flatpickr) {
            return window.flatpickr;
        }

        loader ??= Promise.all([
            import('flatpickr'),
            import('flatpickr/dist/flatpickr.min.css'),
        ]).then(([module]) => {
            window.flatpickr = module.default;
            return window.flatpickr;
        });

        return loader;
    };
})();

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const initPush = () => initPushNotifications();

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(initPush, { timeout: 2500 });
    } else {
        window.setTimeout(initPush, 800);
    }

    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports
    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartResumeKegiatan')) {
        import('./components/chart/chart-resume-kegiatan').then(module => module.initChartResumeKegiatan());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then(module => module.initChartThirteen());
    }

    // Calendar init
    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }
});
