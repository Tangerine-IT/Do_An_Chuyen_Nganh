import {
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

document.addEventListener('DOMContentLoaded', () => {
    const weightChart = document.getElementById('pet-weight-chart');
    if (weightChart) {
        new Chart(weightChart, {
            type: 'line',
            data: {
                labels: JSON.parse(weightChart.dataset.labels),
                datasets: [{
                    label: 'Cân nặng (kg)',
                    data: JSON.parse(weightChart.dataset.values),
                    borderColor: '#166534',
                    backgroundColor: 'rgba(22, 101, 52, .12)',
                    fill: true,
                    tension: .25,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: false } },
            },
        });
    }

    const dashboard = document.querySelector('[data-admin-dashboard]');
    if (! dashboard) {
        return;
    }

    const revenue = JSON.parse(dashboard.dataset.revenue);
    new Chart(document.getElementById('revenue-chart'), {
        type: 'line',
        data: {
            labels: revenue.labels,
            datasets: [{
                label: 'Doanh thu',
                data: revenue.values,
                borderColor: '#1F5035',
                backgroundColor: 'rgba(31, 80, 53, .12)',
                fill: true,
                tension: .35,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });

    const statuses = JSON.parse(dashboard.dataset.statuses);
    new Chart(document.getElementById('status-chart'), {
        type: 'doughnut',
        data: {
            labels: statuses.labels,
            datasets: [{
                data: statuses.values,
                backgroundColor: ['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444'],
            }],
        },
        options: { responsive: true, maintainAspectRatio: false },
    });

    const products = JSON.parse(dashboard.dataset.products);
    new Chart(document.getElementById('products-chart'), {
        type: 'bar',
        data: {
            labels: products.map((product) => product.name),
            datasets: [{
                label: 'Đã bán',
                data: products.map((product) => product.quantity),
                backgroundColor: '#F97316',
                borderRadius: 8,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } },
        },
    });
});
