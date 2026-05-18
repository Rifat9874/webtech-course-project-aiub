<?php
// views/instructor/home.php
$pageTitle = 'Instructor Dashboard';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-black mb-1">🎓 Instructor Panel — <?= htmlspecialchars($_SESSION['name']) ?></h2>
        <p class="text-muted mb-0">Overview of your quizzes and student activity.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-sm-6">
            <div class="card text-center p-4 h-100" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                <div style="font-size:2.5rem">📝</div>
                <div class="display-5 fw-black text-primary mt-2"><?= (int)($stats['quiz_count'] ?? 0) ?></div>
                <div class="text-muted fw-semibold mt-1">Quizzes Created</div>
                <a href="<?= BASE ?>?page=instructor/quizzes" class="btn btn-primary btn-sm mt-3">Manage Quizzes</a>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card text-center p-4 h-100" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                <div style="font-size:2.5rem">📊</div>
                <div class="display-5 fw-black text-success mt-2"><?= (int)($stats['total_attempts'] ?? 0) ?></div>
                <div class="text-muted fw-semibold mt-1">Total Attempts by Students</div>
                <a href="<?= BASE ?>?page=instructor/analytics" class="btn btn-success btn-sm mt-3">View Analytics</a>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="card p-4">
        <h5 class="fw-bold mb-3">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= BASE ?>?page=instructor/quizzes/create" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Create New Quiz
            </a>
            <a href="<?= BASE ?>?page=instructor/quizzes" class="btn btn-outline-secondary">
                <i class="bi bi-collection me-2"></i>All My Quizzes
            </a>
            <a href="<?= BASE ?>?page=instructor/analytics" class="btn btn-outline-success">
                <i class="bi bi-bar-chart me-2"></i>Student Analytics
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
