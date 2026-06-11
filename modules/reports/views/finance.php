<?php /** FabX ERP - Finance Reports */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-currency-rupee text-success"></i> Finance Reports</h1>
    <div class="page-actions">
        <button onclick="exportTableToCSV('financeTable','finance_report.csv')" class="btn btn-outline-secondary">
            <i class="bi bi-download"></i> Export CSV
        </button>
    </div>
</div>

<?php
$totalInvoiced = array_sum(array_column($monthly_revenue ?? [], 'invoiced'));
$totalCollected = array_sum(array_column($monthly_revenue ?? [], 'collected'));
$totalOutstanding = $totalInvoiced - $totalCollected;
$collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 1) : 0;
?>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="fs-4 fw-bold text-primary"><?= format_currency($totalInvoiced) ?></div>
            <div class="text-muted small">Total Invoiced (12M)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="fs-4 fw-bold text-success"><?= format_currency($totalCollected) ?></div>
            <div class="text-muted small">Total Collected</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="fs-4 fw-bold text-danger"><?= format_currency($totalOutstanding) ?></div>
            <div class="text-muted small">Outstanding Receivable</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="fx-card text-center p-3">
            <div class="fs-4 fw-bold <?= $collectionRate >= 80 ? 'text-success' : ($collectionRate >= 50 ? 'text-warning' : 'text-danger') ?>">
                <?= $collectionRate ?>%
            </div>
            <div class="text-muted small">Collection Rate</div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="fx-card mb-4">
    <div class="fx-card-header py-3"><h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Monthly Revenue vs Collections (Last 12 Months)</h5></div>
    <div class="fx-card-body p-4">
        <canvas id="financeChart" height="110"></canvas>
    </div>
</div>

<!-- Detail Table -->
<div class="fx-card">
    <div class="fx-card-header py-3"><h5 class="mb-0"><i class="bi bi-table"></i> Month-by-Month Breakdown</h5></div>
    <div class="fx-card-body p-0">
        <table class="fx-table mb-0" id="financeTable">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-end">Invoiced (₹)</th>
                    <th class="text-end">Collected (₹)</th>
                    <th class="text-end">Outstanding (₹)</th>
                    <th class="text-center">Collection Rate</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($monthly_revenue)): ?>
                    <?php foreach (array_reverse($monthly_revenue) as $mr):
                        $invoiced = (float)($mr['invoiced'] ?? 0);
                        $collected = (float)($mr['collected'] ?? 0);
                        $outstanding = $invoiced - $collected;
                        $rate = $invoiced > 0 ? round(($collected / $invoiced) * 100) : 0;
                    ?>
                        <tr>
                            <td class="fw-bold"><?= e(date('F Y', strtotime($mr['month'] . '-01'))) ?></td>
                            <td class="text-end"><?= format_currency($invoiced) ?></td>
                            <td class="text-end text-success fw-bold"><?= format_currency($collected) ?></td>
                            <td class="text-end text-danger"><?= format_currency($outstanding) ?></td>
                            <td class="text-center">
                                <span class="fw-bold <?= $rate >= 80 ? 'text-success' : ($rate >= 50 ? 'text-warning' : 'text-danger') ?>">
                                    <?= $rate ?>%
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height:6px; min-width:80px;">
                                    <div class="progress-bar <?= $rate >= 80 ? 'bg-success' : ($rate >= 50 ? 'bg-warning' : 'bg-danger') ?>" style="width:<?= $rate ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-currency-rupee"></i><h5>No financial data available</h5></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = <?= json_encode(array_values($monthly_revenue ?? [])) ?>;
    if (data.length && document.getElementById('financeChart')) {
        new Chart(document.getElementById('financeChart'), {
            type: 'bar',
            data: {
                labels: data.map(r => r.month),
                datasets: [
                    { label: 'Invoiced (₹)', data: data.map(r => parseFloat(r.invoiced)||0), backgroundColor: 'rgba(52,152,219,0.65)', borderColor: 'rgba(52,152,219,1)', borderWidth: 1, borderRadius: 4 },
                    { label: 'Collected (₹)', data: data.map(r => parseFloat(r.collected)||0), backgroundColor: 'rgba(39,174,96,0.65)', borderColor: 'rgba(39,174,96,1)', borderWidth: 1, borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { color: '#a0aec0' } },
                    tooltip: { callbacks: { label: ctx => '₹ ' + ctx.parsed.y.toLocaleString('en-IN', {minimumFractionDigits:2}) } }
                },
                scales: {
                    x: { ticks: { color: '#a0aec0' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y: { ticks: { color: '#a0aec0', callback: v => '₹' + (v/100000).toFixed(1)+'L' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                }
            }
        });
    }
});
</script>
