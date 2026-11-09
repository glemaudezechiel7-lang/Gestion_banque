<?php
$title = 'Demandes de cartes';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-credit-card me-2"></i>Demandes de cartes</h2>
<hr class="border-info">

<?php if (isset($demandes) && !empty($demandes)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d->nom . ' ' . $d->prenom) ?></td>
                        <td><?= htmlspecialchars($d->email) ?></td>
                        <td><?= $d->type_carte ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($d->date_demande)) ?></td>
                        <td>
                            <form method="POST" action="index.php?route=admin/approuver-carte" class="d-inline">
                                <input type="hidden" name="demande_id" value="<?= $d->id ?>">
                                <button type="submit" class="btn btn-success btn-sm">Approuver</button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejetModal<?= $d->id ?>">Rejeter</button>

                            <!-- Modal Rejet -->
                            <div class="modal fade" id="rejetModal<?= $d->id ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-white">
                                        <div class="modal-header border-info">
                                            <h5 class="modal-title">Rejeter la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="index.php?route=admin/rejeter-carte">
                                            <div class="modal-body">
                                                <input type="hidden" name="demande_id" value="<?= $d->id ?>">
                                                <div class="mb-3">
                                                    <label>Motif du rejet</label>
                                                    <textarea name="commentaire" class="form-control bg-secondary text-white" rows="3" placeholder="Raison du rejet..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Aucune demande en attente.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>