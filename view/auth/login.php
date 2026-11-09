<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INCONNUBANK - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">
    <div class="card bg-dark text-white p-4 shadow-lg" style="max-width: 420px; width: 100%; border: 1px solid #00aaff;">
        <h2 class="text-center text-info"><i class="fas fa-university me-2"></i>✦ INCONNUBANK</h2>
        <p class="text-center text-secondary">Connexion sécurisée</p>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <form method="POST" action="index.php?route=login">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control bg-secondary text-white" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control bg-secondary text-white" required>
            </div>
            <button type="submit" class="btn btn-info w-100 fw-bold"><i class="fas fa-sign-in-alt me-2"></i>SE CONNECTER</button>
        </form>
        <p class="mt-3 text-center"><a href="index.php?route=register" class="text-info">Créer un compte</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>