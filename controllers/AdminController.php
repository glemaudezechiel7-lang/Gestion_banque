<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CompteModel.php';
require_once __DIR__ . '/../models/TransactionModel.php';
require_once __DIR__ . '/../models/CarteModel.php';
require_once __DIR__ . '/../config/session.php';

class AdminController {
    private $userModel;
    private $clientModel;
    private $compteModel;
    private $transactionModel;

    public function __construct() {
        if (!isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
        $this->userModel = new UserModel();
        $this->clientModel = new ClientModel();
        $this->compteModel = new CompteModel();
        $this->transactionModel = new TransactionModel();
    }

  public function dashboard() {
    $clients = $this->clientModel->findAll();
    $comptes = $this->compteModel->findAll();
    $transactions = $this->transactionModel->getAll(20);
    $totalClients = count($clients);
    $totalComptes = $this->compteModel->count();
    $totalTransactions = $this->transactionModel->count();
    $soldeTotal = $this->compteModel->getTotalSolde();

    // Récupérer les statistiques quotidiennes pour le graphique (30 derniers jours)
    $dailyStats = $this->transactionModel->getDailyTransactionCounts(30);

    require_once __DIR__ . '/../view/admin/dashboard.php';
}

    public function clients() {
        $clients = $this->clientModel->findAll();
        require_once __DIR__ . '/../view/admin/clients.php';
    }

    public function comptes() {
        $comptes = $this->compteModel->findAll();
        require_once __DIR__ . '/../view/admin/comptes.php';
    }

    public function transactions() {
        $transactions = $this->transactionModel->getAll(200);
        require_once __DIR__ . '/../view/admin/transactions.php';
    }

    public function utilisateurs() {
        $users = $this->userModel->findAll();
        require_once __DIR__ . '/../view/admin/utilisateurs.php';
    }

    public function clientDetails() {
        $id = $_GET['id'] ?? 0;
        $client = $this->clientModel->findById($id);
        if (!$client) {
            $_SESSION['error'] = 'Client introuvable.';
            header('Location: index.php?route=admin/clients');
            exit;
        }
        echo "<h3>Détails du client</h3>";
        var_dump($client);
        exit;
    }

    public function clientToggle() {
        $id = $_GET['id'] ?? 0;
        $client = $this->clientModel->findById($id);
        if ($client) {
            $newStatus = $client->actif ? 0 : 1;
            $this->clientModel->update($id, ['actif' => $newStatus]);
            $_SESSION['success'] = 'Statut du client modifié.';
        } else {
            $_SESSION['error'] = 'Client introuvable.';
        }
        header('Location: index.php?route=admin/clients');
        exit;
    }

    public function utilisateurEdit() {
        $id = $_GET['id'] ?? 0;
        echo "Édition utilisateur $id";
        exit;
    }

    public function utilisateurToggle() {
        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->findById($id);
        if ($user) {
            $newStatus = $user->actif ? 0 : 1;
            $this->userModel->update($id, ['actif' => $newStatus]);
            $_SESSION['success'] = 'Statut utilisateur modifié.';
        } else {
            $_SESSION['error'] = 'Utilisateur introuvable.';
        }
        header('Location: index.php?route=admin/utilisateurs');
        exit;
    }


// ==================== GESTION DES CARTES (ADMIN) ====================
public function demandesCartes() {
    if (!isAdmin()) { header('Location: index.php?route=login'); exit; }
    $carteModel = new CarteModel();
    $demandes = $carteModel->getDemandesEnAttente();
    require_once __DIR__ . '/../view/admin/demandes_cartes.php';
}

public function approuverCarte() {
    if (!isAdmin()) { header('Location: index.php?route=login'); exit; }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $demandeId = $_POST['demande_id'] ?? 0;
        $adminId = $_SESSION['user_id'];
        $carteModel = new CarteModel();
        $result = $carteModel->approuverDemande($demandeId, $adminId);
        $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
        header('Location: index.php?route=admin/demandes-cartes');
        exit;
    }
}

public function rejeterCarte() {
    if (!isAdmin()) { header('Location: index.php?route=login'); exit; }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $demandeId = $_POST['demande_id'] ?? 0;
        $adminId = $_SESSION['user_id'];
        $commentaire = $_POST['commentaire'] ?? '';
        $carteModel = new CarteModel();
        $success = $carteModel->rejeterDemande($demandeId, $adminId, $commentaire);
        $_SESSION[$success ? 'success' : 'error'] = $success ? 'Demande rejetée.' : 'Erreur.';
        header('Location: index.php?route=admin/demandes-cartes');
        exit;
    }
}
    
}
?>