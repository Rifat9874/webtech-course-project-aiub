<?php
// api/quiz/submit.php — Grade and save quiz answers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

require_once __DIR__ . '/../../models/AttemptModel.php';
require_once __DIR__ . '/../../models/QuestionModel.php';

$attemptModel  = new AttemptModel();
$questionModel = new QuestionModel();

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$attempt_id = isset($data['attempt_id']) ? (int) $data['attempt_id'] : 0;
$answers    = $data['answers'] ?? [];

if ($attempt_id < 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid attempt ID.']);
    exit;
}
if (!is_array($answers)) {
    echo json_encode(['success' => false, 'error' => 'Answers must be an array.']);
    exit;
}

$attempt = $attemptModel->getAttemptById($attempt_id);
if (!$attempt) {
    echo json_encode(['success' => false, 'error' => 'Attempt not found.']);
    exit;
}
if ((int) $attempt['student_id'] !== (int) $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'This attempt does not belong to you.']);
    exit;
}
if ($attempt['completed_at'] !== null) {
    echo json_encode([
        'success' => false,
        'error' => 'This quiz has already been submitted.'
    ]);
    exit;
}

$totalScore = 0;
$quiz_id = (int) $attempt['quiz_id'];

foreach ($answers as $question_id => $option_id) {
    $question_id = (int) $question_id;
    $option_id   = (int) $option_id;

    if ($question_id < 1 || $option_id < 1) {
        continue;
    }

    $validatedAnswer = $attemptModel->getValidatedAnswer($quiz_id, $question_id, $option_id);

    if (!$validatedAnswer) {
        continue;
    }

    $attemptModel->saveAnswer($attempt_id, $question_id, $option_id);

    if ((int) $validatedAnswer['is_correct'] === 1) {
        $totalScore += (int) $validatedAnswer['marks'];
    }
}

$attemptModel->completeAttempt($attempt_id, $totalScore);

echo json_encode([
    'success' => true,
    'attempt_id' => $attempt_id,
    'score' => $totalScore
]);
