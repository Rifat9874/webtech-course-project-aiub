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
<script>
const originalData = {};
<?php foreach ($questions as $q): ?>
originalData[<?= $q['id'] ?>] = {
    text: <?= json_encode($q['question_text']) ?>,
    options: <?= json_encode(array_values($q['options'])) ?>
};
<?php endforeach; ?>

function startEdit(qid) {
    const data = originalData[qid];
    document.getElementById('qtext-display-' + qid).innerHTML =
        `<textarea class="form-control form-control-sm" id="edit-qtext-${qid}" rows="2">${escHtml(data.text)}</textarea>`;

    let optHtml = '<div class="small">';
    data.options.forEach(opt => {
        optHtml += `<div class="d-flex align-items-center gap-2 mb-1">
            <input type="radio" name="correct-${qid}" value="${opt.id}" id="radio-${opt.id}" ${opt.is_correct?'checked':''}>
            <input type="text" class="form-control form-control-sm" id="edit-opt-${opt.id}" value="${escHtml(opt.option_text)}">
        </div>`;
    });
    optHtml += '</div>';
    document.getElementById('options-display-' + qid).innerHTML = optHtml;

    document.getElementById('edit-btn-'   + qid).classList.add('d-none');
    document.getElementById('save-btn-'   + qid).classList.remove('d-none');
    document.getElementById('cancel-btn-' + qid).classList.remove('d-none');
}

function cancelEdit(qid) {
    const data = originalData[qid];
    document.getElementById('qtext-display-' + qid).textContent = data.text;

    let optHtml = '<ul class="list-unstyled mb-0 small">';
    data.options.forEach(opt => {
        const icon = opt.is_correct ? '<span class="text-success fw-bold">✔</span>' : '<span class="text-muted">○</span>';
        optHtml += `<li>${icon} ${escHtml(opt.option_text)}</li>`;
    });
    optHtml += '</ul>';
    document.getElementById('options-display-' + qid).innerHTML = optHtml;

    document.getElementById('edit-btn-'   + qid).classList.remove('d-none');
    document.getElementById('save-btn-'   + qid).classList.add('d-none');
    document.getElementById('cancel-btn-' + qid).classList.add('d-none');
}

async function saveQuestion(qid) {
    const data    = originalData[qid];
    const newText = document.getElementById('edit-qtext-' + qid).value.trim();
    if (!newText) { alert('Question text cannot be empty.'); return; }

    const options = [];
    let correctOptionId = null;
    for (const opt of data.options) {
        const textInput  = document.getElementById('edit-opt-'  + opt.id);
        const radioInput = document.getElementById('radio-' + opt.id);
        if (!textInput.value.trim()) { alert('All options must be filled in.'); return; }
        options.push({ id: opt.id, text: textInput.value.trim() });
        if (radioInput && radioInput.checked) correctOptionId = opt.id;
    }
    if (!correctOptionId) { alert('Please select the correct answer.'); return; }

    const saveBtn = document.getElementById('save-btn-' + qid);
    saveBtn.disabled = true; saveBtn.textContent = 'Saving…';

    try {
        const base = BASE_URL.replace('/public/index.php', '');
        const res  = await fetch(base + '/api/questions/update.php', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question_id: qid, question_text: newText, options, correct_option_id: correctOptionId })
        });
        const result = await res.json();
        if (result.success) {
            originalData[qid].text = newText;
            originalData[qid].options = data.options.map(opt => {
                const upd = options.find(o => o.id === opt.id);
                return { id: opt.id, option_text: upd ? upd.text : opt.option_text, is_correct: (opt.id === correctOptionId) ? 1 : 0 };
            });
            cancelEdit(qid);
        } else { alert('Error: ' + (result.error || 'Could not save.')); }
    } catch(e) { alert('Network error. Please try again.'); }
    finally { saveBtn.disabled = false; saveBtn.innerHTML = '<i class="bi bi-check-lg"></i> Save'; }
}

async function deleteQuestion(qid) {
    if (!confirm('Delete this question? This cannot be undone.')) return;
    try {
        const base = BASE_URL.replace('/public/index.php', '');
        const res  = await fetch(base + '/api/questions/delete.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question_id: qid })
        });
        const result = await res.json();
        if (result.success) {
            document.getElementById('question-row-' + qid)?.remove();
        } else { alert('Error: ' + (result.error || 'Could not delete.')); }
    } catch(e) { alert('Network error. Please try again.'); }
}

function escHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}
</script>