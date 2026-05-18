<?php
// views/student/take_quiz.php
$pageTitle = htmlspecialchars($quiz['title']);
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">

    <!-- Quiz header + timer -->
    <div class="card mb-4" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-black mb-1"><?= htmlspecialchars($quiz['title']) ?></h3>
                <div class="opacity-75 small"><?= count($questions) ?> questions &bull; <?= (int)$quiz['total_marks'] ?> total marks</div>
            </div>
            <div class="text-center">
                <div class="fw-bold small opacity-75 mb-1">⏱ Time Remaining</div>
                <div id="timer-display"
                    data-seconds="<?= (int)$quiz['time_limit_minutes'] * 60 ?>"
                    style="font-size:2rem;font-weight:900;font-family:monospace;letter-spacing:2px;">
                    <?= str_pad($quiz['time_limit_minutes'], 2, '0', STR_PAD_LEFT) ?>:00
                </div>
            </div>
        </div>
    </div>

    <!-- Time's up banner (hidden until timer = 0) -->
    <div id="times-up-banner" class="alert alert-danger d-none fw-bold text-center fs-5 mb-4">
        ⏰ Time's up! Please submit your answers now.
    </div>

    <!-- Quiz form -->
    <form id="quiz-form">
        <input type="hidden" name="attempt_id" value="<?= (int)$attempt_id ?>">

        <?php foreach ($questions as $i => $q): ?>
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-primary rounded-circle" style="width:32px;height:32px;font-size:1rem;display:flex;align-items:center;justify-content:center;"><?= $i + 1 ?></span>
                        <h6 class="mb-0 fw-bold" style="flex:1"><?= htmlspecialchars($q['question_text']) ?></h6>
                        <span class="badge bg-light text-dark"><?= (int)$q['marks'] ?> mark<?= $q['marks'] > 1 ? 's' : '' ?></span>
                    </div>

                    <div class="d-flex flex-column gap-2 ps-2">
                        <?php
                        $optLetters = ['A', 'B', 'C', 'D'];
                        foreach ($q['options'] as $oi => $opt):
                        ?>
                            <label class="option-label d-flex align-items-center gap-3 p-3 rounded-3 border"
                                style="cursor:pointer;transition:all 0.2s;"
                                for="opt_<?= $q['id'] ?>_<?= $opt['id'] ?>">
                                <input type="radio"
                                    class="form-check-input m-0 flex-shrink-0"
                                    name="question_<?= $q['id'] ?>"
                                    id="opt_<?= $q['id'] ?>_<?= $opt['id'] ?>"
                                    value="<?= $opt['id'] ?>"
                                    style="width:20px;height:20px;">
                                <span class="badge bg-secondary me-1" style="min-width:28px;"><?= $optLetters[$oi] ?></span>
                                <span><?= htmlspecialchars($opt['option_text']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Submit -->
        <div class="card p-4 text-center mb-5" style="border:2px dashed #2a2d3e !important;">
            <p class="text-muted mb-3">Make sure you've answered all questions before submitting.</p>
            <button type="button" id="submit-btn" class="btn btn-success btn-lg px-5">
                <i class="bi bi-check-circle-fill me-2"></i>Submit Quiz
            </button>
            <div id="submit-spinner" class="d-none mt-3">
                <div class="spinner-border text-success" role="status"></div>
                <p class="text-muted mt-2">Submitting and grading…</p>
            </div>
            <div id="submit-error" class="alert alert-danger d-none mt-3"></div>
        </div>
    </form>
</div>

<style>
    .option-label:hover {
        background: rgba(0, 212, 212, 0.08);
        border-color: #00d4d4 !important;
    }

    .option-label:has(input:checked) {
        background: rgba(0, 212, 212, 0.15);
        border-color: #00d4d4 !important;
        font-weight: 600;
    }
</style>

<script>
    // ── Timer ─────────────────────────────────────────────────
    const timerEl = document.getElementById('timer-display');
    let totalSeconds = parseInt(timerEl.dataset.seconds) || 30 * 60;

    function updateTimer() {
        const m = Math.floor(totalSeconds / 60);
        const s = totalSeconds % 60;
        timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');

        if (totalSeconds <= 60) {
            timerEl.style.color = '#fca5a5'; // red tint when < 1 min
        }
        if (totalSeconds <= 0) {
            clearInterval(timerInterval);
            totalSeconds = 0;
            document.getElementById('times-up-banner').classList.remove('d-none');
            // validation bypass করে force submit
            forceSubmit();
            return;
        }

        totalSeconds--;
    }
    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);

    // ── Submit ────────────────────────────────────────────────
    document.getElementById('submit-btn').addEventListener('click', submitQuiz);


    async function forceSubmit() {
        const answers = {};
        document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
            const qid = radio.name.replace('question_', '');
            answers[qid] = radio.value;
        });

        const attempt_id = document.querySelector('input[name=attempt_id]').value;

        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-spinner').classList.remove('d-none');

        try {
            const apiUrl = "<?= str_replace('/public/index.php', '/api/quiz/submit.php', BASE) ?>";
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    attempt_id: parseInt(attempt_id),
                    answers
                })
            });
            const data = await response.json();
            if (data.success) {
                window.location.href = '<?= BASE ?>?page=result&attempt_id=' + data.attempt_id;
            } else {
                showError(data.error || 'Auto-submission failed.');
            }
        } catch (err) {
            showError('Network error during auto-submit.');
        }
    }

    async function submitQuiz() {
        // Check all questions answered
        const totalQuestions = <?= count($questions) ?>;
        const answered = document.querySelectorAll('input[type=radio]:checked').length;

        if (answered === 0) {
            showError('Please answer at least one question before submitting.');
            return;
        }

        if (answered < totalQuestions) {
            const confirmed = confirm(`You have answered ${answered} of ${totalQuestions} questions. Submit anyway?`);
            if (!confirmed) return;
        }

        // Collect answers
        const answers = {};
        document.querySelectorAll('input[type=radio]:checked').forEach(radio => {
            const qid = radio.name.replace('question_', '');
            answers[qid] = radio.value;
        });

        const attempt_id = document.querySelector('input[name=attempt_id]').value;

        // Show spinner
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-spinner').classList.remove('d-none');
        document.getElementById('submit-error').classList.add('d-none');

        try {
            // Build API URL (same folder structure)
            const apiUrl = "<?= str_replace('/public/index.php', '/api/quiz/submit.php', BASE) ?>";
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    attempt_id: parseInt(attempt_id),
                    answers
                })
            });

            const data = await response.json();

            if (data.success) {
                // Redirect to result page
                window.location.href = '<?= BASE ?>?page=result&attempt_id=' + data.attempt_id;
            } else {
                showError(data.error || 'Submission failed. Please try again.');
            }
        } catch (err) {
            showError('Network error. Please try again.');
        }
    }

    function showError(msg) {
        document.getElementById('submit-btn').disabled = false;
        document.getElementById('submit-spinner').classList.add('d-none');
        const errEl = document.getElementById('submit-error');
        errEl.textContent = msg;
        errEl.classList.remove('d-none');
    }
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>