<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $db;
    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Authentification : comparaison en clair
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nom as role_nom 
            FROM utilisateurs u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? AND u.mot_de_passe = ? AND u.actif = 1
        ");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        if ($user) {
            return $user;
        }
        return false;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT u.*, r.nom as role_nom FROM utilisateurs u JOIN roles r ON u.role_id = r.id ORDER BY u.id");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Création sans hachage
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nom'], $data['prenom'], $data['email'], 
            $data['mot_de_passe'], // stocké en clair
            $data['telephone'], $data['adresse'], $data['role_id']
        ]);
    }

    // Mise à jour sans hachage
    public function update($id, $data) {
        $fields = [];
        $params = [];
        if (isset($data['nom'])) { $fields[] = "nom = ?"; $params[] = $data['nom']; }
        if (isset($data['prenom'])) { $fields[] = "prenom = ?"; $params[] = $data['prenom']; }
        if (isset($data['email'])) { $fields[] = "email = ?"; $params[] = $data['email']; }
        if (isset($data['telephone'])) { $fields[] = "telephone = ?"; $params[] = $data['telephone']; }
        if (isset($data['adresse'])) { $fields[] = "adresse = ?"; $params[] = $data['adresse']; }
        if (isset($data['role_id'])) { $fields[] = "role_id = ?"; $params[] = $data['role_id']; }
        if (isset($data['actif'])) { $fields[] = "actif = ?"; $params[] = $data['actif']; }
        if (isset($data['mot_de_passe'])) { 
            $fields[] = "mot_de_passe = ?"; 
            $params[] = $data['mot_de_passe']; // en clair
        }
        $params[] = $id;
        $sql = "UPDATE utilisateurs SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}