<?php
$title = 'Tableau de bord';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <?php if (isset($client) && $client): ?>
            <h2 class="text-light">Bonjour, <?= htmlspecialchars($client->prenom ?? 'Client') ?> 👋</h2>
        <?php else: ?>
            <h2 class="text-light">Bonjour, Client 👋</h2>
        <?php endif; ?>
        <hr class="border-info">
    </div>
</div>

<?php if (isset($comptes) && !empty($comptes)): ?>
    <div class="row g-4">
        <?php foreach ($comptes as $compte): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card bg-dark text-white border-info shadow">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-wallet me-2"></i><?= htmlspecialchars($compte->type_compte ?? 'Compte') ?></h5>
                    <p class="card-text"><strong>N° Compte :</strong> <?= htmlspecialchars($compte->numero_compte ?? 'N/A') ?></p>
                    <p class="card-text"><strong>Solde :</strong> <span class="text-success"><?= number_format($compte->solde ?? 0, 2) ?> <?= htmlspecialchars($compte->devise ?? 'USD') ?></span></p>
                    <p class="card-text"><small class="text-secondary">Statut : <?= htmlspecialchars($compte->statut ?? 'Actif') ?></small></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">Aucun compte trouvé. Contactez votre conseiller.</div>
<?php endif; ?>

<div class="row mt-5">
    <div class="col-12">
        <h4 class="text-light">📋 Dernières transactions</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
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
                            <td><?= htmlspecialchars($t->type_transaction ?? 'N/A') ?></td>
                            <td class="<?= ($t->montant ?? 0) < 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($t->montant ?? 0, 2) ?> <?= htmlspecialchars($t->devise ?? 'USD') ?>
                            </td>
                            <td><?= htmlspecialchars($t->description ?? '') ?></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($t->statut ?? 'Approuvée') ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-secondary">Aucune transaction</td></tr>
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