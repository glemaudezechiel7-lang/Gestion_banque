<?php
$title = 'Caisse - Historique';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-history me-2"></i>Historique des opérations</h2>
<hr class="border-info">

<div class="card bg-dark text-white border-info shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Compte</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Description</th>
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
                                <td><?= htmlspecialchars($t->description ?? '') ?></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($t->statut ?? 'Approuvée') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-secondary">Aucune opération enregistrée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>