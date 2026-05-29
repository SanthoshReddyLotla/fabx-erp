<?php /** FabX ERP - Quality Reports View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-shield-check"></i> Quality Reports</h1>
</div>
<div class="row g-4">
    <div class="col-md-8">
        <div class="fx-card">
            <div class="fx-card-header"><strong>NCR Trend (Last 12 Months)</strong></div>
            <div class="fx-card-body"><canvas id="ncrChart" height="250"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="fx-card">
            <div class="fx-card-header"><strong>CAPA Status</strong></div>
            <div class="fx-card-body"><canvas id="capaChart" height="250"></canvas></div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ncrData = <?= json_encode($ncr_trend ?? []) ?>;
    const capaData = <?= json_encode($capa_status ?? []) ?>;

    if (document.getElementById('ncrChart') && ncrData.length) {
        const months = [...new Set(ncrData.map(r => r.month))].sort();
        new Chart(document.getElementById('ncrChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'NCR Count',
                    data: months.map(m => ncrData.filter(r => r.month === m).reduce((a,r) => a + parseInt(r.count), 0)),
                    backgroundColor: 'rgba(231,76,60,0.6)'
                }]
            }
        });
    }

    if (document.getElementById('capaChart') && capaData.length) {
        new Chart(document.getElementById('capaChart'), {
            type: 'doughnut',
            data: {
                labels: capaData.map(r => r.status),
                datasets: [{ data: capaData.map(r => r.count), backgroundColor: ['#27ae60','#f39c12','#e74c3c','#3498db'] }]
            }
        });
    }
});
</script>
