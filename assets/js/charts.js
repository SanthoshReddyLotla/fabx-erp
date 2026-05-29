/**
 * FabX ERP - Chart Configurations
 * Chart.js initialization for dashboard and reports
 */

(function() {
    'use strict';

    const FabXCharts = {

        /**
         * Default chart options
         */
        defaults: {
            font: { family: "'Inter', sans-serif", size: 11 },
            colors: {
                primary: '#3498db',
                success: '#27ae60',
                warning: '#f39c12',
                danger: '#e74c3c',
                info: '#9b59b6',
                accent: '#e67e22',
                gray: '#95a5a6'
            },
            gridColor: 'rgba(0,0,0,0.05)',
            textColor: '#64748b'
        },

        /**
         * Safely create a chart — destroys any existing instance on the canvas first.
         * Prevents the "Canvas is already in use" error when the page is revisited
         * or the script executes more than once.
         */
        safeChart: function(elementId, config) {
            const canvas = document.getElementById(elementId);
            if (!canvas) return null;

            // Destroy existing Chart instance on this canvas if one exists
            const existing = Chart.getChart(canvas);
            if (existing) {
                existing.destroy();
            }

            return new Chart(canvas, config);
        },

        /**
         * Initialize all charts on page
         */
        init: function() {
            this.initRevenueChart();
            this.initProductionChart();
            this.initQualityChart();
            this.initProjectStatusChart();
            this.initNCRTrendChart();
            this.initSalesPipelineChart();
            this.initAttendanceChart();
        },

        /**
         * Revenue Chart - Line chart
         */
        initRevenueChart: function() {
            this.safeChart('revenueChart', {
                type: 'line',
                data: {
                    labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
                    datasets: [{
                        label: 'Revenue (₹ Lakhs)',
                        data: [45, 52, 48, 61, 55, 67, 72, 58, 63, 70, 75, 82],
                        borderColor: this.defaults.colors.primary,
                        backgroundColor: 'rgba(52,152,219,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: this.defaults.colors.primary,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Target (₹ Lakhs)',
                        data: [40, 45, 50, 55, 60, 65, 70, 65, 70, 75, 80, 85],
                        borderColor: this.defaults.colors.gray,
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, padding: 20 } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: this.defaults.textColor } },
                        y: { grid: { color: this.defaults.gridColor }, ticks: { color: this.defaults.textColor }, beginAtZero: true }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        },

        /**
         * Production Chart - Bar chart
         */
        initProductionChart: function() {
            this.safeChart('productionChart', {
                type: 'bar',
                data: {
                    labels: ['Structural', 'Piping', 'Tank', 'Pressure Vessel', 'Heat Exchanger', 'Ducting', 'Other'],
                    datasets: [{
                        label: 'Planned (MT)',
                        data: [120, 85, 60, 45, 30, 55, 25],
                        backgroundColor: 'rgba(52,152,219,0.7)',
                        borderRadius: 4
                    }, {
                        label: 'Achieved (MT)',
                        data: [115, 82, 58, 42, 28, 52, 22],
                        backgroundColor: 'rgba(39,174,96,0.7)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: this.defaults.textColor } },
                        y: { grid: { color: this.defaults.gridColor }, ticks: { color: this.defaults.textColor }, beginAtZero: true }
                    }
                }
            });
        },

        /**
         * Quality Metrics - Doughnut
         */
        initQualityChart: function() {
            this.safeChart('qualityChart', {
                type: 'doughnut',
                data: {
                    labels: ['Accepted', 'Rejected', 'Rework', 'Hold'],
                    datasets: [{
                        data: [87, 5, 5, 3],
                        backgroundColor: [
                            this.defaults.colors.success,
                            this.defaults.colors.danger,
                            this.defaults.colors.warning,
                            this.defaults.colors.info
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } }
                    }
                }
            });
        },

        /**
         * Project Status - Horizontal bar
         */
        initProjectStatusChart: function() {
            this.safeChart('projectStatusChart', {
                type: 'bar',
                data: {
                    labels: ['Planning', 'Design', 'Procurement', 'Production', 'Assembly', 'Painting', 'Dispatch', 'Installation'],
                    datasets: [{
                        label: 'Projects',
                        data: [3, 5, 8, 12, 6, 4, 2, 3],
                        backgroundColor: [
                            '#95a5a6', '#3498db', '#f39c12', '#27ae60',
                            '#9b59b6', '#1abc9c', '#e67e22', '#2c3e50'
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: this.defaults.gridColor }, ticks: { color: this.defaults.textColor }, beginAtZero: true },
                        y: { grid: { display: false }, ticks: { color: this.defaults.textColor } }
                    }
                }
            });
        },

        /**
         * NCR Trend - Line chart
         */
        initNCRTrendChart: function() {
            this.safeChart('ncrTrendChart', {
                type: 'line',
                data: {
                    labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
                    datasets: [{
                        label: 'NCRs Raised',
                        data: [8, 6, 9, 5, 7, 4, 6, 5, 3, 4, 5, 3],
                        borderColor: this.defaults.colors.danger,
                        backgroundColor: 'rgba(231,76,60,0.08)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3
                    }, {
                        label: 'CAPAs Closed',
                        data: [7, 5, 8, 6, 6, 5, 5, 4, 4, 3, 4, 3],
                        borderColor: this.defaults.colors.success,
                        backgroundColor: 'transparent',
                        tension: 0.3,
                        pointRadius: 3,
                        borderDash: [5, 5]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: this.defaults.textColor } },
                        y: { grid: { color: this.defaults.gridColor }, ticks: { color: this.defaults.textColor }, beginAtZero: true }
                    }
                }
            });
        },

        /**
         * Sales Pipeline - Bar chart
         */
        initSalesPipelineChart: function() {
            this.safeChart('salesPipelineChart', {
                type: 'bar',
                data: {
                    labels: ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'],
                    datasets: [{
                        label: 'Count',
                        data: [25, 15, 10, 8, 12, 5],
                        backgroundColor: [
                            '#95a5a6', '#3498db', '#f39c12', '#9b59b6',
                            '#27ae60', '#e74c3c'
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: this.defaults.textColor } },
                        y: { grid: { color: this.defaults.gridColor }, ticks: { color: this.defaults.textColor }, beginAtZero: true }
                    }
                }
            });
        },

        /**
         * Attendance Chart - Stacked bar
         * NOTE: Fixed duplicate 'x' scale key (was overwriting itself in old code).
         */
        initAttendanceChart: function() {
            this.safeChart('attendanceChart', {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    datasets: [{
                        label: 'Present',
                        data: [145, 142, 148, 140, 138, 95],
                        backgroundColor: 'rgba(39,174,96,0.7)',
                        borderRadius: 3
                    }, {
                        label: 'Absent',
                        data: [5, 8, 2, 10, 12, 0],
                        backgroundColor: 'rgba(231,76,60,0.7)',
                        borderRadius: 3
                    }, {
                        label: 'On Leave',
                        data: [8, 6, 5, 5, 4, 0],
                        backgroundColor: 'rgba(243,156,18,0.7)',
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: this.defaults.textColor }, stacked: true },
                        y: { grid: { color: this.defaults.gridColor }, ticks: { color: this.defaults.textColor }, beginAtZero: true, stacked: true }
                    }
                }
            });
        },

        /**
         * Create a generic chart (also safe — destroys existing instance first)
         */
        create: function(elementId, type, data, options = {}) {
            const defaultOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { font: this.defaults.font } }
                },
                scales: {
                    x: { ticks: { font: this.defaults.font, color: this.defaults.textColor }, grid: { color: this.defaults.gridColor } },
                    y: { ticks: { font: this.defaults.font, color: this.defaults.textColor }, grid: { color: this.defaults.gridColor } }
                }
            };

            return this.safeChart(elementId, {
                type: type,
                data: data,
                options: { ...defaultOptions, ...options }
            });
        }
    };

    // Auto-initialize charts
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FabXCharts.init());
    } else {
        FabXCharts.init();
    }

    window.FabXCharts = FabXCharts;

})();
