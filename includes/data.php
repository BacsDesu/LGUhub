<?php
/**
 * Current page/message resolution + all data-fetching queries used by the views.
 */

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Get message
$message = '';
$messageType = '';
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = isset($_GET['type']) ? $_GET['type'] : 'success';
}

// Fetch data
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStock = $pdo->query("SELECT SUM(quantity) FROM inventory")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

$recentTransactions = $pdo->query("
    SELECT it.*, p.product_name 
    FROM inventory_transactions it 
    JOIN products p ON it.product_id = p.product_id 
    ORDER BY transaction_date DESC LIMIT 5
")->fetchAll();

$products = $pdo->query("
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    ORDER BY p.product_id
")->fetchAll();

$inventory = $pdo->query("
    SELECT i.*, p.product_name 
    FROM inventory i 
    JOIN products p ON i.product_id = p.product_id 
    ORDER BY p.product_name
")->fetchAll();

$transactions = $pdo->query("
    SELECT it.*, p.product_name 
    FROM inventory_transactions it 
    JOIN products p ON it.product_id = p.product_id 
    ORDER BY transaction_date DESC LIMIT 50
")->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();

$roles = $pdo->query("SELECT * FROM roles ORDER BY role_id")->fetchAll();

$users = $pdo->query("
    SELECT u.*, r.role_name 
    FROM users u 
    LEFT JOIN roles r ON u.role_id = r.role_id 
    ORDER BY u.user_id
")->fetchAll();
