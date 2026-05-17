<?php
// views/results/analytics.php
$pageTitle = 'Instructor Analytics';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-black mb-1">📊 Instructor Analytics</h2>
        <p class="text-muted mb-0">Select a quiz to see detailed student performance.</p>
    </div>

    <!-- Quiz selector -->
    <div class="card p-4 mb-4">
        <form method="GET" action="<?= BASE ?>">
            <input type="hidden" name="page" value="instructor/analytics">
            <div class="row align-items-end g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Select Quiz</label>
                    <select name="quiz_id" class="form-select">
                        <option value="">— Choose a quiz —</option>
                        <?php foreach ($quizzes as $q): ?>
                            <option value="<?= $q['id'] ?>" <?= $selectedQuizId == $q['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($q['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-graph-up me-2"></i>View Analytics
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($selectedQuizId && !empty($attempts)): ?>

    <!-- Stats summary -->
    <?php if ($stats): ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="text-muted small">Average Score</div>
                <div class="fw-black fs-3 text-primary"><?= $stats['avg_score'] ?? '–' ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="text-muted small">Highest Score</div>
                <div class="fw-black fs-3 text-success"><?= $stats['max_score'] ?? '–' ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="text-muted small">Lowest Score</div>
                <div class="fw-black fs-3 text-danger"><?= $stats['min_score'] ?? '–' ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="text-muted small">Pass Rate</div>
                <div class="fw-black fs-3 text-warning"><?= $stats['pass_rate'] ?? 0 ?>%</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Attempts table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Score</th>
                            <th>Duration</th>
                            <th>Date</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $i => $a):
                            $dur = $a['duration_seconds'] ?? 0;
                            $mins = floor($dur / 60); $secs = $dur % 60;
                        ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($a['student_name']) ?></td>
                            <td>
                                <span class="fw-bold"><?= (int)$a['score'] ?></span>
                                <span class="text-muted">/ <?= (int)$a['total_marks'] ?></span>
                            </td>
                            <td class="text-muted small"><?= $mins ?>m <?= $secs ?>s</td>
                            <td class="text-muted small"><?= date('M j, Y', strtotime($a['started_at'])) ?></td>
                            <td>
                                <span class="badge <?= $a['pass_fail']==='Pass'?'bg-success':'bg-danger' ?>">
                                    <?= $a['pass_fail'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($selectedQuizId): ?>
        <div class="card text-center p-5">
            <div style="font-size:3rem">📭</div>
            <h5 class="mt-3">No completed attempts yet for this quiz.</h5>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
