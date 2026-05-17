<?php
// api/questions/update.php — PATCH: update question text and options
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    echo json_encode(['success'=>false,'error'=>'Unauthorized.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    echo json_encode(['success'=>false,'error'=>'Method not allowed. Use PATCH.']); exit;
}

$raw               = file_get_contents('php://input');
$data              = json_decode($raw, true);
$question_id       = isset($data['question_id'])        ? (int) $data['question_id']     : 0;
$question_text     = isset($data['question_text'])       ? trim($data['question_text'])   : '';
$options           = $data['options']                    ?? [];
$correct_option_id = isset($data['correct_option_id'])  ? (int)$data['correct_option_id'] : 0;

if ($question_id  < 1)    { echo json_encode(['success'=>false,'error'=>'Invalid question ID.']);          exit; }
if ($question_text === '') { echo json_encode(['success'=>false,'error'=>'Question text cannot be empty.']); exit; }
if (count($options) !== 4) { echo json_encode(['success'=>false,'error'=>'Exactly 4 options required.']);  exit; }
foreach ($options as $opt) {
    if (empty(trim($opt['text'] ?? ''))) {
        echo json_encode(['success'=>false,'error'=>'All options must have text.']); exit;
    }
}
if ($correct_option_id < 1) { echo json_encode(['success'=>false,'error'=>'A correct option must be selected.']); exit; }

require_once __DIR__ . '/../../models/QuestionModel.php';
require_once __DIR__ . '/../../models/QuizModel.php';
$questionModel = new QuestionModel();
$quizModel     = new QuizModel();

$question = $questionModel->getQuestionById($question_id);
if (!$question) {
    echo json_encode(['success'=>false,'error'=>'Question not found.']); exit;
}

$quiz = $quizModel->getQuizById($question['quiz_id']);
if (!$quiz || (int)$quiz['instructor_id'] !== (int)$_SESSION['user_id']) {
    echo json_encode(['success'=>false,'error'=>'You do not own this question.']); exit;
}
$questionModel->updateQuestion($question_id, $question_text);
$questionModel->updateOptions($question_id, $options, $correct_option_id);
$quizModel->updateTotalMarks($question['quiz_id']);

echo json_encode(['success' => true]);