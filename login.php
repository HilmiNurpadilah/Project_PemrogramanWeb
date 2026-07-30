<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['_csrf'] ?? '';

    if (!verify_csrf($csrf)) {
        $error = 'Token CSRF tidak valid.';
    } else {
        $stmt = $mysqli->prepare('SELECT id_user, username, password, role, status FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'aktif') {
                $error = 'Akun tidak aktif.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id_user' => $user['id_user'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
                header('Location: index.php');
                exit;
            }
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

require 'includes/header.php';
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h3 class="mb-3">Login</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
