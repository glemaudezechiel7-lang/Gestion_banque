<?php
require_once __DIR__ . '/../config/database.php';

class TransactionModel {
    private $db;
    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function depot($compteId, $montant, $description) {
        $this->db->beginTransaction();
        try {
            // Mettre à jour le solde
            $stmt = $this->db->prepare("UPDATE comptes SET solde = solde + ? WHERE id = ?");
            $stmt->execute([$montant, $compteId]);

            // Générer référence unique
            $ref = 'DEP' . uniqid();

            // Insérer transaction
            $stmt = $this->db->prepare("
                INSERT INTO transactions (compte_id, type_transaction, montant, description, reference, solde_apres)
                SELECT ?, 'Dépôt', ?, ?, ?, solde FROM comptes WHERE id = ?
            ");
            $stmt->execute([$compteId, $montant, $description, $ref, $compteId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function retrait($compteId, $montant, $description) {
        $this->db->beginTransaction();
        try {
            // Vérifier solde
            $stmt = $this->db->prepare("SELECT solde FROM comptes WHERE id = ? FOR UPDATE");
            $stmt->execute([$compteId]);
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if ($row->solde < $montant) {
                throw new Exception("Solde insuffisant");
            }

            // Mettre à jour solde
            $stmt = $this->db->prepare("UPDATE comptes SET solde = solde - ? WHERE id = ?");
            $stmt->execute([$montant, $compteId]);

            $ref = 'RET' . uniqid();
            $stmt = $this->db->prepare("
                INSERT INTO transactions (compte_id, type_transaction, montant, description, reference, solde_apres)
                SELECT ?, 'Retrait', ?, ?, ?, solde FROM comptes WHERE id = ?
            ");
            $stmt->execute([$compteId, $montant, $description, $ref, $compteId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
public function virement($compteSourceId, $compteDestId, $montant, $motif) {
    $this->db->beginTransaction();
    try {
        // Vérifier solde source
        $stmt = $this->db->prepare("SELECT solde FROM comptes WHERE id = ? FOR UPDATE");
        $stmt->execute([$compteSourceId]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if ($row->solde < $montant) {
            throw new Exception("Solde insuffisant");
        }

        // Débiter source
        $stmt = $this->db->prepare("UPDATE comptes SET solde = solde - ? WHERE id = ?");
        $stmt->execute([$montant, $compteSourceId]);

        // Créditer destination
        $stmt = $this->db->prepare("UPDATE comptes SET solde = solde + ? WHERE id = ?");
        $stmt->execute([$montant, $compteDestId]);

        // Génération de DEUX références UNIQUES
        $refSource = 'VIR' . time() . '_' . bin2hex(random_bytes(8));
        usleep(1); // Évite un timestamp identique
        $refDest   = 'VIR' . time() . '_' . bin2hex(random_bytes(8));

        // Transaction source (débit)
        $stmt = $this->db->prepare("
            INSERT INTO transactions (compte_id, type_transaction, montant, description, reference, solde_apres)
            SELECT ?, 'Virement', -?, ?, ?, solde FROM comptes WHERE id = ?
        ");
        $stmt->execute([$compteSourceId, $montant, "Virement vers compte " . $compteDestId . " - " . $motif, $refSource, $compteSourceId]);

        // Transaction destination (crédit)
        $stmt = $this->db->prepare("
            INSERT INTO transactions (compte_id, type_transaction, montant, description, reference, solde_apres)
            SELECT ?, 'Virement', ?, ?, ?, solde FROM comptes WHERE id = ?
        ");
        $stmt->execute([$compteDestId, $montant, "Virement de compte " . $compteSourceId . " - " . $motif, $refDest, $compteDestId]);

        // Enregistrer dans la table virements (avec la référence source pour le suivi)
        $stmt = $this->db->prepare("
            INSERT INTO virements (transaction_id, compte_source_id, compte_destinataire_id, reference, motif, statut)
            SELECT (SELECT id FROM transactions WHERE reference = ?), ?, ?, ?, ?, 'Exécuté'
        ");
        $stmt->execute([$refSource, $compteSourceId, $compteDestId, $refSource, $motif]);

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

    public function getLastByCompteId($compteId, $limit = 20) {
        $limit = intval($limit);
        $stmt = $this->db->prepare("
            SELECT t.*, c.numero_compte, cl.nom as client_nom, cl.prenom as client_prenom 
            FROM transactions t
            JOIN comptes c ON t.compte_id = c.id
            JOIN clients cl ON c.client_id = cl.id
            WHERE t.compte_id = ?
            ORDER BY t.date_transaction DESC LIMIT $limit
        ");
        $stmt->execute([$compteId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAll($limit = 100, $offset = 0) {
        $limit = intval($limit);
        $offset = intval($offset);
        $stmt = $this->db->prepare("
            SELECT t.*, c.numero_compte, cl.nom as client_nom, cl.prenom as client_prenom 
            FROM transactions t
            JOIN comptes c ON t.compte_id = c.id
            JOIN clients cl ON c.client_id = cl.id
            ORDER BY t.date_transaction DESC LIMIT $limit OFFSET $offset
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM transactions");
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }

    public function sumByType($type) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(montant), 0) as total 
            FROM transactions 
            WHERE type_transaction = ? AND statut = 'Approuvée'
        ");
        $stmt->execute([$type]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0;
    }

    public function getDailyTransactionCounts($days = 30) {
    $stmt = $this->db->prepare("
        SELECT DATE(date_transaction) as date, COUNT(*) as count
        FROM transactions
        WHERE date_transaction >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY DATE(date_transaction)
        ORDER BY date_transaction ASC
    ");
    $stmt->execute([$days]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
}
?>