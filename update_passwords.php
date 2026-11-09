<?php
// update_passwords.php
// Exécutez ce script une seule fois pour hacher les mots de passe des utilisateurs

require_once __DIR__ . '/config/database.php';

$config = require __DIR__ . '/config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
try {
    $db = new PDO($dsn, $config['username'], $config['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer tous les utilisateurs
    $stmt = $db->query("SELECT id, mot_de_passe FROM utilisateurs");
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);

    foreach ($users as $user) {
        // Vérifier si le mot de passe est déjà hashé (commence par $2y$)
        if (strpos($user->mot_de_passe, '$2y$') === 0) {
            echo "Utilisateur ID {$user->id} déjà hashé, ignoré.<br>";
            continue;
        }

        // Hasher le mot de passe en clair
        $hashed = password_hash($user->mot_de_passe, PASSWORD_DEFAULT);
        $update = $db->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        $update->execute([$hashed, $user->id]);
        echo "Utilisateur ID {$user->id} mis à jour.<br>";
    }

    echo "✅ Tous les mots de passe ont été hashés avec succès.";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>