<?php
// views/student/quiz_list.php
$pageTitle = 'Browse Quizzes';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black mb-1">📚 Available Quizzes</h2>
            <p class="text-muted mb-0">Choose a quiz below to get started.</p>
        </div>
    </div>

    <?php if (empty($quizzes)): ?>
        <div class="card text-center p-5">
            <div style="font-size:4rem">😴</div>
            <h4 class="mt-3 fw-bold">No quizzes available yet</h4>
            <p class="text-muted">Check back soon — instructors are preparing quizzes for you!</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($quizzes as $quiz): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 <?= $quiz['attempted'] ? 'opacity-75' : '' ?>">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div style="font-size:2rem">📋</div>
                                <?php if ($quiz['attempted']): ?>
                                    <span class="badge bg-success">✓ Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Available</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="fw-bold mb-2"><?= htmlspecialchars($quiz['title']) ?></h5>
                            <?php if ($quiz['description']): ?>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($quiz['description']) ?></p>
                            <?php endif; ?>
                            <div class="d-flex gap-3 mb-3 small text-muted">
                                <span><i class="bi bi-clock me-1"></i><?= (int)$quiz['time_limit_minutes'] ?> min</span>
                                <span><i class="bi bi-star me-1"></i><?= (int)$quiz['total_marks'] ?> marks</span>
                            </div>
                            <div class="mt-auto">
                                <?php if ($quiz['attempted']): ?>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted small">Your score:</span>
                                        <span class="fw-bold text-success fs-5"><?= (int)$quiz['score'] ?> / <?= (int)$quiz['total_marks'] ?></span>
                                    </div>
                                    <div class="btn btn-outline-secondary w-100 mt-2 disabled" style="cursor:not-allowed;">
                                        <i class="bi bi-lock me-1"></i>Already Attempted
                                    </div>
                                <?php else: ?>
                                    <a href="<?= BASE ?>?page=student/quiz/start&quiz_id=<?= $quiz['id'] ?>"
                                        class="btn btn-primary w-100">
                                        <i class="bi bi-play-fill me-2"></i>Start Quiz
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>