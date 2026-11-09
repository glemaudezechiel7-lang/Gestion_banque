<?php
$title = 'Transactions - Comptable';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-list me-2"></i>Toutes les transactions</h2>
<hr class="border-info">

<?php if (isset($transactions) && !empty($transactions)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Compte</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Devise</th>
                    <th>Description</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($t->date_transaction ?? 'now')) ?></td>
                        <td><?= htmlspecialchars(($t->client_nom ?? '') . ' ' . ($t->client_prenom ?? '')) ?></td>
                        <td><?= htmlspecialchars($t->numero_compte ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($t->type_transaction ?? 'N/A') ?></td>
                        <td class="<?= ($t->montant ?? 0) < 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($t->montant ?? 0, 2) ?>
                        </td>
                        <td><?= htmlspecialchars($t->devise ?? 'USD') ?></td>
                        <td><?= htmlspecialchars($t->description ?? '') ?></td>
                        <td><span class="badge bg-success"><?= htmlspecialchars($t->statut ?? 'Approuvée') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($totalPages) && isset($page) && $totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link bg-dark text-light" href="index.php?route=comptable/transactions&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-info">Aucune transaction trouvée.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>