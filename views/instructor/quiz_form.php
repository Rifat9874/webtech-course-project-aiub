<?php
// views/instructor/quiz_form.php — Create or Edit quiz
$isEdit    = isset($quiz) && $quiz !== null;
$pageTitle = $isEdit ? 'Edit Quiz' : 'Create Quiz';
require_once __DIR__ . '/../layout/header.php';
?>
<div class="container py-4" style="max-width:640px;">
    <div class="mb-4">
        <a href="<?= BASE ?>?page=instructor/quizzes" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to My Quizzes
        </a>
        <h2 class="fw-black mt-2"><?= $isEdit ? '✏️ Edit Quiz' : '➕ Create New Quiz' ?></h2>
    </div>

    <div class="card p-4">
        <?php $action = $isEdit
            ? BASE . '?page=instructor/quizzes/edit&id=' . $quiz['id']
            : BASE . '?page=instructor/quizzes/create'; ?>
        <form method="POST" action="<?= $action ?>">

            

            <!-- Total marks: read-only after creation -->
            <?php if ($isEdit): ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Total Marks</label>
                <input type="text" class="form-control"
                    value="<?= (int)$quiz['total_marks'] ?> marks (auto-calculated from questions)" readonly>
            </div>
            <?php endif; ?>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle-fill me-2"></i><?= $isEdit ? 'Save Changes' : 'Create Quiz' ?>
                </button>
                <a href="<?= BASE ?>?page=instructor/quizzes" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php if ($isEdit): ?>
    <div class="card mt-3 p-3">
        <div class="small text-muted">
            <i class="bi bi-info-circle me-1 text-success"></i>
            <strong>Next step:</strong> Go to
            <a href="<?= BASE ?>?page=instructor/questions&quiz_id=<?= $quiz['id'] ?>">Manage Questions</a>
            to add questions, then publish the quiz.
        </div>
    </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>