<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INCONNUBANK - Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="card bg-dark text-white p-4 shadow-lg" style="max-width: 600px; width: 100%; border: 1px solid #00aaff;">
        <h2 class="text-center text-info"><i class="fas fa-user-plus me-2"></i>✦ Créer un compte</h2>
        <p class="text-center text-secondary">Inscription rapide</p>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form method="POST" action="index.php?route=register">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control bg-secondary text-white" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control bg-secondary text-white" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control bg-secondary text-white" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" class="form-control bg-secondary text-white" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Adresse</label>
                <input type="text" name="adresse" class="form-control bg-secondary text-white" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pays</label>
                    <input type="text" name="pays" class="form-control bg-secondary text-white" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sexe</label>
                    <select name="sexe" class="form-select bg-secondary text-white">
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control bg-secondary text-white" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="code" class="form-control bg-secondary text-white" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type de compte</label>
                    <select name="type_compte" class="form-select bg-secondary text-white">
                        <option value="Courant">Courant</option>
                        <option value="Épargne">Épargne</option>
                        <option value="Dollar">Dollar</option>
                        <option value="Euro">Euro</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-info w-100 fw-bold"><i class="fas fa-user-check me-2"></i>S'INSCRIRE</button>
        </form>
        <p class="mt-3 text-center"><a href="index.php?route=login" class="text-info">Déjà un compte ? Connectez-vous</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>