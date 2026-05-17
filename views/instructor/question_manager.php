<?php
// views/instructor/question_manager.php
$pageTitle = 'Manage Questions';
$errors    = $errors ?? [];
require_once __DIR__ . '/../layout/header.php';
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="...">Back to My Quizzes</a>
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