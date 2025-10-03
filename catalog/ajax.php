<?php

header('Content-Type: application/json');

require_once '../components/Database.php';
require_once 'repositories/ProductRepository.php';

$db = new Database();
$productRepository = new ProductRepository($db->getConnection());

$action = $_GET['action'] ?? '';
if ($action === 'get_products') {
    $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
    $sort = $_GET['sort'] ?? 'price_asc';

    $products = $productRepository->getAll($categoryId, $sort);

    echo json_encode($products);
}

