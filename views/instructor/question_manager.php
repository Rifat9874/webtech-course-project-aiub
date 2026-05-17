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