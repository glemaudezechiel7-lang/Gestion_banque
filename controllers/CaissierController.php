<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CompteModel.php';
require_once __DIR__ . '/../models/TransactionModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';

class CaissierController {
    private $clientModel;
    private $compteModel;
    private $transactionModel;
    private $userModel;

    public function __construct() {
        if (!isCaissier()) {
            header('Location: index.php?route=login');
            exit;
        }
        $this->clientModel = new ClientModel();
        $this->compteModel = new CompteModel();
        $this->transactionModel = new TransactionModel();
        $this->userModel = new UserModel();
    }

    public function dashboard() {
        $message = '';
        $compte = null;
        $client = null;
        $numeroCompte = '';

        // Recherche
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
            $numeroCompte = trim($_POST['numero_compte']);
            $compte = $this->compteModel->findByNumero($numeroCompte);
            if ($compte) {
                $client = $this->clientModel->findById($compte->client_id);
            } else {
                $message = '❌ Compte introuvable.';
            }
        }

        // Opération
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operation'])) {
            $numeroCompte = trim($_POST['numero_compte']);
            $montant = floatval($_POST['montant']);
            $type = $_POST['type'];
            $description = trim($_POST['description']);
            $code = $_POST['code'];

            // Vérifier le code du caissier en clair
            $caissier = $this->userModel->findById($_SESSION['user_id']);
            // ✅ Comparaison directe (pas de hash)
            if ($code !== $caissier->mot_de_passe) {
                $message = '❌ Code incorrect. Opération annulée.';
            } else {
                $compte = $this->compteModel->findByNumero($numeroCompte);
                if (!$compte) {
                    $message = '❌ Compte introuvable.';
                } else {
                    try {
                        if ($type === 'depot') {
                            $this->transactionModel->depot($compte->id, $montant, $description);
                            $message = '✅ Dépôt effectué avec succès.';
                        } elseif ($type === 'retrait') {
                            if ($compte->solde < $montant) {
                                $message = '❌ Solde insuffisant.';
                            } else {
                                $this->transactionModel->retrait($compte->id, $montant, $description);
                                $message = '✅ Retrait effectué avec succès.';
                            }
                        } else {
                            $message = '❌ Type d\'opération invalide.';
                        }
                        // Rafraîchir les infos
                        $compte = $this->compteModel->findByNumero($numeroCompte);
                        $client = $this->clientModel->findById($compte->client_id);
                    } catch (Exception $e) {
                        $message = '❌ ' . $e->getMessage();
                    }
                }
            }
        }

        require_once __DIR__ . '/../view/caissier/dashboard.php';
    }

    public function history() {
        $transactions = $this->transactionModel->getAll(200);
        require_once __DIR__ . '/../view/caissier/history.php';
    }
}