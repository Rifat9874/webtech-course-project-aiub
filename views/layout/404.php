<?php
$pageTitle = '404 Not Found';
require_once __DIR__ . '/header.php';
?>
<div class="container text-center py-5">
    <div style="font-size:6rem">🔍</div>
    <h1 class="display-3 fw-black text-danger">404</h1>
    <p class="fs-4 text-muted">Page not found.</p>
    <a href="<?= BASE ?>?page=home" class="btn btn-primary btn-lg mt-2">Go to Homepage</a>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>