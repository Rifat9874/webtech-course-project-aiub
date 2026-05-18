<?php
// views/student/home.php
$pageTitle = 'Student Dashboard';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">

    <!-- Welcome -->
    <div class="mb-4">
        <h2 class="fw-black mb-1">👋 Hello, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
        <p class="text-muted mb-0">Here's your learning overview.</p>
    </div>

    <!-- Stat cards -->
    <div class="row g-4 mb-5">
        <div class="col-sm-4">
            <div class="card text-center p-4 h-100" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                <div style="font-size:2.5rem">📚</div>
                <div class="display-6 fw-black text-primary mt-2"><?= (int)($stats['available_quizzes'] ?? 0) ?></div>
                <div class="text-muted fw-semibold mt-1">Quizzes Available</div>
                <a href="<?= BASE ?>?page=student/quizzes" class="btn btn-primary btn-sm mt-3">Browse</a>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center p-4 h-100" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                <div style="font-size:2.5rem">✅</div>
                <div class="display-6 fw-black text-success mt-2"><?= (int)($stats['attempts_taken'] ?? 0) ?></div>
                <div class="text-muted fw-semibold mt-1">Quizzes Taken</div>
                <a href="<?= BASE ?>?page=student/my-results" class="btn btn-success btn-sm mt-3">My Results</a>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center p-4 h-100" style="background:linear-gradient(135deg,#fefce8,#fef9c3);">
                <div style="font-size:2.5rem">⭐</div>
                <div class="display-6 fw-black text-warning mt-2"><?= (int)($stats['total_score'] ?? 0) ?></div>
                <div class="text-muted fw-semibold mt-1">Total Score Earned</div>
                <a href="<?= BASE ?>?page=leaderboard" class="btn btn-warning btn-sm mt-3">Leaderboard</a>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="card p-4">
        <h5 class="fw-bold mb-3">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= BASE ?>?page=student/quizzes" class="btn btn-primary">
                <i class="bi bi-play-circle-fill me-2"></i>Start a Quiz
            </a>
            <a href="<?= BASE ?>?page=student/my-results" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-2"></i>View All My Results
            </a>
            <a href="<?= BASE ?>?page=leaderboard" class="btn btn-outline-warning">
                <i class="bi bi-trophy me-2"></i>View Leaderboard
            </a>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
