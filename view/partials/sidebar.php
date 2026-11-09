<?php
$role = $_SESSION['role'] ?? 'Client';
$base = 'index.php?route=';
$current = basename($_SERVER['REQUEST_URI']);
?>
<ul class="nav flex-column">
    <?php if ($role === 'Administrateur'): ?>
        <li class="nav-item"><a class="nav-link <?= $current == 'dashboard' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>admin/dashboard"><i class="fas fa-chart-line me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'clients' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>admin/clients"><i class="fas fa-users me-2"></i>Clients</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'comptes' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>admin/comptes"><i class="fas fa-wallet me-2"></i>Comptes</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'transactions' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>admin/transactions"><i class="fas fa-list me-2"></i>Transactions</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'utilisateurs' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>admin/utilisateurs"><i class="fas fa-user-cog me-2"></i>Utilisateurs</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'demandes-cartes' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>admin/demandes-cartes"><i class="fas fa-credit-card me-2"></i>Demandes cartes</a></li>
    <?php elseif ($role === 'Comptable'): ?>
        <li class="nav-item"><a class="nav-link <?= $current == 'dashboard' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>comptable/dashboard"><i class="fas fa-chart-pie me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'transactions' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>comptable/transactions"><i class="fas fa-list me-2"></i>Transactions</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'rapports' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>comptable/rapports"><i class="fas fa-file-alt me-2"></i>Rapports</a></li>
    <?php elseif ($role === 'Caissier'): ?>
        <li class="nav-item"><a class="nav-link <?= $current == 'dashboard' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>caissier/dashboard"><i class="fas fa-cash-register me-2"></i>Opérations</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'history' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>caissier/history"><i class="fas fa-history me-2"></i>Historique</a></li>
    <?php else: // Client ?>
        <li class="nav-item"><a class="nav-link <?= $current == 'dashboard' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>client/dashboard"><i class="fas fa-home me-2"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'depot' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>client/depot"><i class="fas fa-arrow-up me-2"></i>Dépôt</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'retrait' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>client/retrait"><i class="fas fa-arrow-down me-2"></i>Retrait</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'virement' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>client/virement"><i class="fas fa-exchange-alt me-2"></i>Virement</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'cartes' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>client/cartes"><i class="fas fa-credit-card me-2"></i>Cartes</a></li>
        <li class="nav-item"><a class="nav-link <?= $current == 'notifications' ? 'text-info' : 'text-light' ?>" href="<?= $base ?>client/notifications"><i class="fas fa-bell me-2"></i>Notifications</a></li>
    <?php endif; ?>
</ul>