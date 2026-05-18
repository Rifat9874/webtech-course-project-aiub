<?php
// views/admin/panel.php
$pageTitle = 'Admin Panel';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-black mb-1">⚙️ Admin Panel</h2>
        <p class="text-muted mb-0">Manage all registered users.</p>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-muted"><?= (int)$user['id'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php $badgeColor = match($user['role']) {
                                    'admin' => 'danger', 'instructor' => 'warning text-dark', default => 'info text-dark'
                                }; ?>
                                <span class="badge bg-<?= $badgeColor ?>"><?= ucfirst($user['role']) ?></span>
                            </td>
                            <td>
                                <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-secondary' ?>"
                                    id="status-badge-<?= $user['id'] ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Suspended' ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td class="text-center">
                                <?php if ($user['role'] !== 'admin'): ?>
                                <button class="btn btn-sm <?= $user['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                    id="toggle-btn-<?= $user['id'] ?>"
                                    onclick="toggleUser(<?= $user['id'] ?>)">
                                    <?= $user['is_active'] ? 'Suspend' : 'Activate' ?>
                                </button>
                                <?php else: ?>
                                <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function toggleUser(userId) {
    try {
        const base = BASE_URL.replace('/public/index.php', '');
        const res  = await fetch(base + '/api/users/toggle.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });
        const data = await res.json();
        if (!data.success) { alert('Error: ' + (data.message || 'Failed')); return; }

        const badge = document.getElementById('status-badge-' + userId);
        const btn   = document.getElementById('toggle-btn-' + userId);

        if (data.is_active == 1) {
            badge.textContent = 'Active';   badge.className = 'badge bg-success';
            btn.textContent   = 'Suspend';  btn.className   = 'btn btn-sm btn-outline-danger';
        } else {
            badge.textContent = 'Suspended'; badge.className = 'badge bg-secondary';
            btn.textContent   = 'Activate';  btn.className   = 'btn btn-sm btn-outline-success';
        }
    } catch(e) { alert('Network error.'); }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
