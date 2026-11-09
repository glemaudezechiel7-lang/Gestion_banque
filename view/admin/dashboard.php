<?php
$title = 'Dashboard Admin';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-chart-line me-2"></i>Tableau de bord</h2>
<hr class="border-info">

<div class="row g-4">
    <div class="col-md-3">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-users text-info"></i></h5>
                <h3><?= isset($totalClients) ? $totalClients : 0 ?></h3>
                <p>Clients</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-wallet text-info"></i></h5>
                <h3><?= isset($totalComptes) ? $totalComptes : 0 ?></h3>
                <p>Comptes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-list text-info"></i></h5>
                <h3><?= isset($totalTransactions) ? $totalTransactions : 0 ?></h3>
                <p>Transactions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-coins text-info"></i></h5>
                <h3><?= isset($soldeTotal) ? number_format($soldeTotal, 2) : '0.00' ?> USD</h3>
                <p>Solde total</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body">
                <h5 class="text-info"><i class="fas fa-chart-area me-2"></i>Évolution des transactions (30 derniers jours)</h5>
                <canvas id="transactionChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h4 class="text-light">📋 Dernières transactions</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Compte</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($transactions) && !empty($transactions)): ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($t->date_transaction ?? 'now')) ?></td>
                                <td><?= htmlspecialchars(($t->client_nom ?? '') . ' ' . ($t->client_prenom ?? '')) ?></td>
                                <td><?= htmlspecialchars($t->numero_compte ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t->type_transaction ?? 'N/A') ?></td>
                                <td class="<?= ($t->montant ?? 0) < 0 ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($t->montant ?? 0, 2) ?> <?= htmlspecialchars($t->devise ?? 'USD') ?>
                                </td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($t->statut ?? 'Approuvée') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-secondary">Aucune transaction récente</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('transactionChart').getContext('2d');
    const dailyStats = <?= isset($dailyStats) ? json_encode($dailyStats) : '[]' ?>;
    const labels = dailyStats.map(item => item.date);
    const data = dailyStats.map(item => item.count);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre de transactions',
                data: data,
                backgroundColor: 'rgba(100, 200, 255, 0.2)',
                borderColor: '#64d8ff',
                borderWidth: 2,
                tension: 0.3,
                pointBackgroundColor: '#64d8ff',
                pointBorderColor: '#fff',
                pointBorderWidth: 1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { labels: { color: '#e0e0e0' } }
            },
            scales: {
                x: { ticks: { color: '#aaaaaa' }, grid: { color: '#333333' } },
                y: { ticks: { color: '#aaaaaa', stepSize: 1 }, grid: { color: '#333333' }, beginAtZero: true }
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>