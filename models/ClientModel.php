<?php
require_once __DIR__ . '/../config/database.php';

class ClientModel {
    private $db;
    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Authentification : comparaison en clair
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE email = ? AND actif = 1 AND code = ?");
        $stmt->execute([$email, $password]);
        $client = $stmt->fetch(PDO::FETCH_OBJ);
        if ($client) {
            return $client;
        }
        return false;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM clients ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Inscription : stockage en clair
    public function create($data) {
        // Aucun hachage, on stocke le code tel quel
        $stmt = $this->db->prepare("
            INSERT INTO clients (nom, prenom, email, telephone, adresse, pays, sexe, date_naissance, code, image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $success = $stmt->execute([
            $data['nom'], $data['prenom'], $data['email'], $data['telephone'],
            $data['adresse'], $data['pays'], $data['sexe'], $data['date_naissance'],
            $data['code'], $data['image'] ?? null
        ]);
        if ($success) {
            return $this->db->lastInsertId();
        }
        return false;
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
        if (isset($data['pays'])) { $fields[] = "pays = ?"; $params[] = $data['pays']; }
        if (isset($data['sexe'])) { $fields[] = "sexe = ?"; $params[] = $data['sexe']; }
        if (isset($data['date_naissance'])) { $fields[] = "date_naissance = ?"; $params[] = $data['date_naissance']; }
        if (isset($data['actif'])) { $fields[] = "actif = ?"; $params[] = $data['actif']; }
        if (isset($data['code'])) { 
            $fields[] = "code = ?"; 
            $params[] = $data['code']; // stockage en clair
        }
        if (isset($data['image'])) { $fields[] = "image = ?"; $params[] = $data['image']; }
        $params[] = $id;
        $sql = "UPDATE clients SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }
}