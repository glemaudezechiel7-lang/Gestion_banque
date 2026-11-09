<?php
require_once __DIR__ . '/../config/database.php';

class CompteModel {
    private $db;
    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function findByClientId($clientId) {
        $stmt = $this->db->prepare("SELECT * FROM comptes WHERE client_id = ?");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByNumero($numero) {
        $stmt = $this->db->prepare("SELECT * FROM comptes WHERE numero_compte = ? AND statut = 'Actif'");
        $stmt->execute([$numero]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT c.*, cl.nom as client_nom, cl.prenom as client_prenom FROM comptes c JOIN clients cl ON c.client_id = cl.id ORDER BY c.id");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateSolde($compteId, $nouveauSolde) {
        $stmt = $this->db->prepare("UPDATE comptes SET solde = ? WHERE id = ?");
        return $stmt->execute([$nouveauSolde, $compteId]);
    }

    public function creerCompte($clientId, $type = 'Courant') {
        $numero = 'INN' . date('Ymd') . rand(100000, 999999);
        $stmt = $this->db->prepare("
            INSERT INTO comptes (client_id, numero_compte, type_compte, solde, devise, date_ouverture)
            VALUES (?, ?, ?, 0.00, 'USD', CURDATE())
        ");
        return $stmt->execute([$clientId, $numero, $type]);
    }

    public function getTotalSolde() {
        $stmt = $this->db->query("SELECT SUM(solde) as total FROM comptes WHERE statut = 'Actif'");
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM comptes");
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }
}