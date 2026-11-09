<?php
require_once __DIR__ . '/../config/database.php';

class VirementModel {
    private $db;
    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function findByClientId($clientId) {
        $stmt = $this->db->prepare("
            SELECT v.*, 
                   cs.numero_compte as source_numero, 
                   cd.numero_compte as dest_numero
            FROM virements v
            JOIN comptes cs ON v.compte_source_id = cs.id
            JOIN comptes cd ON v.compte_destinataire_id = cd.id
            WHERE cs.client_id = ? OR cd.client_id = ?
            ORDER BY v.date_execution DESC
        ");
        $stmt->execute([$clientId, $clientId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}