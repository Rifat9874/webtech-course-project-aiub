<?php
// views/instructor/quiz_list.php
$pageTitle = 'My Quizzes';
require_once __DIR__ . '/../layout/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black mb-1">📋 My Quizzes</h2>
            <p class="text-muted mb-0">Create and manage your quizzes.</p>
        </div>
        <a href="<?= BASE ?>?page=instructor/quizzes/create" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-2"></i>New Quiz
        </a>
    </div>

    <?php if (empty($quizzes)): ?>
        <div class="card text-center p-5">
            <div style="font-size:4rem">📭</div>
            <h4 class="mt-3 fw-bold">No quizzes yet</h4>
            <p class="text-muted">Create your first quiz to get started.</p>
            <a href="<?= BASE ?>?page=instructor/quizzes/create" class="btn btn-primary d-inline-block mx-auto mt-2" style="width:fit-content;">
                <i class="bi bi-plus me-1"></i>Create Quiz
            </a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Total Marks</th>
                            <th>Time Limit</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($quiz['title']) ?></td>
                            <td><?= (int)$quiz['total_marks'] ?></td>
                            <td><?= (int)$quiz['time_limit_minutes'] ?> min</td>
                            <td>
                                <span class="badge <?= $quiz['status']==='published'?'bg-success':'bg-secondary' ?>"
                                    id="status-badge-<?= $quiz['id'] ?>">
                                    <?= $quiz['status']==='published'?'● Published':'○ Draft' ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('M j, Y', strtotime($quiz['created_at'])) ?></td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="<?= BASE ?>?page=instructor/questions&quiz_id=<?= $quiz['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-list-ol"></i> Questions
                                    </a>
                                    <a href="<?= BASE ?>?page=instructor/quizzes/edit&id=<?= $quiz['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm <?= $quiz['status']==='published'?'btn-warning':'btn-success' ?>"
                                        id="toggle-btn-<?= $quiz['id'] ?>"
                                        onclick="toggleQuiz(<?= $quiz['id'] ?>)">
                                        <?= $quiz['status']==='published'?'Unpublish':'Publish' ?>
                                    </button>
                                    <form method="POST" action="<?= BASE ?>?page=instructor/quizzes/delete"
                                        onsubmit="return confirm('Delete this quiz and all its questions?')">
                                        <input type="hidden" name="id" value="<?= $quiz['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
async function toggleQuiz(quizId) {
    try {
        const base = BASE_URL.replace('/public/index.php', '');
        const res  = await fetch(base + '/api/quizzes/toggle.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quiz_id: quizId })
        });
        const data = await res.json();
        if (!data.success) { alert(data.error || 'Error'); return; }

        const badge = document.getElementById('status-badge-' + quizId);
        const btn   = document.getElementById('toggle-btn-'  + quizId);

        if (data.new_status === 'published') {
            badge.textContent = '● Published'; badge.className = 'badge bg-success';
            btn.textContent   = 'Unpublish';   btn.className   = 'btn btn-sm btn-warning';
        } else {
            badge.textContent = '○ Draft';  badge.className = 'badge bg-secondary';
            btn.textContent   = 'Publish';  btn.className   = 'btn btn-sm btn-success';
        }
    } catch(e) { alert('Network error. Please refresh and try again.'); }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>