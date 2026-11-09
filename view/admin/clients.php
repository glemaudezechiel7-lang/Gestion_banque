<?php
$title = 'Gestion des clients';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-users me-2"></i>Clients</h2>
<hr class="border-info">

<?php if (isset($clients) && !empty($clients)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Pays</th>
                    <th>Actif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c->id ?? '') ?></td>
                    <td><?= htmlspecialchars($c->nom ?? '') ?></td>
                    <td><?= htmlspecialchars($c->prenom ?? '') ?></td>
                    <td><?= htmlspecialchars($c->email ?? '') ?></td>
                    <td><?= htmlspecialchars($c->telephone ?? '') ?></td>
                    <td><?= htmlspecialchars($c->pays ?? '') ?></td>
                    <td><?= isset($c->actif) && $c->actif ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-danger">Inactif</span>' ?></td>
                    <td>
                        <a href="index.php?route=admin/client/details?id=<?= $c->id ?? '' ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="index.php?route=admin/client/toggle?id=<?= $c->id ?? '' ?>" class="btn btn-warning btn-sm"><i class="fas fa-toggle-on"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Aucun client trouvé.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>