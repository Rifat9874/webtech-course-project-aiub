<?php
// views/layout/header.php — Top of every page
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$pageTitle    = $pageTitle ?? 'QuizApp';
$isLoggedIn   = isset($_SESSION['user_id']);
$loggedInName = $_SESSION['name'] ?? 'User';
$loggedInRole = $_SESSION['role'] ?? '';

$homeUrl = BASE . '?page=home';
if ($isLoggedIn) {
    $roleHome = ['student' => 'student/home', 'instructor' => 'instructor/home', 'admin' => 'admin/panel'];
    $homeUrl  = BASE . '?page=' . ($roleHome[$loggedInRole] ?? 'home');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | QuizApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { min-height:100vh; background:#0f1117; color:#e2e8f0; font-family:'Segoe UI',system-ui,sans-serif; }
        .navbar { background:#1a1d2e !important; border-bottom:1px solid #2a2d3e; }
        .navbar-brand { font-weight:800; font-size:1.5rem; color:#fff !important; }
        .navbar-brand i { color:#00d4d4; }
        .nav-link { color:rgba(255,255,255,0.75) !important; font-weight:500; padding:0.5rem 0.8rem !important; border-radius:8px; transition:all 0.2s; }
        .nav-link:hover { color:#00d4d4 !important; background:rgba(0,212,212,0.1); }
        .btn-nav-register { background:rgba(0,212,212,0.15); border:1.5px solid #00d4d4; color:#00d4d4 !important; border-radius:8px; font-weight:600; }
        .btn-nav-register:hover { background:#00d4d4; color:#0f1117 !important; }
        .avatar-circle { width:32px; height:32px; background:rgba(0,212,212,0.2); border:2px solid #00d4d4; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:700; color:#00d4d4; }
        .dropdown-menu { background:#1a1d2e; border:1px solid #2a2d3e; border-radius:12px; padding:0.5rem; }
        .dropdown-item { border-radius:8px; padding:0.5rem 1rem; color:#e2e8f0; }
        .dropdown-item:hover { background:#2a2d3e; color:#00d4d4; }
        .card { border-radius:16px !important; border:1px solid #2a2d3e !important; background:#1a1d2e !important; color:#e2e8f0 !important; }
        .btn { border-radius:10px; font-weight:600; }
        .btn-primary { background:#00d4d4 !important; border:none !important; color:#0f1117 !important; }
        .btn-primary:hover { background:#00a8a8 !important; }
        .btn-success { background:#00d4d4 !important; border:none !important; color:#0f1117 !important; }
        .form-control, .form-select { border-radius:10px; border:1.5px solid #2a2d3e; background:#0f1117 !important; color:#e2e8f0 !important; }
        .form-control:focus, .form-select:focus { border-color:#00d4d4; box-shadow:0 0 0 3px rgba(0,212,212,0.15); }
        .form-label { color:#94a3b8; }
        .table { color:#e2e8f0; border-color:#2a2d3e; }
        .table thead th { background:#1a1d2e; border-color:#2a2d3e; color:#00d4d4; }
        .table td, .table th { border-color:#2a2d3e; }
        footer { background:#1a1d2e !important; border-top:1px solid #2a2d3e !important; color:#94a3b8; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE ?>?page=home"><i class="bi bi-mortarboard-fill me-2"></i>QuizApp</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <?php if ($isLoggedIn): ?>
                    <?php if ($loggedInRole === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=student/home"><i class="bi bi-house-fill me-1"></i>Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=student/quizzes"><i class="bi bi-journal-text me-1"></i>Browse Quizzes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=student/my-results"><i class="bi bi-graph-up me-1"></i>My Results</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=leaderboard"><i class="bi bi-trophy me-1"></i>Leaderboard</a></li>
                    <?php elseif ($loggedInRole === 'instructor'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=instructor/home"><i class="bi bi-house-fill me-1"></i>Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=instructor/quizzes"><i class="bi bi-collection me-1"></i>My Quizzes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=instructor/analytics"><i class="bi bi-bar-chart-fill me-1"></i>Analytics</a></li>
                    <?php elseif ($loggedInRole === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=admin/panel"><i class="bi bi-gear-fill me-1"></i>Admin Panel</a></li>
                    <?php endif; ?>
                    <li class="nav-item dropdown ms-1">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle"><?= strtoupper(substr($loggedInName, 0, 1)) ?></div>
                            <?= htmlspecialchars($loggedInName) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small px-3 py-2">Signed in as <span class="badge ms-1 <?= $loggedInRole==='admin'?'bg-danger':($loggedInRole==='instructor'?'bg-warning text-dark':'bg-success') ?>"><?= ucfirst($loggedInRole) ?></span></span></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE ?>?page=logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=leaderboard"><i class="bi bi-trophy me-1"></i>Leaderboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE ?>?page=login"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                    <li class="nav-item"><a class="btn btn-nav-register btn-sm px-3 py-2 ms-1" href="<?= BASE ?>?page=register"><i class="bi bi-person-plus-fill me-1"></i>Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['flash_message'])): ?>
<div class="container mt-3">
    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['flash_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>const BASE_URL = "<?= BASE ?>";</script>
<main>