<?php
// api/users/toggle.php
// AJAX endpoint — receives a POST request with JSON body { "user_id": 5 }
// Flips is_active for that user and returns JSON { "success": true, "is_active": 1 }
//
// Called by the JavaScript in views/admin/panel.php.

// Start session so we can check that the caller is a logged-in admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Always send JSON back, no matter what happens
header('Content-Type: application/json');

// ── Security check ──
// Only admins should be able to call this endpoint
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403); // 403 = Forbidden
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// ── Only accept POST requests ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 = Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'POST only.']);
    exit;
}

// ── Read and decode the JSON request body ──
// file_get_contents('php://input') reads the raw POST body
$body = file_get_contents('php://input');
$data = json_decode($body, true); // true → returns an associative array

// Make sure user_id is present and is a number
if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
    http_response_code(400); // 400 = Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid user_id.']);
    exit;
}

$userId = (int)$data['user_id'];

// ── Load dependencies ──
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/UserModel.php';

$db        = getDB();
$userModel = new UserModel($db);

// toggleActive() flips is_active and returns the NEW value (0 or 1)
$newStatus = $userModel->toggleActive($userId);

// ── Send the result back as JSON ──
echo json_encode([
    'success'   => true,
    'is_active' => (int)$newStatus,
]);
exit;
