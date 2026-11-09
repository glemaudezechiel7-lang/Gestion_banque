<?php
$title = 'Notifications';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-bell me-2"></i>Notifications</h2>
<hr class="border-info">

<?php if (!empty($notifications)): ?>
    <div class="mb-3">
        <a href="index.php?route=client/marquer-tout-lu" class="btn btn-info btn-sm">
            <i class="fas fa-check-double me-2"></i>Tout marquer comme lu
        </a>
    </div>
    <div class="list-group">
        <?php foreach ($notifications as $n): ?>
            <div class="list-group-item bg-dark text-white border-<?= $n->lu ? 'secondary' : 'info' ?> mb-2 rounded">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1 <?= $n->lu ? 'text-secondary' : 'text-info' ?>">
                        <?= htmlspecialchars($n->titre) ?>
                        <?php if (!$n->lu): ?>
                            <span class="badge bg-danger">Nouveau</span>
                        <?php endif; ?>
                    </h5>
                    <small class="text-secondary"><?= date('d/m/Y H:i', strtotime($n->date_creation)) ?></small>
                </div>
                <p class="mb-1 <?= $n->lu ? 'text-secondary' : 'text-light' ?>">
                    <?= htmlspecialchars($n->message) ?>
                </p>
                <?php if (!$n->lu): ?>
                    <form method="POST" action="index.php?route=client/marquer-lu" class="mt-2">
                        <input type="hidden" name="id" value="<?= $n->id ?>">
                        <button type="submit" class="btn btn-sm btn-outline-info">Marquer comme lu</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">Aucune notification.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>