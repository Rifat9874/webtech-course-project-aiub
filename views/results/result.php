<?php
// views/results/result.php — Post-attempt result page
$pageTitle = 'Quiz Result';
require_once __DIR__ . '/../layout/header.php';

$passed = $percentage >= 60;
?>

<div class="container py-4" style="max-width:800px;">

    <!-- Score banner -->
    <div class="card text-center mb-4" style="background:linear-gradient(135deg,<?= $passed ? '#059669,#10b981' : '#dc2626,#ef4444' ?>);color:#fff;">
        <div class="card-body p-5">
            <div style="font-size:4rem"><?= $passed ? '🎉' : '😔' ?></div>
            <h2 class="fw-black mt-2 mb-1"><?= $passed ? 'Congratulations!' : 'Keep Practicing!' ?></h2>
            <p class="opacity-75 mb-3"><?= htmlspecialchars($result['quiz_title']) ?></p>
            <div class="display-4 fw-black"><?= (int)$result['score'] ?> / <?= (int)$result['total_marks'] ?></div>
            <div class="fs-5 mt-1 opacity-90"><?= round($percentage, 1) ?>%</div>
            <span class="badge mt-3 px-4 py-2 fs-6" style="background:rgba(255,255,255,0.25)">
                <?= $passed ? '✓ PASS' : '✗ FAIL' ?>
            </span>
        </div>
    </div>

    <!-- Question breakdown -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">📋 Question Breakdown</h5>
            <?php foreach ($breakdown as $i => $row): ?>
                <div class="mb-3 p-3 rounded-3" style="border: 1px solid <?= $row['is_selected_correct'] ? '#00d4d4' : '#dc2626' ?>; background: <?= $row['is_selected_correct'] ? 'rgba(0,212,212,0.08)' : 'rgba(220,38,38,0.08)' ?>;">
                    <div class="fw-semibold mb-2" style="color: #e2e8f0;">
                        <?= ($i + 1) ?>. <?= htmlspecialchars($row['question_text']) ?>
                    </div>
                    <div class="small">
                        <span class="me-3">
                            Your answer:
                            <span class="fw-bold" style="color: <?= $row['is_selected_correct'] ? '#00d4d4' : '#f87171' ?>;">
                                <?= $row['is_selected_correct'] ? '✓' : '✗' ?>
                                <?= htmlspecialchars($row['selected_option_text']) ?>
                            </span>
                        </span>
                        <?php if (!$row['is_selected_correct']): ?>
                            <span class="fw-bold" style="color: #00d4d4;">
                                ✓ Correct: <?= htmlspecialchars($row['correct_option_text']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2 justify-content-center flex-wrap">
        <a href="<?= BASE ?>?page=student/quizzes" class="btn btn-primary">
            <i class="bi bi-collection me-2"></i>Browse More Quizzes
        </a>
        <a href="<?= BASE ?>?page=student/my-results" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul me-2"></i>My Results History
        </a>
        <a href="<?= BASE ?>?page=leaderboard" class="btn btn-outline-warning">
            <i class="bi bi-trophy me-2"></i>Leaderboard
        </a>
    </div>

</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>