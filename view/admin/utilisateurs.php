<?php
$title = 'Gestion des utilisateurs';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-user-cog me-2"></i>Utilisateurs</h2>
<hr class="border-info">

<?php if (isset($users) && !empty($users)): ?>
    <div class="table-responsive">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u->id ?? '') ?></td>
                    <td><?= htmlspecialchars($u->nom ?? '') ?></td>
                    <td><?= htmlspecialchars($u->prenom ?? '') ?></td>
                    <td><?= htmlspecialchars($u->email ?? '') ?></td>
                    <td><?= htmlspecialchars($u->role_nom ?? '') ?></td>
                    <td><?= isset($u->actif) && $u->actif ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-danger">Inactif</span>' ?></td>
                    <td>
                        <a href="index.php?route=admin/utilisateur/edit?id=<?= $u->id ?? '' ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                        <a href="index.php?route=admin/utilisateur/toggle?id=<?= $u->id ?? '' ?>" class="btn btn-warning btn-sm"><i class="fas fa-toggle-on"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Aucun utilisateur trouvé.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>