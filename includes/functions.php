<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /Pharmacy-management/login.php');
        exit;
    }
}

function formatDate($date) {
    return date('d-m-Y', strtotime($date));
}

function formatCurrency($amount) {
    return '&#8377; ' . number_format($amount, 2);
}
