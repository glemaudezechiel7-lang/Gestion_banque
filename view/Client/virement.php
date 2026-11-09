<?php $title = 'Virement'; ob_start(); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body">
                <h4 class="card-title text-warning"><i class="fas fa-exchange-alt me-2"></i>Effectuer un virement</h4>
                <form method="POST" action="index.php?route=client/virement">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <div class="mb-3">
                        <label class="form-label">Compte destinataire</label>
                        <input type="text" name="compte_dest" class="form-control bg-secondary text-white" placeholder="INN202406150001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <input type="number" step="0.01" name="montant" class="form-control bg-secondary text-white" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input type="text" name="motif" class="form-control bg-secondary text-white" placeholder="Remboursement, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code de sécurité</label>
                        <input type="password" name="code" class="form-control bg-secondary text-white" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold"><i class="fas fa-check me-2"></i>Valider le virement</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>