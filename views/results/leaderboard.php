<?php
// views/results/leaderboard.php — Public leaderboard
$pageTitle = 'Leaderboard';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4" style="max-width:700px;">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <h2 class="fw-black mb-0">🏆 Top 10 Students</h2>
        <span class="badge bg-secondary fs-6" id="countdown-badge">
            Refreshing in <span id="countdown">30</span>s
        </span>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:80px">Rank</th>
                        <th>Student Name</th>
                        <th style="width:130px">Quizzes Taken</th>
                        <th style="width:130px">Total Score</th>
                    </tr>
                </thead>
                <tbody id="leaderboard-body">
                    <?php if (!empty($leaders)): ?>
                        <?php
                        $medals = ['🥇','🥈','🥉'];
                        $rowClasses = ['','','',''];
                        foreach ($leaders as $rank => $student):
                            $bg = match($rank) { 0=>'#fff8dc', 1=>'#f5f5f5', 2=>'#fdf0e0', default=>'' };
                        ?>
                        <tr style="<?= $bg ? "background:$bg" : '' ?>">
                            <td class="fw-bold">
                                <?= isset($medals[$rank]) ? $medals[$rank].' '.($rank+1) : ($rank+1) ?>
                            </td>
                            <td class="fw-semibold"><?= htmlspecialchars($student['name']) ?></td>
                            <td class="text-muted"><?= (int)$student['quizzes_taken'] ?></td>
                            <td class="fw-black text-primary fs-5"><?= (int)$student['total_score'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <div style="font-size:3rem">🏅</div>
                                <div class="mt-2">No scores yet. Be the first!</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
            <a href="<?= BASE ?>?page=student/quizzes" class="btn btn-primary">Take a Quiz</a>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE ?>?page=register" class="btn btn-primary">Join Now</a>
        <?php endif; ?>
    </div>
</div>

<script>
const REFRESH_SECS = 30;
const countdownEl  = document.getElementById('countdown');
const tbodyEl      = document.getElementById('leaderboard-body');
const medals       = ['🥇','🥈','🥉'];
let secondsLeft    = REFRESH_SECS;

setInterval(() => {
    secondsLeft--;
    countdownEl.textContent = secondsLeft;
    if (secondsLeft <= 0) {
        refreshLeaderboard();
        secondsLeft = REFRESH_SECS;
    }
}, 1000);

async function refreshLeaderboard() {
    try {
        const base = BASE_URL.replace('/public/index.php', '');
        const res  = await fetch(base + '/api/leaderboard.php');
        const data = await res.json();

        if (!data || data.length === 0) {
            tbodyEl.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No scores yet.</td></tr>';
            return;
        }
        const bgs = ['#fff8dc','#f5f5f5','#fdf0e0'];
        tbodyEl.innerHTML = data.map((s, i) => {
            const medal  = medals[i] ? medals[i]+' ' : '';
            const bg     = bgs[i] ? `background:${bgs[i]}` : '';
            return `<tr style="${bg}">
                <td class="fw-bold">${medal}${s.rank}</td>
                <td class="fw-semibold">${esc(s.name)}</td>
                <td class="text-muted">${s.quizzes_taken}</td>
                <td class="fw-black text-primary fs-5">${s.total_score}</td>
            </tr>`;
        }).join('');
    } catch(e) {
        console.error('Leaderboard refresh failed:', e);
    }
}

function esc(t) {
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
