<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/security.php';

$route = $_GET['route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$route = trim($route, '/');

// ==================== MAP POUR LES ACTIONS AVEC TIRETS ====================
$actionMap = [
    // Client
    'demander-carte'   => 'demanderCarte',
    'bloquer-carte'    => 'bloquerCarte',
    'debloquer-carte'  => 'debloquerCarte',
    'changer-pin'      => 'changerPin',
    'marquer-lu'       => 'marquerLu',
    'marquer-tout-lu'  => 'marquerToutLu',
    // Admin
    'demandes-cartes'  => 'demandesCartes',
    'approuver-carte'  => 'approuverCarte',
    'rejeter-carte'    => 'rejeterCarte',
];

// Fonction pour récupérer le nom de méthode à appeler
function getMethodName($action) {
    global $actionMap;
    return $actionMap[$action] ?? $action;
}

switch ($route) {
    // ==================== ROUTES PUBLIQUES ====================
    case '':
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $ctrl = new AuthController();
        if ($method === 'POST') {
            $ctrl->login();
        } else {
            if (method_exists($ctrl, 'loginForm')) {
                $ctrl->loginForm();
            } else {
                $ctrl->login();
            }
        }
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $ctrl = new AuthController();
        $ctrl->register();
        break;

    // ==================== ROUTES CLIENT ====================
    case 'client/dashboard':
    case 'client/depot':
    case 'client/retrait':
    case 'client/virement':
    case 'client/cartes':
    case 'client/demander-carte':
    case 'client/bloquer-carte':
    case 'client/debloquer-carte':
    case 'client/changer-pin':
    case 'client/notifications':
    case 'client/marquer-lu':
    case 'client/marquer-tout-lu':
        if (!isClient()) {
            header('Location: index.php?route=login');
            exit;
        }
        require_once __DIR__ . '/controllers/ClientController.php';
        $ctrl = new ClientController();
        $action = basename($route);
        $methodName = getMethodName($action);
        if (method_exists($ctrl, $methodName)) {
            $ctrl->$methodName();
        } else {
            http_response_code(404);
            echo "Action non trouvée.";
        }
        break;

    // ==================== ROUTES ADMIN ====================
    case 'admin/dashboard':
    case 'admin/clients':
    case 'admin/comptes':
    case 'admin/transactions':
    case 'admin/utilisateurs':
    case 'admin/client/details':
    case 'admin/client/toggle':
    case 'admin/utilisateur/edit':
    case 'admin/utilisateur/toggle':
    case 'admin/demandes-cartes':
    case 'admin/approuver-carte':
    case 'admin/rejeter-carte':
        if (!isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
        require_once __DIR__ . '/controllers/AdminController.php';
        $ctrl = new AdminController();
        $action = basename($route);
        $methodName = getMethodName($action);
        if (method_exists($ctrl, $methodName)) {
            $ctrl->$methodName();
        } else {
            http_response_code(404);
            echo "Action non trouvée.";
        }
        break;

    // ==================== ROUTES COMPTABLE ====================
    case 'comptable/dashboard':
    case 'comptable/transactions':
    case 'comptable/rapports':
        if (!isComptable()) {
            header('Location: index.php?route=login');
            exit;
        }
        require_once __DIR__ . '/controllers/ComptableController.php';
        $ctrl = new ComptableController();
        $action = basename($route);
        if (method_exists($ctrl, $action)) {
            $ctrl->$action();
        } else {
            http_response_code(404);
            echo "Action non trouvée.";
        }
        break;

    // ==================== ROUTES CAISSIER ====================
    case 'caissier/dashboard':
    case 'caissier/history':
        if (!isCaissier()) {
            header('Location: index.php?route=login');
            exit;
        }
        require_once __DIR__ . '/controllers/CaissierController.php';
        $ctrl = new CaissierController();
        $action = basename($route);
        if (method_exists($ctrl, $action)) {
            $ctrl->$action();
        } else {
            http_response_code(404);
            echo "Action non trouvée.";
        }
        break;

    // ==================== 404 ====================
    default:
        http_response_code(404);
        $errorFile = __DIR__ . '/view/errors/404.php';
        if (file_exists($errorFile)) {
            require_once $errorFile;
        } else {
            echo "<!DOCTYPE html>
            <html><head><title>404</title>
            <style>body{background:#12121c;color:#fff;text-align:center;padding-top:50px;font-family:sans-serif;}
            h1{font-size:6rem;color:#64d8ff;}</style></head>
            <body><h1>404</h1><p>Page non trouvée.</p>
            <a href='index.php?route=' style='color:#64d8ff;'>Accueil</a></body></html>";
        }
        break;
}
?>