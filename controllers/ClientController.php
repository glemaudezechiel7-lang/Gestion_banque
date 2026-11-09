<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CompteModel.php';
require_once __DIR__ . '/../models/TransactionModel.php';
require_once __DIR__ . '/../models/CarteModel.php';        // ← Ajouter
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';

class ClientController {
    private $clientModel;
    private $compteModel;
    private $transactionModel;

    public function __construct() {
        $this->clientModel = new ClientModel();
        $this->compteModel = new CompteModel();
        $this->transactionModel = new TransactionModel();
    }

    private function checkClient() {
        if (!isClient()) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function dashboard() {
        $this->checkClient();
        $client = $this->clientModel->findById($_SESSION['client_id']);
        $comptes = $this->compteModel->findByClientId($client->id);
        $transactions = [];
        if (!empty($comptes)) {
            $transactions = $this->transactionModel->getLastByCompteId($comptes[0]->id, 20);
        }
        require_once __DIR__ . '/../view/client/dashboard.php';
    }

    public function depot() {
        $this->checkClient();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Token CSRF invalide.';
                header('Location: index.php?route=client/depot');
                exit;
            }
            $montant = floatval($_POST['montant']);
            $code = $_POST['code'];
            $client = $this->clientModel->findById($_SESSION['client_id']);
            if ($code !== $client->code) {
                $_SESSION['error'] = 'Code incorrect. Opération annulée.';
                header('Location: index.php?route=client/depot');
                exit;
            }
            $comptes = $this->compteModel->findByClientId($client->id);
            if (empty($comptes)) {
                $_SESSION['error'] = 'Aucun compte trouvé.';
                header('Location: index.php?route=client/depot');
                exit;
            }
            try {
                $this->transactionModel->depot($comptes[0]->id, $montant, 'Dépôt en ligne');
                $_SESSION['success'] = 'Dépôt effectué avec succès !';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: index.php?route=client/depot');
            exit;
        }
        require_once __DIR__ . '/../view/client/depot.php';
    }

    public function retrait() {
        $this->checkClient();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Token CSRF invalide.';
                header('Location: index.php?route=client/retrait');
                exit;
            }
            $montant = floatval($_POST['montant']);
            $code = $_POST['code'];
            $client = $this->clientModel->findById($_SESSION['client_id']);
           if ($code !== $client->code) {
                $_SESSION['error'] = 'Code incorrect. Opération annulée.';
                header('Location: index.php?route=client/retrait');
                exit;
            }
            $comptes = $this->compteModel->findByClientId($client->id);
            if (empty($comptes)) {
                $_SESSION['error'] = 'Aucun compte trouvé.';
                header('Location: index.php?route=client/retrait');
                exit;
            }
            try {
                $this->transactionModel->retrait($comptes[0]->id, $montant, 'Retrait en ligne');
                $_SESSION['success'] = 'Retrait effectué avec succès !';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: index.php?route=client/retrait');
            exit;
        }
        require_once __DIR__ . '/../view/client/retrait.php';
    }

    public function virement() {
        $this->checkClient();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Token CSRF invalide.';
                header('Location: index.php?route=client/virement');
                exit;
            }
            $compteDestNum = trim($_POST['compte_dest']);
            $montant = floatval($_POST['montant']);
            $motif = trim($_POST['motif']);
            $code = $_POST['code'];

            $client = $this->clientModel->findById($_SESSION['client_id']);
            if ($code !== $client->code) {
                $_SESSION['error'] = 'Code incorrect. Opération annulée.';
                header('Location: index.php?route=client/virement');
                exit;
            }

            $comptesSource = $this->compteModel->findByClientId($client->id);
            if (empty($comptesSource)) {
                $_SESSION['error'] = 'Aucun compte source trouvé.';
                header('Location: index.php?route=client/virement');
                exit;
            }

            $compteDest = $this->compteModel->findByNumero($compteDestNum);
            if (!$compteDest) {
                $_SESSION['error'] = 'Compte destinataire introuvable.';
                header('Location: index.php?route=client/virement');
                exit;
            }

            if ($comptesSource[0]->id == $compteDest->id) {
                $_SESSION['error'] = 'Virement vers votre propre compte interdit.';
                header('Location: index.php?route=client/virement');
                exit;
            }

            try {
                $this->transactionModel->virement($comptesSource[0]->id, $compteDest->id, $montant, $motif);
                $_SESSION['success'] = 'Virement effectué avec succès !';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: index.php?route=client/virement');
            exit;
        }
        require_once __DIR__ . '/../view/client/virement.php';
    }


    // ==================== GESTION DES CARTES ====================
