<?php /** FabX ERP - Finance Reports View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-currency-rupee"></i> Finance Reports</h1>
</div>
<div class="fx-card mb-4">
    <div class="fx-card-header"><strong>Monthly Revenue vs Collections (Last 12 Months)</strong></div>
    <div class="fx-card-body"><canvas id="financeChart" height="120"></canvas></div>
</div>
<div class="fx-card">
    <div class="fx-card-body p-0">
        <table class="fx-table mb-0">
            <thead><tr><th>Month</th><th>Invoiced</th><th>Collected</th><th>Outstanding</th></tr></thead>
            <tbody>
                <?php if (!empty($monthly_revenue)): ?>
                    <?php foreach (array_reverse($monthly_revenue) as $mr): ?>
                        <tr>
                            <td><?= e($mr['month']) ?></td>
                            <td><?= format_currency($mr['invoiced'] ?? 0) ?></td>
                            <td><?= format_currency($mr['collected'] ?? 0) ?></td>
                            <td class="text-danger"><?= format_currency(($mr['invoiced'] ?? 0) - ($mr['collected'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4"><div class="empty-state"><i class="bi bi-currency-rupee"></i><h5>No financial data available</h5></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = <?= json_encode($monthly_revenue ?? []) ?>;
    if (data.length) {
        new Chart(document.getElementById('financeChart'), {
            type: 'bar',
            data: {
                labels: data.map(r => r.month),
                datasets: [
                    { label: 'Invoiced', data: data.map(r => parseFloat(r.invoiced)||0), backgroundColor: 'rgba(52,152,219,0.7)' },
                    { label: 'Collected', data: data.map(r => parseFloat(r.collected)||0), backgroundColor: 'rgba(39,174,96,0.7)' }
                ]
            },
            options: { responsive: true }
        });
    }
});
</script>
