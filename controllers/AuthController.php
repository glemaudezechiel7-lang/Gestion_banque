<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ClientModel.php';
require_once __DIR__ . '/../models/CompteModel.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/security.php';

class AuthController {
    private $userModel;
    private $clientModel;
    private $compteModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->clientModel = new ClientModel();
        $this->compteModel = new CompteModel();
    }

    public function loginForm() {
        if (isLoggedIn()) {
            $this->redirectByRole();
            exit;
        }
        require_once __DIR__ . '/../view/auth/login.php';
    }

    public function login() {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token CSRF invalide.';
            header('Location: index.php?route=login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            header('Location: index.php?route=login');
            exit;
        }

        $user = $this->userModel->authenticate($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['nom'] = $user->nom;
            $_SESSION['prenom'] = $user->prenom;
            $_SESSION['role'] = $user->role_nom;
            session_regenerate_id(true);
            $this->redirectByRole($user->role_nom);
            exit;
        }

        $client = $this->clientModel->authenticate($email, $password);
        if ($client) {
            $_SESSION['client_id'] = $client->id;
            $_SESSION['nom'] = $client->nom;
            $_SESSION['prenom'] = $client->prenom;
            $_SESSION['role'] = 'Client';
            session_regenerate_id(true);
            header('Location: index.php?route=client/dashboard');
            exit;
        }

        $_SESSION['error'] = 'Email ou mot de passe incorrect.';
        header('Location: index.php?route=login');
        exit;
    }

    public function logout() {
        logout();
        header('Location: index.php?route=login');
        exit;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Token CSRF invalide.';
                header('Location: index.php?route=register');
                exit;
            }

            $errors = [];
            if (!validateEmail($_POST['email'] ?? '')) $errors[] = 'Email invalide.';
            if (strlen($_POST['code'] ?? '') < 6) $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';
            if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['telephone']) || empty($_POST['adresse']) || empty($_POST['pays']) || empty($_POST['date_naissance'])) {
                $errors[] = 'Tous les champs sont obligatoires.';
            }
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                header('Location: index.php?route=register');
                exit;
            }

            $data = [
                'nom' => trim($_POST['nom']),
                'prenom' => trim($_POST['prenom']),
                'email' => trim($_POST['email']),
                'telephone' => trim($_POST['telephone']),
                'adresse' => trim($_POST['adresse']),
                'pays' => trim($_POST['pays']),
                'sexe' => $_POST['sexe'],
                'date_naissance' => $_POST['date_naissance'],
                'code' => $_POST['code'],
                'type_compte' => $_POST['type_compte'] ?? 'Courant'
            ];

            if ($this->clientModel->findByEmail($data['email'])) {
                $_SESSION['error'] = 'Cet email est déjà utilisé.';
                header('Location: index.php?route=register');
                exit;
            }

            $clientId = $this->clientModel->create($data);
            if ($clientId) {
                $this->compteModel->creerCompte($clientId, $data['type_compte']);
                $_SESSION['success'] = 'Compte créé avec succès ! Connectez-vous.';
                header('Location: index.php?route=login');
                exit;
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'inscription.';
                header('Location: index.php?route=register');
                exit;
            }
        }

        require_once __DIR__ . '/../view/auth/register.php';
    }

    private function redirectByRole($role = null) {
        if (!$role) $role = $_SESSION['role'] ?? 'Client';
        switch ($role) {
            case 'Administrateur': header('Location: index.php?route=admin/dashboard'); break;
            case 'Comptable': header('Location: index.php?route=comptable/dashboard'); break;
            case 'Caissier': header('Location: index.php?route=caissier/dashboard'); break;
            default: header('Location: index.php?route=client/dashboard'); break;
        }
        exit;
    }
}
?>