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

require_once __DIR__ . '/../../models/QuestionModel.php';
require_once __DIR__ . '/../../models/QuizModel.php';
$questionModel = new QuestionModel();
$quizModel     = new QuizModel();

$raw         = file_get_contents('php://input');
$data        = json_decode($raw, true);
$question_id = isset($data['question_id']) ? (int)$data['question_id'] : 0;

if ($question_id < 1) { echo json_encode(['success'=>false,'error'=>'Invalid question ID.']); exit; }

$question = $questionModel->getQuestionById($question_id);
if (!$question) { echo json_encode(['success'=>false,'error'=>'Question not found.']); exit; }

$quiz = $quizModel->getQuizById($question['quiz_id']);
if (!$quiz || (int)$quiz['instructor_id'] !== (int)$_SESSION['user_id']) {
    echo json_encode(['success'=>false,'error'=>'You do not own this question.']); exit;
}

$questionModel->deleteQuestion($question_id);
$quizModel->updateTotalMarks($question['quiz_id']);

echo json_encode(['success' => true]);