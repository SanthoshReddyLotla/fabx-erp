<?php /** FabX ERP - Sales Reports View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-graph-up-arrow"></i> Sales Reports</h1>
</div>
<div class="fx-card mb-4">
    <div class="fx-card-header"><strong>Monthly Quotation Value (Last 12 Months)</strong></div>
    <div class="fx-card-body"><canvas id="salesChart" height="120"></canvas></div>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <table class="fx-table mb-0">
            <thead><tr><th>Month</th><th>Quotations</th><th>Total Value</th></tr></thead>
            <tbody>
                <?php if (!empty($monthly_sales)): ?>
                    <?php foreach (array_reverse($monthly_sales) as $ms): ?>
                        <tr>
                            <td><?= e($ms['month']) ?></td>
                            <td><?= e($ms['quotations']) ?></td>
                            <td><?= format_currency($ms['value'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3"><div class="empty-state"><i class="bi bi-graph-up-arrow"></i><h5>No sales data available</h5></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = <?= json_encode($monthly_sales ?? []) ?>;
    if (data.length) {
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: data.map(r => r.month),
                datasets: [{
                    label: 'Quotation Value (₹)',
                    data: data.map(r => parseFloat(r.value) || 0),
                    borderColor: '#e67e22', backgroundColor: 'rgba(230,126,34,0.1)', fill: true, tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }
});
</script>