public function cartes() {
    $this->checkClient();
    $clientId = $_SESSION['client_id'];
    $carteModel = new CarteModel();
    $compteModel = new CompteModel();

    $comptes = $compteModel->findByClientId($clientId);
    $cartes = $carteModel->getCartesByClient($clientId);
    $demandes = $carteModel->getDemandesByClient($clientId);
    $solde = $carteModel->getSoldeCompte($clientId);
    $notificationModel = new NotificationModel();
    $notifications = $notificationModel->getByClient($clientId);
    $unreadCount = $notificationModel->getUnreadCount($clientId);

    require_once __DIR__ . '/../view/client/cartes.php';
}

public function demanderCarte() {
    $this->checkClient();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clientId = $_SESSION['client_id'];
        $compteId = $_POST['compte_id'] ?? 0;
        $typeCarte = $_POST['type_carte'] ?? 'Débit';

        $carteModel = new CarteModel();
        $result = $carteModel->demanderCarte($clientId, $compteId, $typeCarte);

        $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
        header('Location: index.php?route=client/cartes');
        exit;
    }
}

public function bloquerCarte() {
    $this->checkClient();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $carteId = $_POST['carte_id'] ?? 0;
        $clientId = $_SESSION['client_id'];
        $carteModel = new CarteModel();
        $success = $carteModel->bloquerCarte($carteId, $clientId);
        $_SESSION[$success ? 'success' : 'error'] = $success ? 'Carte bloquée.' : 'Erreur.';
        header('Location: index.php?route=client/cartes');
        exit;
    }
}

public function debloquerCarte() {
    $this->checkClient();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $carteId = $_POST['carte_id'] ?? 0;
        $clientId = $_SESSION['client_id'];
        $carteModel = new CarteModel();
        $success = $carteModel->debloquerCarte($carteId, $clientId);
        $_SESSION[$success ? 'success' : 'error'] = $success ? 'Carte débloquée.' : 'Erreur.';
        header('Location: index.php?route=client/cartes');
        exit;
    }
}

public function changerPin() {
    $this->checkClient();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $carteId = $_POST['carte_id'] ?? 0;
        $ancienPin = $_POST['ancien_pin'] ?? '';
        $nouveauPin = $_POST['nouveau_pin'] ?? '';
        $clientId = $_SESSION['client_id'];

        if (strlen($nouveauPin) != 4 || !ctype_digit($nouveauPin)) {
            $_SESSION['error'] = 'Le nouveau PIN doit être composé de 4 chiffres.';
            header('Location: index.php?route=client/cartes');
            exit;
        }

        $carteModel = new CarteModel();
        $result = $carteModel->changerPIN($carteId, $clientId, $ancienPin, $nouveauPin);
        $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
        header('Location: index.php?route=client/cartes');
        exit;
    }
}

// ==================== NOTIFICATIONS ====================
public function notifications() {
    $this->checkClient();
    $clientId = $_SESSION['client_id'];
    $notificationModel = new NotificationModel();
    $notifications = $notificationModel->getByClient($clientId, 100);
    $unreadCount = $notificationModel->getUnreadCount($clientId);
    require_once __DIR__ . '/../view/client/notifications.php';
}

public function marquerLu() {
    $this->checkClient();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? 0;
        $clientId = $_SESSION['client_id'];
        $notificationModel = new NotificationModel();
        $notificationModel->markAsRead($id, $clientId);
        header('Location: index.php?route=client/notifications');
        exit;
    }
}

public function marquerToutLu() {
    $this->checkClient();
    $clientId = $_SESSION['client_id'];
    $notificationModel = new NotificationModel();
    $notificationModel->markAllAsRead($clientId);
    header('Location: index.php?route=client/notifications');
    exit;
}
}
?>