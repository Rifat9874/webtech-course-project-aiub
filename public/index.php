<?php
// ============================================================
// public/index.php  —  MAIN ROUTER
// ============================================================

session_start();

// ── Base URL — AUTO-DETECTED so it works on any XAMPP setup ──
// This detects the path automatically. No manual editing needed!
$scriptDir = dirname(dirname($_SERVER['SCRIPT_NAME']));
define('BASE', rtrim($scriptDir, '/') . '/public/index.php');

// ── Load database config ──
require_once __DIR__ . '/../config/database.php';

// ── Load all Models ──
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/QuizModel.php';
require_once __DIR__ . '/../models/QuestionModel.php';
require_once __DIR__ . '/../models/AttemptModel.php';
require_once __DIR__ . '/../models/ResultModel.php';

// ── Load all Controllers ──
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/QuizController.php';
require_once __DIR__ . '/../controllers/StudentController.php';
require_once __DIR__ . '/../controllers/ResultController.php';

// ── Read current page and request method ──
$page   = $_GET['page'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'];

// ─────────────────────────────────────────────────────────────
// HELPER: requireLogin()
// ─────────────────────────────────────────────────────────────
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE . '?page=login');
        exit;
    }
}

// ─────────────────────────────────────────────────────────────
// HELPER: requireRole($role)
// ─────────────────────────────────────────────────────────────
function requireRole(string $role) {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        if (isset($_SESSION['role'])) {
            $dest = [
                'student'    => 'student/home',
                'instructor' => 'instructor/home',
                'admin'      => 'admin/panel',
            ];
            header('Location: ' . BASE . '?page=' . ($dest[$_SESSION['role']] ?? 'login'));
            exit;
        }
        header('Location: ' . BASE . '?page=login');
        exit;
    }
}

// =============================================================
// HOMEPAGE
// =============================================================
if ($page === 'home' || $page === '') {
    require_once __DIR__ . '/../views/layout/home.php';

// =============================================================
// AUTH ROUTES
// =============================================================
} elseif ($page === 'register') {
    $auth = new AuthController();
    ($method === 'POST') ? $auth->register() : $auth->showRegister();

} elseif ($page === 'login') {
    $auth = new AuthController();
    ($method === 'POST') ? $auth->login() : $auth->showLogin();

} elseif ($page === 'logout') {
    $auth = new AuthController();
    $auth->logout();

// =============================================================
// STUDENT ROUTES
// =============================================================
} elseif ($page === 'student/home') {
    requireRole('student');
    $db        = getDB();
    $userModel = new UserModel($db);
    $stats     = $userModel->getStudentStats($_SESSION['user_id']);
    require_once __DIR__ . '/../views/student/home.php';

} elseif ($page === 'student/quizzes') {
    requireRole('student');
    $studentController = new StudentController();
    $studentController->listQuizzes();

} elseif ($page === 'student/quiz/start') {
    requireRole('student');
    $quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;
    if ($quiz_id < 1) {
        header('Location: ' . BASE . '?page=student/quizzes');
        exit;
    }
    $studentController = new StudentController();
    $studentController->startQuiz($quiz_id);

} elseif ($page === 'student/my-results') {
    requireRole('student');
    $resultController = new ResultController();
    $resultController->myResults();

// =============================================================
// INSTRUCTOR ROUTES
// =============================================================
} elseif ($page === 'instructor/home') {
    requireRole('instructor');
    $db        = getDB();
    $userModel = new UserModel($db);
    $stats     = $userModel->getInstructorStats($_SESSION['user_id']);
    require_once __DIR__ . '/../views/instructor/home.php';

} elseif ($page === 'instructor/quizzes') {
    requireRole('instructor');
    $quizController = new QuizController();
    $quizController->listQuizzes();

} elseif ($page === 'instructor/quizzes/create') {
    requireRole('instructor');
    $quizController = new QuizController();
    ($method === 'POST') ? $quizController->createQuiz() : $quizController->showCreate();

} elseif ($page === 'instructor/quizzes/edit') {
    requireRole('instructor');
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $quizController = new QuizController();
    ($method === 'POST') ? $quizController->editQuiz($id) : $quizController->showEdit($id);

} elseif ($page === 'instructor/quizzes/delete') {
    requireRole('instructor');
    if ($method !== 'POST') {
        header('Location: ' . BASE . '?page=instructor/quizzes');
        exit;
    }
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $quizController = new QuizController();
    $quizController->deleteQuiz($id);

} elseif ($page === 'instructor/questions') {
    requireRole('instructor');
    $quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;
    $quizController = new QuizController();
    $quizController->showQuestions($quiz_id);

} elseif ($page === 'instructor/questions/add') {
    requireRole('instructor');
    if ($method !== 'POST') {
        header('Location: ' . BASE . '?page=instructor/quizzes');
        exit;
    }
    $quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;
    $quizController = new QuizController();
    $quizController->addQuestion($quiz_id);

} elseif ($page === 'instructor/analytics') {
    requireRole('instructor');
    $resultController = new ResultController();
    $resultController->analytics();

// =============================================================
// ADMIN ROUTES
// =============================================================
} elseif ($page === 'admin/panel') {
    requireRole('admin');
    $db        = getDB();
    $userModel = new UserModel($db);
    $users     = $userModel->getAllUsers();
    require_once __DIR__ . '/../views/admin/panel.php';

// =============================================================
// RESULTS & LEADERBOARD
// =============================================================
} elseif ($page === 'result') {
    requireLogin();
    $attempt_id = isset($_GET['attempt_id']) ? (int) $_GET['attempt_id'] : 0;
    $resultController = new ResultController();
    $resultController->showResult($attempt_id);

} elseif ($page === 'leaderboard') {
    $model   = new ResultModel();
    $leaders = $model->getLeaderboard();
    require_once __DIR__ . '/../views/results/leaderboard.php';

// =============================================================
// 404
// =============================================================
} else {
    http_response_code(404);
    require_once __DIR__ . '/../views/layout/404.php';
}
