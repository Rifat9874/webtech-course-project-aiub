<?php
// api/quizzes/toggle.php — POST: flip quiz draft/published
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']); exit;
}

require_once __DIR__ . '/../../models/QuizModel.php';
require_once __DIR__ . '/../../models/QuestionModel.php';
$quizModel = new QuizModel();

$raw     = file_get_contents('php://input');
$data    = json_decode($raw, true);
$quiz_id = isset($data['quiz_id']) ? (int) $data['quiz_id'] : 0;

if ($quiz_id < 1) { echo json_encode(['success'=>false,'error'=>'Invalid quiz ID.']); exit; }

$quiz = $quizModel->getQuizById($quiz_id);
if (!$quiz) { echo json_encode(['success'=>false,'error'=>'Quiz not found.']); exit; }
if ((int)$quiz['instructor_id'] !== (int)$_SESSION['user_id']) {
    echo json_encode(['success'=>false,'error'=>'You do not own this quiz.']); exit;
}

