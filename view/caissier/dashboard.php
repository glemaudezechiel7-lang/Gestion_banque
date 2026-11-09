<?php
$title = 'Caisse - Opérations';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-cash-register me-2"></i>Opérations de caisse</h2>
<hr class="border-info">

<?php if (!empty($message)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Recherche -->
    <div class="col-md-4">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body">
                <h5 class="card-title text-info"><i class="fas fa-search me-2"></i>Rechercher un compte</h5>
                <form method="POST" action="index.php?route=caissier/dashboard">
                    <input type="hidden" name="search" value="1">
                    <div class="mb-3">
                        <label class="form-label">Numéro de compte</label>
                        <input type="text" name="numero_compte" class="form-control bg-secondary text-white" 
                               value="<?= htmlspecialchars($numeroCompte ?? '') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-info w-100 fw-bold">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Informations du compte -->
    <div class="col-md-8">
        <?php if (isset($compte) && $compte && isset($client) && $client): ?>
            <div class="card bg-dark text-white border-info shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Client :</strong> <?= htmlspecialchars($client->nom . ' ' . $client->prenom) ?></p>
                            <p><strong>E-mail :</strong> <?= htmlspecialchars($client->email) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>N° Compte :</strong> <?= htmlspecialchars($compte->numero_compte) ?></p>
                            <p><strong>Type :</strong> <?= htmlspecialchars($compte->type_compte) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Solde :</strong> <span class="text-success"><?= number_format($compte->solde ?? 0, 2) ?> <?= htmlspecialchars($compte->devise ?? 'USD') ?></span></p>
                            <p><strong>Statut :</strong> <?= htmlspecialchars($compte->statut ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire d'opération -->
            <div class="card bg-dark text-white border-info shadow mt-3">
                <div class="card-body">
                    <h5 class="card-title text-warning"><i class="fas fa-exchange-alt me-2"></i>Effectuer une opération</h5>
                    <form method="POST" action="index.php?route=caissier/dashboard">
                        <input type="hidden" name="operation" value="1">
                        <input type="hidden" name="numero_compte" value="<?= htmlspecialchars($compte->numero_compte) ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type d'opération</label>
                                <select name="type" class="form-select bg-secondary text-white">
                                    <option value="depot">Dépôt</option>
                                    <option value="retrait">Retrait</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant</label>
                                <input type="number" step="0.01" name="montant" class="form-control bg-secondary text-white" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (optionnel)</label>
                            <input type="text" name="description" class="form-control bg-secondary text-white" placeholder="Ex: Retrait ATM">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Votre code de sécurité</label>
                            <input type="password" name="code" class="form-control bg-secondary text-white" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">
                            <i class="fas fa-check me-2"></i>VALIDER L'OPÉRATION
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card bg-dark text-white border-info shadow">
                <div class="card-body text-center text-secondary">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p>Recherchez un compte pour effectuer une opération.</p>
                    <?php if (isset($message) && strpos($message, 'introuvable') !== false): ?>
                        <p class="text-danger"><?= htmlspecialchars($message) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>