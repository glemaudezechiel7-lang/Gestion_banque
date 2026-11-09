<?php
$title = 'Dashboard Comptable';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-chart-pie me-2"></i>Tableau de bord comptable</h2>
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
        <a href="index.php?route=comptable/transactions" class="btn btn-info btn-sm">Voir tout</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>