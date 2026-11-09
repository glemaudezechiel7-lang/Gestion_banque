<?php
// config/session.php

// ⚠️ IMPORTANT : Ces réglages doivent être faits AVANT de démarrer la session
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['client_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Administrateur';
}

function isComptable() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Comptable';
}

function isCaissier() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Caissier';
}

function isClient() {
    return isset($_SESSION['client_id']);
}

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        return (object) [
            'id' => $_SESSION['user_id'],
            'nom' => $_SESSION['nom'],
            'prenom' => $_SESSION['prenom'],
            'role' => $_SESSION['role']
        ];
    } elseif (isset($_SESSION['client_id'])) {
        return (object) [
            'id' => $_SESSION['client_id'],
            'nom' => $_SESSION['nom'],
            'prenom' => $_SESSION['prenom'],
            'role' => 'Client'
        ];
    }
    return null;
}

function logout() {
    $_SESSION = [];
    session_destroy();
}