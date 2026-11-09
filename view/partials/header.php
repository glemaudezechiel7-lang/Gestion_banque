<?php
$role = $_SESSION['role'] ?? null;
$prenom = $_SESSION['prenom'] ?? null;
$nom = $_SESSION['nom'] ?? null;
$isLoggedIn = isLoggedIn();
$base = 'index.php?route=';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-info">
    <div class="container-fluid">
        <a class="navbar-brand text-info fw-bold" href="<?= $isLoggedIn ? $base : $base . 'login' ?>">
            <i class="fas fa-university me-2"></i>✦ INCONNUBANK
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($isLoggedIn): ?>
                    <?php if ($role === 'Administrateur'): ?>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>admin/dashboard"><i class="fas fa-chart-line me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>admin/clients"><i class="fas fa-users me-1"></i>Clients</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>admin/comptes"><i class="fas fa-wallet me-1"></i>Comptes</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>admin/transactions"><i class="fas fa-list me-1"></i>Transactions</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>admin/utilisateurs"><i class="fas fa-user-cog me-1"></i>Utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>admin/demandes-cartes"><i class="fas fa-credit-card me-1"></i>Cartes</a></li>
                    <?php elseif ($role === 'Comptable'): ?>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>comptable/dashboard"><i class="fas fa-chart-pie me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>comptable/transactions"><i class="fas fa-list me-1"></i>Transactions</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>comptable/rapports"><i class="fas fa-file-alt me-1"></i>Rapports</a></li>
                    <?php elseif ($role === 'Caissier'): ?>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>caissier/dashboard"><i class="fas fa-cash-register me-1"></i>Opérations</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>caissier/history"><i class="fas fa-history me-1"></i>Historique</a></li>
                    <?php else: // Client ?>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>client/dashboard"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>client/depot"><i class="fas fa-arrow-up me-1"></i>Dépôt</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>client/retrait"><i class="fas fa-arrow-down me-1"></i>Retrait</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>client/virement"><i class="fas fa-exchange-alt me-1"></i>Virement</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>client/cartes"><i class="fas fa-credit-card me-1"></i>Cartes</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>client/notifications"><i class="fas fa-bell me-1"></i>Notifications</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>login"><i class="fas fa-sign-in-alt me-1"></i>Connexion</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="<?= $base ?>register"><i class="fas fa-user-plus me-1"></i>Inscription</a></li>
                <?php endif; ?>
            </ul>

            <?php if ($isLoggedIn): ?>
                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= htmlspecialchars($prenom . ' ' . $nom) ?>
                    </span>
                    <a href="<?= $base ?>logout" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>