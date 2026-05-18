<?php // views/layout/footer.php 
?>
</main>

<footer class="mt-5 py-4" style="border-top: 1px solid #2a2d3e; min-height: 180px;">
    <div class="container">
        <div class="row gy-3 align-items-start">

            <!-- Left: Brand -->
            <div class="col-md-4">
                <div class="fw-bold mb-1" style="color:#00d4d4;">
                    <i class="bi bi-mortarboard-fill me-1"></i>QuizApp
                </div>
                <div class="small text-muted">Web Technologies Lab Project</div>
            </div>

            <!-- Middle: Nav links -->
            <div class="col-md-4 text-md-center">
                <div class="small fw-semibold text-muted mb-2">NAVIGATE</div>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= BASE ?>?page=home" class="text-muted text-decoration-none small">Home</a>
                    <a href="<?= BASE ?>?page=leaderboard" class="text-muted text-decoration-none small">Leaderboard</a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE ?>?page=login" class="text-muted text-decoration-none small">Login</a>
                        <a href="<?= BASE ?>?page=register" class="text-muted text-decoration-none small">Register</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Legal -->
            <div class="col-md-4 text-md-end">
                <div class="small fw-semibold text-muted mb-2">LEGAL</div>
                <div class="d-flex flex-column gap-1 align-items-md-end">
                    <a href="#" class="text-muted text-decoration-none small">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none small">Terms of Service</a>
                </div>
            </div>

        </div>

        <!-- Bottom copyright -->
        <div class="text-center mt-3 pt-2" style="border-top: 1px solid #2a2d3e;">
            <small class="text-muted">
                &copy; <?= date('Y') ?> American International University Of Bangladesh.<br>
                All rights reserved. Developed by AIUB | Contact: aiubedu@gmail.com
            </small>
        </div>
    </div>
</footer>
<?php if (!empty($extraJs)) echo $extraJs; ?>
</body>

</html>