import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import Chart from 'chart.js/auto';
import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'boxicons/css/boxicons.min.css';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
// FullCalendar
import { Calendar } from '@fullcalendar/core';



window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.Chart = Chart;
window.$ = window.jQuery = $;
window.DataTable = DataTable;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;
window.notyf = new Notyf({
    duration: 4200,
    position: { x: 'center', y: 'bottom' },
    ripple: false,
    dismissible: true,
    types: [
        {
            type: 'success',
            background: '#12b76a',
            icon: false,
        },
        {
            type: 'error',
            background: '#f04438',
            icon: false,
        },
        {
            type: 'warning',
            background: '#f79009',
            icon: false,
        },
        {
            type: 'info',
            background: '#465fff',
            icon: false,
        },
    ],
});
window.toast = (type, message) => {
    const variant = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
    window.notyf.open({ type: variant, message });
};

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('app:toast', (event) => {
        if (event.detail?.message) {
            window.toast(event.detail.type, event.detail.message);
        }
    });

    const emptyDatatableState = `
        <div class="datatable-empty-state">
            <img class="datatable-empty-image" src="/images/empty/datatable-empty.png" alt="Tidak ada data">
            <span class="datatable-empty-title">Tidak ada data</span>
            <span class="datatable-empty-copy">Coba ubah kata kunci pencarian atau tambahkan data baru.</span>
        </div>
    `;

    document.querySelectorAll('[data-ajax-datatable]').forEach((table) => {
        const columns = JSON.parse(table.dataset.columns || '[]');
        const searchInput = document.querySelector(table.dataset.searchTarget);
        const filterInput = document.querySelector(table.dataset.filterTarget);
        const filterParam = table.dataset.filterParam || 'filter';
        let debounceTimer;

        const dataTable = new DataTable(table, {
            ajax: {
                url: table.dataset.ajaxUrl,
                data: (data) => {
                    if (filterInput && filterInput.value !== '') {
                        data[filterParam] = filterInput.value;
                    }
                },
            },
            columns,
            lengthChange: false,
            ordering: false,
            pageLength: Number(table.dataset.pageLength || 10),
            processing: true,
            searching: true,
            serverSide: true,
            dom: 'rt<"flex flex-col gap-3 border-t border-gray-100 px-5 py-4 text-sm text-gray-600 sm:flex-row sm:items-center sm:justify-between"ip>',
            language: {
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '',
                paginate: {
                    first: '<span class="sr-only">Pertama</span><svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M11.6667 5L6.66675 10L11.6667 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 5L10 10L15 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    last: '<span class="sr-only">Terakhir</span><svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M8.33325 5L13.3333 10L8.33325 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 5L10 10L5 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    next: '<span class="sr-only">Berikutnya</span><svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7.5 5L12.5 10L7.5 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    previous: '<span class="sr-only">Sebelumnya</span><svg width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 5L7.5 10L12.5 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                },
                emptyTable: emptyDatatableState,
                processing: 'Memuat data...',
                zeroRecords: emptyDatatableState,
            },
        });

        searchInput?.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => dataTable.search(searchInput.value).draw(), 350);
        });

        filterInput?.addEventListener('change', () => {
            dataTable.draw();
        });
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.js-edit-record');

        if (!button) return;

        const record = button.dataset.recordPayload
            ? JSON.parse(atob(button.dataset.recordPayload))
            : JSON.parse(button.dataset.record || '{}');
        const modalName = button.dataset.modal;
        const form = document.querySelector(`[data-edit-form="${modalName}"]`);

        if (!form) return;

        form.action = form.dataset.updateUrl.replace('__ID__', record.id);

        Object.entries(record).forEach(([name, value]) => {
            const field = form.querySelector(`[name="${name}"]`);

            if (!field) return;

            if (field.type === 'checkbox') {
                field.checked = Boolean(value);
            } else {
                field.value = value ?? '';
                if (field.hasAttribute('data-money-input')) {
                    formatMoneyInputValue(field);
                }
            }
        });

        window.dispatchEvent(new CustomEvent('open-modal', { detail: modalName }));
    });

    function formatMoneyInputValue(el) {
        if (!el) return;
        const raw = String(el.value).replace(/[^0-9]/g, '');
        el.value = raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw, 10)) : '';
    }

    document.addEventListener('input', (e) => {
        if (e.target.matches('[data-money-input]')) {
            e.target.setCustomValidity('');
            formatMoneyInputValue(e.target);
        }
    });

    document.addEventListener('submit', (e) => {
        const moneyInputs = e.target.querySelectorAll('[data-money-input]');
        let hasError = false;

        moneyInputs.forEach(input => {
            const raw = input.value.replace(/[^0-9]/g, '');
            if (input.hasAttribute('required') && (!raw || parseInt(raw, 10) <= 0)) {
                input.setCustomValidity('Harga harus diisi dan lebih besar dari Rp 0.');
                input.reportValidity();
                hasError = true;
            } else {
                input.setCustomValidity('');
                input.value = raw;
            }
        });

        if (hasError) {
            e.preventDefault();
        }
    });

    document.querySelectorAll('[data-money-input]').forEach(formatMoneyInputValue);

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
