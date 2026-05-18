<?php
// views/auth/register.php
$pageTitle = 'Register';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-5 col-lg-4">

    <div class="text-center mb-4">
        <div style="font-size:3.5rem;">✨</div>
        <h2 class="fw-black mt-2">Create Account</h2>
        <p class="text-muted">Join the QuizApp platform</p>
    </div>

    <div class="card shadow">
        <div class="card-body p-4">

            <form method="POST" action="<?= BASE ?>?page=register" novalidate>

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text"
                            class="form-control border-start-0 <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            id="name" name="name"
                            placeholder="Jane Smith"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email"
                            class="form-control border-start-0 <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            id="email" name="email"
                            placeholder="you@example.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password"
                            class="form-control border-start-0 <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            id="password" name="password"
                            placeholder="At least 8 characters">
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label class="form-label fw-semibold d-block">I am a …</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="role" id="roleStudent" value="student"
                                <?= (($_POST['role'] ?? '') === 'student') ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary w-100 py-3" for="roleStudent">
                                <div style="font-size:1.8rem">🎒</div>
                                <div class="fw-bold mt-1">Student</div>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="role" id="roleInstructor" value="instructor"
                                <?= (($_POST['role'] ?? '') === 'instructor') ? 'checked' : '' ?>>
                            <label class="btn btn-outline-success w-100 py-3" for="roleInstructor">
                                <div style="font-size:1.8rem">🎓</div>
                                <div class="fw-bold mt-1">Instructor</div>
                            </label>
                        </div>
                    </div>
                    <?php if (isset($errors['role'])): ?>
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($errors['role']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg py-2">
                        <i class="bi bi-person-check-fill me-2"></i>Create Account
                    </button>
                </div>
            </form>

            <p class="text-center mt-3 mb-0 small text-muted">
                Already have an account?
                <a href="<?= BASE ?>?page=login" class="text-decoration-none fw-semibold">Log in</a>
            </p>
        </div>
    </div>

</div>
</div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
