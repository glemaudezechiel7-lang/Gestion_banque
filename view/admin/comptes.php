<?php
$title = 'Gestion des comptes';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-wallet me-2"></i>Comptes</h2>
<hr class="border-info">

<?php if (isset($comptes) && !empty($comptes)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Solde</th>
                    <th>Devise</th>
                    <th>Statut</th>
                    <th>Ouverture</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comptes as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c->numero_compte ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars(($c->client_nom ?? '') . ' ' . ($c->client_prenom ?? '')) ?></td>
                    <td><?= htmlspecialchars($c->type_compte ?? 'N/A') ?></td>
                    <td><?= number_format($c->solde ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($c->devise ?? 'USD') ?></td>
                    <td><?= htmlspecialchars($c->statut ?? 'Actif') ?></td>
                    <td><?= htmlspecialchars($c->date_ouverture ?? 'N/A') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Aucun compte trouvé.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>