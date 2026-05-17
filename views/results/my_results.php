<?php
// views/results/my_results.php — Student attempt history
$pageTitle = 'My Results';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-black mb-1">📊 My Results</h2>
        <p class="text-muted mb-0">All your completed quiz attempts.</p>
    </div>

    <?php if (empty($attempts)): ?>
        <div class="card text-center p-5">
            <div style="font-size:4rem">📭</div>
            <h4 class="mt-3 fw-bold">No results yet</h4>
            <p class="text-muted">You haven't completed any quizzes. Start one now!</p>
            <a href="<?= BASE ?>?page=student/quizzes" class="btn btn-primary d-inline-block mx-auto mt-2" style="width:fit-content;">Browse Quizzes</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quiz</th>
                            <th>Score</th>
                            <th>Duration</th>
                            <th>Date</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $i => $a):
                            $dur = strtotime($a['completed_at']) - strtotime($a['started_at']);
                            $mins = floor($dur / 60); $secs = $dur % 60;
                        ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($a['quiz_title']) ?></td>
                            <td>
                                <span class="fw-bold"><?= (int)$a['score'] ?></span>
                                <span class="text-muted">/ <?= (int)$a['total_marks'] ?></span>
                                <div class="progress mt-1" style="height:4px;width:80px;">
                                    <div class="progress-bar <?= $a['pass_fail']==='Pass'?'bg-success':'bg-danger' ?>"
                                        style="width:<?= $a['total_marks']>0 ? round($a['score']/$a['total_marks']*100) : 0 ?>%"></div>
                                </div>
                            </td>
                            <td class="text-muted small"><?= $mins ?>m <?= $secs ?>s</td>
                            <td class="text-muted small"><?= date('M j, Y', strtotime($a['completed_at'])) ?></td>
                            <td>
                                <span class="badge <?= $a['pass_fail']==='Pass' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $a['pass_fail'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
