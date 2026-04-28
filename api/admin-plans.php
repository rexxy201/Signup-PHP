<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
requireAdminLogin();

$method = $_SERVER['REQUEST_METHOD'];
$db = getDb();

if ($method === 'GET') {
    $plans = $db->query("SELECT * FROM plans ORDER BY location_zone, sort_order, id")->fetchAll();
    jsonResponse($plans);
}

if ($method === 'POST') {
    $data = getPostBody();
    $name     = trim($data['name'] ?? '');
    $price    = trim($data['price'] ?? '');
    $speed    = trim($data['speed'] ?? '');
    $category = trim($data['category'] ?? 'Residential');
    $zone     = trim($data['location_zone'] ?? 'default');
    $active   = isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1;
    $order    = (int)($data['sort_order'] ?? 0);

    if (!$name || !$price || !$speed) jsonError('name, price and speed are required');

    $stmt = $db->prepare("INSERT INTO plans (name,price,speed,category,location_zone,is_active,sort_order) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$name,$price,$speed,$category,$zone,$active,$order]);
    $id = $db->lastInsertId();
    $row = $db->query("SELECT * FROM plans WHERE id = $id")->fetch();
    jsonResponse($row, 201);
}

if ($method === 'PUT') {
    $id   = (int)($_GET['id'] ?? 0);
    $data = getPostBody();
    if (!$id) jsonError('id is required');

    $name     = trim($data['name'] ?? '');
    $price    = trim($data['price'] ?? '');
    $speed    = trim($data['speed'] ?? '');
    $category = trim($data['category'] ?? 'Residential');
    $zone     = trim($data['location_zone'] ?? 'default');
    $active   = isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1;
    $order    = (int)($data['sort_order'] ?? 0);

    if (!$name || !$price || !$speed) jsonError('name, price and speed are required');

    $stmt = $db->prepare("UPDATE plans SET name=?,price=?,speed=?,category=?,location_zone=?,is_active=?,sort_order=? WHERE id=?");
    $stmt->execute([$name,$price,$speed,$category,$zone,$active,$order,$id]);
    $row = $db->query("SELECT * FROM plans WHERE id = $id")->fetch();
    jsonResponse($row ?: []);
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('id is required');
    $db->prepare("DELETE FROM plans WHERE id=?")->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
