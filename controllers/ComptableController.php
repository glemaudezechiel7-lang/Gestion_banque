<?php
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CompteModel.php';
require_once __DIR__ . '/../models/TransactionModel.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';

class ComptableController {
    private $clientModel;
    private $compteModel;
    private $transactionModel;

    public function __construct() {
        if (!isComptable()) {
            header('Location: index.php?route=login');
            exit;
        }
        $this->clientModel = new ClientModel();
        $this->compteModel = new CompteModel();
        $this->transactionModel = new TransactionModel();
    }

    // ==================== DASHBOARD ====================
    public function dashboard() {
        $clients = $this->clientModel->findAll();
        $transactions = $this->transactionModel->getAll(50);
        $totalClients = count($clients);
        $totalComptes = $this->compteModel->count();
        $totalTransactions = $this->transactionModel->count();
        $soldeTotal = $this->compteModel->getTotalSolde();
        require_once __DIR__ . '/../view/comptable/dashboard.php';
    }

    // ==================== TRANSACTIONS ====================
 public function transactions() {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 50;
    $offset = ($page - 1) * $limit;
    $transactions = $this->transactionModel->getAll($limit, $offset);
    $total = $this->transactionModel->count();
    $totalPages = ceil($total / $limit);
    require_once __DIR__ . '/../view/comptable/transactions.php'; // ✅ corrigé
}
    // ==================== RAPPORTS FINANCIERS ====================
   public function rapports() {
    $totalDepots = $this->transactionModel->sumByType('Dépôt');
    $totalRetraits = $this->transactionModel->sumByType('Retrait');
    $totalVirements = $this->transactionModel->sumByType('Virement');
    require_once __DIR__ . '/../view/comptable/rapports.php';
}
}