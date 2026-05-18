<?php
// controllers/StudentController.php
// session_start() NOT called here — already in public/index.php

require_once __DIR__ . '/../models/AttemptModel.php';
require_once __DIR__ . '/../models/QuizModel.php';
require_once __DIR__ . '/../models/QuestionModel.php';

class StudentController
{

    private $attemptModel;
    private $quizModel;
    private $questionModel;

    public function __construct()
    {
        $this->attemptModel  = new AttemptModel();
        $this->quizModel     = new QuizModel();
        $this->questionModel = new QuestionModel();
    }

    // Student dashboard
    // URL: ?page=student/home
    public function home()
    {
        $this->requireStudent();

        $student_id = $_SESSION['user_id'];

        // Published quizzes count
        $available_quizzes = $this->quizModel->countPublishedQuizzes();

        // Attempts taken (completed only)
        $attempts_taken = $this->attemptModel->countCompletedAttempts($student_id);

        // Total score earned
        $total_score = $this->attemptModel->getTotalScore($student_id);

        $stats = [
            'available_quizzes' => $available_quizzes,
            'attempts_taken'    => $attempts_taken,
            'total_score'       => $total_score,
        ];

        require_once __DIR__ . '/../views/student/home.php';
    }

    private function requireStudent()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
            header('Location: ' . BASE . '?page=login');
            exit;
        }
    }

    // Show all published quizzes
    // URL: ?page=student/quizzes
    public function listQuizzes()
    {
        $this->requireStudent();

        $quizzes = $this->attemptModel->getPublishedQuizzes();

        foreach ($quizzes as &$quiz) {
            $attempt = $this->attemptModel->getStudentAttempt(
                $_SESSION['user_id'],
                $quiz['id']
            );
            if ($attempt && $attempt['completed_at'] !== null) {
                $quiz['attempted'] = true;
                $quiz['score']     = $attempt['score'];
            } else {
                $quiz['attempted'] = false;
                $quiz['score']     = null;
            }
        }
        unset($quiz);

        require_once __DIR__ . '/../views/student/quiz_list.php';
    }

    // Start a quiz
    // URL: ?page=student/quiz/start&quiz_id=X
    public function startQuiz($quiz_id)
    {
        $this->requireStudent();

        $quiz = $this->quizModel->getQuizById($quiz_id);

        if (!$quiz || $quiz['status'] !== 'published') {
            header('Location: ' . BASE . '?page=student/quizzes');
            exit;
        }

        // Block re-attempt at PHP level
        if ($this->attemptModel->hasCompletedAttempt($_SESSION['user_id'], $quiz_id)) {
            header('Location: ' . BASE . '?page=student/quizzes');
            exit;
        }

        // Reuse in-progress attempt if exists
        $existing = $this->attemptModel->getStudentAttempt($_SESSION['user_id'], $quiz_id);

        if ($existing && $existing['completed_at'] === null) {
            $attempt_id = $existing['id'];
        } else {
            $attempt_id = $this->attemptModel->createAttempt($quiz_id, $_SESSION['user_id']);
        }

        $questions = $this->questionModel->getQuestionsByQuiz($quiz_id);

        require_once __DIR__ . '/../views/student/take_quiz.php';
    }
}
