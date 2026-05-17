<?php
// views/instructor/question_manager.php
$pageTitle = 'Manage Questions';
$errors    = $errors ?? [];
require_once __DIR__ . '/../layout/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= BASE ?>?page=instructor/quizzes" class="text-muted text-decoration-none">
            <h2>Questions: <span><?= htmlspecialchars($quiz['title']) ?></span></h2>
            <span class="badge ...">...</span>
        </div>
        <div class="text-end">
            <span>...question count & marks...</span>
        </div>
    </div>
    <?php if ($quiz['status']==='published' && count($questions)===0): ?>
        <div class="alert alert-warning">...</div>
    <?php endif; ?>
</div>
<!-- Questions Table -->
    <?php if (!empty($questions)): ?>
    <div class="card mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="questions-table">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Question</th>
                        <th>Options</th>
                        <th style="width:80px">Marks</th>
                        <th style="width:160px" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $i => $q): ?>
                    <tr id="question-row-<?= $q['id'] ?>">
                        <td class="text-muted fw-bold"><?= $i + 1 ?></td>
                        <td id="qtext-display-<?= $q['id'] ?>"><?= htmlspecialchars($q['question_text']) ?></td>
                        <td id="options-display-<?= $q['id'] ?>">
                            <ul class="list-unstyled mb-0 small">
                                <?php foreach ($q['options'] as $opt): ?>
                                <li>
                                    <?= $opt['is_correct'] ? '<span class="text-success fw-bold">✔</span>' : '<span class="text-muted">○</span>' ?>
                                    <?= htmlspecialchars($opt['option_text']) ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td><?= (int)$q['marks'] ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning me-1" id="edit-btn-<?= $q['id'] ?>" onclick="startEdit(<?= $q['id'] ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteQuestion(<?= $q['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                            <button class="btn btn-sm btn-success me-1 d-none" id="save-btn-<?= $q['id'] ?>" onclick="saveQuestion(<?= $q['id'] ?>)">
                                <i class="bi bi-check-lg"></i> Save
                            </button>
                            <button class="btn btn-sm btn-secondary d-none" id="cancel-btn-<?= $q['id'] ?>" onclick="cancelEdit(<?= $q['id'] ?>)">
                                Cancel
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-secondary mb-4">No questions yet — add your first question below.</div>
    <?php endif; ?>
    <!-- Add Question Form -->
    <div class="card p-4">
        <h5 class="fw-bold mb-3">➕ Add New Question</h5>
        <form method="POST" action="<?= BASE ?>?page=instructor/questions/add&quiz_id=<?= $quiz['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                <textarea name="question_text" rows="2"
                    class="form-control <?= isset($errors['question_text'])?'is-invalid':'' ?>"
                    placeholder="Type your MCQ question here…"><?= htmlspecialchars($_POST['question_text'] ?? '') ?></textarea>
                <?php if (isset($errors['question_text'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['question_text']) ?></div>
                <?php endif; ?>
            </div>

            <?php if (isset($errors['options'])): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($errors['options']) ?></div>
            <?php endif; ?>
            <?php if (isset($errors['correct_option'])): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($errors['correct_option']) ?></div>
            <?php endif; ?>

            <p class="text-muted small mb-2">Select the radio button next to the correct answer:</p>

            <?php $optLabels = ['A','B','C','D']; ?>
            <?php for ($j = 0; $j < 4; $j++): ?>
            <div class="mb-2 d-flex align-items-center gap-2">
                <input type="radio" name="correct_option" value="<?= $j ?>" id="correct_<?= $j ?>"
                    <?= (isset($_POST['correct_option']) && $_POST['correct_option'] == $j) ? 'checked' : '' ?>>
                <label for="correct_<?= $j ?>" class="fw-bold text-primary" style="min-width:28px;"><?= $optLabels[$j] ?>.</label>
                <input type="text" name="options[<?= $j ?>]" class="form-control"
                    placeholder="Option <?= $optLabels[$j] ?>"
                    value="<?= htmlspecialchars($_POST['options'][$j] ?? '') ?>">
            </div>
            <?php endfor; ?>

            <div class="row mt-3">
                <div class="col-sm-3">
                    <label class="form-label fw-semibold">Marks <span class="text-danger">*</span></label>
                    <input type="number" name="marks" min="1"
                        class="form-control <?= isset($errors['marks'])?'is-invalid':'' ?>"
                        value="<?= htmlspecialchars($_POST['marks'] ?? '1') ?>">
                    <?php if (isset($errors['marks'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['marks']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Question</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>