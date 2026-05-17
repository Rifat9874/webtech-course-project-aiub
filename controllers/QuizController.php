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



    /**
     *  Show blank create form.
     */
    public function showCreate() {
        $this->requireInstructor();
        $quiz   = null;
        $errors = [];
        require_once __DIR__ . '/../views/instructor/quiz_form.php';
    }

    /**
     *  Handle create form POST — validates title and time_limit.
     */
    public function createQuiz() {
        $this->requireInstructor();
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $time_limit  = (int) ($_POST['time_limit'] ?? 0);

        $errors = [];
        if ($title === '')    $errors['title']      = 'Title is required.';
        if ($time_limit < 1)  $errors['time_limit'] = 'Time limit must be at least 1 minute.';

        if (!empty($errors)) {
            $quiz = null;
            require_once __DIR__ . '/../views/instructor/quiz_form.php';
            return;
        }

        $this->quizModel->createQuiz($_SESSION['user_id'], $title, $description, $time_limit);
        header('Location: ' . BASE . '?page=instructor/quizzes');
        exit;
    }



/**
     * COMMIT 17: Show edit form pre-filled — only for quiz owner.
     */
    public function showEdit($id) {
        $this->requireInstructor();
        $quiz = $this->quizModel->getQuizById($id);
        if (!$quiz || $quiz['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE . '?page=instructor/quizzes'); exit;
        }
        $errors = [];
        require_once __DIR__ . '/../views/instructor/quiz_form.php';
    }

    /**
     *  Handle edit POST — ownership check before saving.
     */
    public function editQuiz($id) {
        $this->requireInstructor();
        $quiz = $this->quizModel->getQuizById($id);
        if (!$quiz || $quiz['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE . '?page=instructor/quizzes'); exit;
        }
        $title       = trim($_POST['title']       ?? '');
        $description = trim($_POST['description'] ?? '');
        $time_limit  = (int) ($_POST['time_limit'] ?? 0);

        $errors = [];
        if ($title === '')   $errors['title']      = 'Title is required.';
        if ($time_limit < 1) $errors['time_limit'] = 'Time limit must be at least 1 minute.';

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/instructor/quiz_form.php';
            return;
        }
        $this->quizModel->updateQuiz($id, $title, $description, $time_limit);
        header('Location: ' . BASE . '?page=instructor/quizzes');
        exit;
    }


/**
     *  Delete quiz — ownership check first.
     */
    public function deleteQuiz($id) {
        $this->requireInstructor();
        $quiz = $this->quizModel->getQuizById($id);
        if (!$quiz || $quiz['instructor_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE . '?page=instructor/quizzes'); exit;
        }
        $this->quizModel->deleteQuiz($id);
        header('Location: ' . BASE . '?page=instructor/quizzes');
        exit;
    }


}
