<?php
// controllers/QuizController.php
require_once __DIR__ . '/../models/QuizModel.php';
require_once __DIR__ . '/../models/QuestionModel.php';

class QuizController {

    private $quizModel;
    private $questionModel;

    public function __construct() {
        $this->quizModel     = new QuizModel();
        $this->questionModel = new QuestionModel();
    }

    private function requireInstructor() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
            header('Location: ' . BASE . '?page=login');
            exit;
        }
    }

    /**
     *  List all quizzes for this instructor.
     */
    public function listQuizzes() {
        $this->requireInstructor();
        $quizzes = $this->quizModel->getQuizzesByInstructor($_SESSION['user_id']);
        require_once __DIR__ . '/../views/instructor/quiz_list.php';
    }
}
