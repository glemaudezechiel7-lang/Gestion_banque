<?php
require_once __DIR__ . '/../config/database.php';

class NotificationModel {
    private $db;

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function add($clientId, $type, $titre, $message) {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (client_id, type, titre, message)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$clientId, $type, $titre, $message]);
    }

    public function getByClient($clientId, $limit = 20) {
        // ✅ Correction : convertir $limit en entier et l'insérer directement
        $limit = intval($limit);
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE client_id = ?
            ORDER BY date_creation DESC LIMIT $limit
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUnreadCount($clientId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE client_id = ? AND lu = 0
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($id, $clientId) {
        $stmt = $this->db->prepare("
            UPDATE notifications SET lu = 1
            WHERE id = ? AND client_id = ?
        ");
        return $stmt->execute([$id, $clientId]);
    }

    public function markAllAsRead($clientId) {
        $stmt = $this->db->prepare("
            UPDATE notifications SET lu = 1
            WHERE client_id = ? AND lu = 0
        ");
        return $stmt->execute([$clientId]);
    }
}