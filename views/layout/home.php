<?php
// views/layout/home.php — Public homepage
$pageTitle = 'Welcome';
require_once __DIR__ . '/header.php';
?>
<div class="container py-5">
    <div class="text-center py-5 mb-5" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:24px;color:#fff;padding:4rem 2rem !important;">
        <div class="mb-3" style="font-size:4rem;">🎓</div>
        <h1 class="display-4 fw-black mb-3">Welcome to QuizApp</h1>
        <p class="fs-5 opacity-75 mb-4 mx-auto" style="max-width:560px;">A full-featured online quiz platform — instructors create, students take, everyone tracks results.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE ?>?page=register" class="btn btn-light btn-lg me-2 px-4 fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Get Started Free</a>
            <a href="<?= BASE ?>?page=login" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
        <?php else: ?>
            <?php $dest=['student'=>'student/home','instructor'=>'instructor/home','admin'=>'admin/panel']; $url=BASE.'?page='.($dest[$_SESSION['role']]??'login'); ?>
            <a href="<?= $url ?>" class="btn btn-light btn-lg px-4 fw-bold"><i class="bi bi-grid-fill me-2"></i>Go to Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4"><div class="card h-100 text-center p-4"><div style="font-size:3rem" class="mb-3">📝</div><h5 class="fw-bold">Take Quizzes</h5><p class="text-muted small">Browse published quizzes, answer within the time limit, and get your score instantly.</p></div></div>
        <div class="col-md-4"><div class="card h-100 text-center p-4"><div style="font-size:3rem" class="mb-3">🏗️</div><h5 class="fw-bold">Create Quizzes</h5><p class="text-muted small">Instructors build MCQ quizzes with 4 choices per question, set time limits, and publish when ready.</p></div></div>
        <div class="col-md-4"><div class="card h-100 text-center p-4"><div style="font-size:3rem" class="mb-3">🏆</div><h5 class="fw-bold">Leaderboard & Analytics</h5><p class="text-muted small">Top 10 students ranked by cumulative score. Instructors see class averages, pass rates, and more.</p></div></div>
    </div>
</div>

<div class="container py-5">
    <!-- HERO -->
    <div class="text-center py-5 mb-5" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:24px;color:#fff;padding:4rem 2rem !important;">
        <div class="mb-3" style="font-size:4rem;">🎓</div>
        <h1 class="display-4 fw-black mb-3">Welcome to QuizApp</h1>
        <p class="fs-5 opacity-75 mb-4 mx-auto" style="max-width:560px;">
            A full-featured online quiz platform — instructors create, students take, everyone tracks results.
        </p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE ?>?page=register" class="btn btn-light btn-lg me-2 px-4 fw-bold">
                <i class="bi bi-person-plus-fill me-2"></i>Get Started Free
            </a>
            <a href="<?= BASE ?>?page=login" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
        <?php else: ?>
            <?php
            $dest = ['student' => 'student/home', 'instructor' => 'instructor/home', 'admin' => 'admin/panel'];
            $url  = BASE . '?page=' . ($dest[$_SESSION['role']] ?? 'login');
            ?>
            <a href="<?= $url ?>" class="btn btn-light btn-lg px-4 fw-bold">
                <i class="bi bi-grid-fill me-2"></i>Go to Dashboard
            </a>
        <?php endif; ?>
    </div>

    <!-- FEATURE CARDS -->
    <?php if (!$isLoggedIn || $loggedInRole === 'instructor'): ?>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div style="font-size:3rem" class="mb-3">📝</div>
                    <h5 class="fw-bold">Take Quizzes</h5>
                    <p class="text-muted small">Browse published quizzes, answer within the time limit, and get your score instantly.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div style="font-size:3rem" class="mb-3">🏗️</div>
                    <h5 class="fw-bold">Create Quizzes</h5>
                    <p class="text-muted small">Instructors build MCQ quizzes with 4 choices per question, set time limits, and publish when ready.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 text-center p-4">
                    <div style="font-size:3rem" class="mb-3">🏆</div>
                    <h5 class="fw-bold">Leaderboard & Analytics</h5>
                    <p class="text-muted small">Top 10 students ranked by cumulative score. Instructors see class averages, pass rates, and more.</p>
                </div>
            </div>
        </div>
</div>
<?php endif; ?>

</div>
<?php require_once __DIR__ . '/footer.php'; ?>


</div>

<?php require_once __DIR__ . '/footer.php'; ?>