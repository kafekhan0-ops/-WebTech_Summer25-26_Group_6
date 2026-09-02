<?php
require_once "header.php";
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
flash('success', 'Product deleted.');
header("Location: products.php");
exit;
?>