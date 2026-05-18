<?php
// views/auth/login.php
$pageTitle = 'Login';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="text-center mb-4">
                <div style="font-size:3.5rem;">🎓</div>
                <h2 class="fw-black mt-2">Welcome back!</h2>
                <p class="text-muted">Log in to your QuizApp account</p>
            </div>

            <div class="card shadow">
                <div class="card-body p-4">

                    <?php if (!empty($justRegistered)): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            Account created! Please log in.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE ?>?page=login" novalidate>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" class="form-control border-start-0"
                                    id="email" name="email" autocomplete="off"
                                    placeholder="you@example.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" class="form-control border-start-0"
                                    id="password" name="password" autocomplete="off"
                                    placeholder="Your password"
                                    required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                            </button>
                        </div>
                    </form>

                    <p class="text-center mt-3 mb-0 small text-muted">
                        Don't have an account?
                        <a href="<?= BASE ?>?page=register" class="text-decoration-none fw-semibold">Register here</a>
                    </p>
                </div>
            </div>

            <!-- Role hint
    <div class="card mt-3" style="background:#f8fafc;border:1.5px dashed #cbd5e1 !important;">
        <div class="card-body p-3 small text-muted">
            <strong>🔑 Default admin:</strong> Create an admin user directly in phpMyAdmin by inserting a row with <code>role='admin'</code> and a hashed password.
        </div>
    </div> -->

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>