<?php
$title = 'Mes cartes';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-credit-card me-2"></i>Mes cartes</h2>
<hr class="border-info">

<?php if (isset($notifications) && !empty($notifications)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-bell me-2"></i>Notifications</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (array_slice($notifications, 0, 3) as $n): ?>
                <li class="<?= $n->lu ? 'text-secondary' : 'text-light' ?>">
                    <?= htmlspecialchars($n->message) ?>
                    <small class="text-muted">(<?= date('d/m/Y H:i', strtotime($n->date_creation)) ?>)</small>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="index.php?route=client/notifications" class="btn btn-info btn-sm mt-2">Voir toutes</a>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body">
                <h5 class="card-title text-warning"><i class="fas fa-plus-circle me-2"></i>Demander une carte</h5>
                <p class="text-secondary">Solde actuel : <strong class="text-success"><?= number_format($solde ?? 0, 2) ?> USD</strong></p>
                <?php if (isset($solde) && $solde >= 5): ?>
                    <form method="POST" action="index.php?route=client/demander-carte">
                        <input type="hidden" name="compte_id" value="<?= $comptes[0]->id ?? 0 ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="type_carte" class="form-select bg-secondary text-white">
                                    <option value="Débit">Débit</option>
                                    <option value="Crédit">Crédit</option>
                                    <option value="Prépaié">Prépaié</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success w-100 fw-bold">
                                    <i class="fas fa-paper-plane me-2"></i>Demander
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">Solde minimum de 5 USD requis pour demander une carte.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<h4 class="text-light">Mes cartes</h4>
<div class="row g-4">
    <?php if (empty($cartes)): ?>
        <div class="col-12">
            <div class="alert alert-info">Vous n'avez pas encore de carte.</div>
        </div>
    <?php else: ?>
        <?php foreach ($cartes as $carte): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card bg-dark text-white border-<?= $carte->statut == 'Actif' ? 'success' : ($carte->statut == 'Bloqué' ? 'danger' : 'warning') ?> shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">
                                <i class="fas fa-credit-card me-2"></i><?= $carte->type_carte ?>
                            </h5>
                            <span class="badge bg-<?= $carte->statut == 'Actif' ? 'success' : ($carte->statut == 'Bloqué' ? 'danger' : 'warning') ?>">
                                <?= $carte->statut ?>
                            </span>
                        </div>
                        <p class="card-text">
                            <strong>N° :</strong> <?= substr($carte->numero_carte, 0, 4) . ' **** **** ' . substr($carte->numero_carte, -4) ?><br>
                            <strong>Expire :</strong> <?= date('m/y', strtotime($carte->date_expiration)) ?><br>
                            <strong>Plafond :</strong> <?= number_format($carte->plafond, 2) ?> USD
                        </p>
                        <div class="btn-group w-100" role="group">
                            <?php if ($carte->statut == 'Actif'): ?>
                                <form method="POST" action="index.php?route=client/bloquer-carte" class="w-50">
                                    <input type="hidden" name="carte_id" value="<?= $carte->id ?>">
                                    <button type="submit" class="btn btn-danger btn-sm w-100">Bloquer</button>
                                </form>
                            <?php elseif ($carte->statut == 'Bloqué'): ?>
                                <form method="POST" action="index.php?route=client/debloquer-carte" class="w-50">
                                    <input type="hidden" name="carte_id" value="<?= $carte->id ?>">
                                    <button type="submit" class="btn btn-success btn-sm w-100">Débloquer</button>
                                </form>
                            <?php endif; ?>
                            <button type="button" class="btn btn-info btn-sm w-50" data-bs-toggle="modal" data-bs-target="#pinModal<?= $carte->id ?>">
                                <i class="fas fa-key"></i> PIN
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Changer PIN -->
                <div class="modal fade" id="pinModal<?= $carte->id ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content bg-dark text-white">
                            <div class="modal-header border-info">
                                <h5 class="modal-title">Changer le PIN</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="index.php?route=client/changer-pin">
                                <div class="modal-body">
                                    <input type="hidden" name="carte_id" value="<?= $carte->id ?>">
                                    <div class="mb-3">
                                        <label>Ancien PIN</label>
                                        <input type="password" name="ancien_pin" class="form-control bg-secondary text-white" required maxlength="4" pattern="[0-9]{4}">
                                    </div>
                                    <div class="mb-3">
                                        <label>Nouveau PIN</label>
                                        <input type="password" name="nouveau_pin" class="form-control bg-secondary text-white" required maxlength="4" pattern="[0-9]{4}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Modifier</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Demandes en attente -->
<?php if (!empty($demandes)): ?>
    <h4 class="text-light mt-4">Demandes en attente</h4>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $d): ?>
                    <tr>
                        <td><?= $d->type_carte ?></td>
                        <td><span class="badge bg-warning"><?= $d->statut ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($d->date_demande)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>