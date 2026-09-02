<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cartCount() {
    $count = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $count += (int)$qty;
        }
    }
    return $count;
}

function cartTotal($conn) {
    $total = 0;
    if (empty($_SESSION['cart'])) return $total;

    $ids = array_keys($_SESSION['cart']);
    $ids = array_map('intval', $ids);
    if (!$ids) return 0;

    $idList = implode(',', $ids);
    $result = $conn->query("SELECT id, price FROM products WHERE id IN ($idList)");
    while ($row = $result->fetch_assoc()) {
        $total += (float)$row['price'] * (int)$_SESSION['cart'][$row['id']];
    }
    return $total;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php?redirect=checkout");
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit;
    }
}

function flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
    if (!empty($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return "";
}
?>