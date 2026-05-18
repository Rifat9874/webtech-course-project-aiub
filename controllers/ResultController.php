<?php
// controllers/ResultController.php
// session_start() NOT called here — already in public/index.php

require_once __DIR__ . '/../models/ResultModel.php';

class ResultController {

    private $model;

    public function __construct() {
        $this->model = new ResultModel();
    }

    // Post-quiz result page
    public function showResult($attempt_id) {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE . '?page=login');
            exit;
        }
        $result = $this->model->getAttemptResult($attempt_id);
        if (!$result) die('Result not found.');

        $breakdown  = $this->model->getAnswerBreakdown($attempt_id);
        $percentage = $result['total_marks'] > 0
            ? ($result['score'] / $result['total_marks']) * 100
            : 0;

        require __DIR__ . '/../views/results/result.php';
    }

    // Student attempt history
    public function myResults() {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: ' . BASE . '?page=login');
            exit;
        }
        $attempts = $this->model->getMyAttempts($_SESSION['user_id']);
        require __DIR__ . '/../views/results/my_results.php';
    }

    // Instructor analytics
    public function analytics() {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
            header('Location: ' . BASE . '?page=login');
            exit;
        }
        $quizzes        = $this->model->getInstructorQuizzes($_SESSION['user_id']);
        $attempts       = [];
        $stats          = null;
        $selectedQuizId = null;

        if (!empty($_GET['quiz_id'])) {
            $selectedQuizId = (int) $_GET['quiz_id'];
            $attempts       = $this->model->getQuizAttempts($selectedQuizId);
            $stats          = $this->model->getQuizStats($selectedQuizId);
        }
        require __DIR__ . '/../views/results/analytics.php';
    }
}
