<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/NotificationModel.php';

class CarteModel {
    private $db;
    private $notificationModel;

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->db = new PDO($dsn, $config['username'], $config['password']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->notificationModel = new NotificationModel();
    }

    // ==================== GÉNÉRATION ====================
    public function genererNumeroCarte() {
        $prefix = '4'; // Visa-like
        $length = 16;
        $number = $prefix . str_pad('', $length - 1, '0');
        for ($i = 1; $i < $length - 1; $i++) {
            $number[$i] = rand(0, 9);
        }
        $sum = 0;
        $alt = false;
        for ($i = $length - 1; $i >= 0; $i--) {
            $n = intval($number[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        $check = (10 - ($sum % 10)) % 10;
        $number[$length - 1] = $check;
        return 'INN' . $number;
    }

    public function genererCVV() {
        return str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    }

    public function genererPIN() {
        return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function genererDateExpiration($annees = 5) {
        return date('Y-m-d', strtotime('+' . $annees . ' years'));
    }

    // ==================== CRÉATION ====================
    public function creerCarte($compteId, $typeCarte = 'Débit', $plafond = 1000.00) {
        $numero = $this->genererNumeroCarte();
        $cvv = $this->genererCVV();
        $pin = $this->genererPIN();
        $expiration = $this->genererDateExpiration(5);

        $cvvHash = password_hash($cvv, PASSWORD_DEFAULT);
        $pinHash = password_hash($pin, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO cartes (compte_id, numero_carte, type_carte, plafond, date_expiration, cvv_hash, pin_hash, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Actif')
        ");
        $success = $stmt->execute([$compteId, $numero, $typeCarte, $plafond, $expiration, $cvvHash, $pinHash]);

        if ($success) {
            $stmtClient = $this->db->prepare("SELECT client_id FROM comptes WHERE id = ?");
            $stmtClient->execute([$compteId]);
            $client = $stmtClient->fetch(PDO::FETCH_OBJ);
            if ($client) {
                $this->notificationModel->add(
                    $client->client_id,
                    'success',
                    'Carte créée 🎉',
                    "Votre carte se terminant par " . substr($numero, -4) . " a été créée. Expire le " . date('d/m/Y', strtotime($expiration)) . "."
                );
            }
            return $this->db->lastInsertId();
        }
        return false;
    }

    // ==================== DEMANDE ====================
    public function demanderCarte($clientId, $compteId, $typeCarte = 'Débit') {
        $stmtCheck = $this->db->prepare("SELECT id FROM demandes_cartes WHERE client_id = ? AND statut = 'En attente'");
        $stmtCheck->execute([$clientId]);
        if ($stmtCheck->fetch()) {
            return ['success' => false, 'message' => 'Vous avez déjà une demande en attente.'];
        }

        $stmtSolde = $this->db->prepare("SELECT solde FROM comptes WHERE id = ? AND client_id = ?");
        $stmtSolde->execute([$compteId, $clientId]);
        $compte = $stmtSolde->fetch(PDO::FETCH_OBJ);
        if (!$compte || $compte->solde < 5) {
            return ['success' => false, 'message' => 'Solde minimum de 5 USD requis pour demander une carte.'];
        }

        $stmt = $this->db->prepare("INSERT INTO demandes_cartes (client_id, type_carte) VALUES (?, ?)");
        $success = $stmt->execute([$clientId, $typeCarte]);
        if ($success) {
            $this->notificationModel->add($clientId, 'info', 'Demande de carte en cours', 'Votre demande a été envoyée. Traitement sous 48h.');
            return ['success' => true, 'message' => 'Demande envoyée avec succès.'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la demande.'];
    }

    // ==================== APPROBATION ====================
    public function approuverDemande($demandeId, $adminId) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT * FROM demandes_cartes WHERE id = ? AND statut = 'En attente'");
            $stmt->execute([$demandeId]);
            $demande = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$demande) throw new Exception("Demande introuvable ou déjà traitée.");

            $stmtCompte = $this->db->prepare("SELECT id FROM comptes WHERE client_id = ? LIMIT 1");
            $stmtCompte->execute([$demande->client_id]);
            $compte = $stmtCompte->fetch(PDO::FETCH_OBJ);
            if (!$compte) throw new Exception("Aucun compte trouvé pour ce client.");

            $this->creerCarte($compte->id, $demande->type_carte);

            $stmtUpdate = $this->db->prepare("UPDATE demandes_cartes SET statut = 'Approuvée', date_traitement = NOW(), traite_par = ? WHERE id = ?");
            $stmtUpdate->execute([$adminId, $demandeId]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Carte créée avec succès.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function rejeterDemande($demandeId, $adminId, $commentaire = '') {
        $stmt = $this->db->prepare("UPDATE demandes_cartes SET statut = 'Rejetée', date_traitement = NOW(), traite_par = ?, commentaire = ? WHERE id = ?");
        $success = $stmt->execute([$adminId, $commentaire, $demandeId]);
        if ($success) {
            $stmtClient = $this->db->prepare("SELECT client_id FROM demandes_cartes WHERE id = ?");
            $stmtClient->execute([$demandeId]);
            $demande = $stmtClient->fetch(PDO::FETCH_OBJ);
            if ($demande) {
                $this->notificationModel->add($demande->client_id, 'danger', 'Demande de carte rejetée', "Raison : " . ($commentaire ?: 'Non précisée.'));
            }
            return true;
        }
        return false;
    }

    // ==================== RECHERCHE ====================
    public function getCartesByCompte($compteId) {
        $stmt = $this->db->prepare("SELECT * FROM cartes WHERE compte_id = ? ORDER BY created_at DESC");
        $stmt->execute([$compteId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCartesByClient($clientId) {
        $stmt = $this->db->prepare("
            SELECT c.*, cp.numero_compte
            FROM cartes c
            JOIN comptes cp ON c.compte_id = cp.id
            WHERE cp.client_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDemandesByClient($clientId) {
        $stmt = $this->db->prepare("SELECT * FROM demandes_cartes WHERE client_id = ? ORDER BY date_demande DESC");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDemandesEnAttente() {
        $stmt = $this->db->prepare("
            SELECT d.*, c.nom, c.prenom, c.email
            FROM demandes_cartes d
            JOIN clients c ON d.client_id = c.id
            WHERE d.statut = 'En attente'
            ORDER BY d.date_demande ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCardById($id) {
        $stmt = $this->db->prepare("SELECT * FROM cartes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ==================== GESTION ====================
    public function bloquerCarte($id, $clientId) {
        $stmt = $this->db->prepare("
            UPDATE cartes c
            JOIN comptes cp ON c.compte_id = cp.id
            SET c.statut = 'Bloqué'
            WHERE c.id = ? AND cp.client_id = ?
        ");
        $success = $stmt->execute([$id, $clientId]);
        if ($success) {
            $this->notificationModel->add($clientId, 'warning', 'Carte bloquée 🔒', 'Votre carte a été bloquée sur votre demande.');
        }
        return $success;
    }

    public function debloquerCarte($id, $clientId) {
        $stmt = $this->db->prepare("
            UPDATE cartes c
            JOIN comptes cp ON c.compte_id = cp.id
            SET c.statut = 'Actif'
            WHERE c.id = ? AND cp.client_id = ?
        ");
        $success = $stmt->execute([$id, $clientId]);
        if ($success) {
            $this->notificationModel->add($clientId, 'success', 'Carte débloquée 🔓', 'Votre carte a été débloquée.');
        }
        return $success;
    }

    public function changerPIN($id, $clientId, $ancienPIN, $nouveauPIN) {
        $stmt = $this->db->prepare("
            SELECT c.pin_hash
            FROM cartes c
            JOIN comptes cp ON c.compte_id = cp.id
            WHERE c.id = ? AND cp.client_id = ?
        ");
        $stmt->execute([$id, $clientId]);
        $carte = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$carte || !password_verify($ancienPIN, $carte->pin_hash)) {
            return ['success' => false, 'message' => 'Ancien PIN incorrect.'];
        }

        $nouveauHash = password_hash($nouveauPIN, PASSWORD_DEFAULT);
        $stmtUpdate = $this->db->prepare("
            UPDATE cartes c
            JOIN comptes cp ON c.compte_id = cp.id
            SET c.pin_hash = ?
            WHERE c.id = ? AND cp.client_id = ?
        ");
        $success = $stmtUpdate->execute([$nouveauHash, $id, $clientId]);
        if ($success) {
            $this->notificationModel->add($clientId, 'info', 'PIN modifié 🔑', 'Votre code PIN a été modifié avec succès.');
            return ['success' => true, 'message' => 'PIN modifié avec succès.'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la modification du PIN.'];
    }

    public function getSoldeCompte($clientId) {
        $stmt = $this->db->prepare("SELECT solde FROM comptes WHERE client_id = ?");
        $stmt->execute([$clientId]);
        $compte = $stmt->fetch(PDO::FETCH_OBJ);
        return $compte ? $compte->solde : 0;
    }
}