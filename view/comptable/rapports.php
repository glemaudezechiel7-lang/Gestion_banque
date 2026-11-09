<?php
$title = 'Rapports financiers';
ob_start();
?>

<h2 class="text-light"><i class="fas fa-file-alt me-2"></i>Rapports financiers</h2>
<hr class="border-info">

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-arrow-up text-success"></i> Total Dépôts</h5>
                <h3 class="text-success"><?= isset($totalDepots) ? number_format($totalDepots, 2) : '0.00' ?> USD</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-arrow-down text-danger"></i> Total Retraits</h5>
                <h3 class="text-danger"><?= isset($totalRetraits) ? number_format($totalRetraits, 2) : '0.00' ?> USD</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark text-white border-info shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-exchange-alt text-warning"></i> Total Virements</h5>
                <h3 class="text-warning"><?= isset($totalVirements) ? number_format($totalVirements, 2) : '0.00' ?> USD</h3>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>