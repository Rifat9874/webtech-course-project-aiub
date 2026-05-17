<?php
// api/questions/delete.php — DELETE: remove a question and its options
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['success'=>false,'error'=>'Method not allowed. Use DELETE.']); exit;
}